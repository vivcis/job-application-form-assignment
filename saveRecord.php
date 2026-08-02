<?php
/**
 * saveRecord.php
 *
 * Handles job application form submissions.
 * Receives POST data, handles file upload, and inserts into MySQL database.
 */

// ─── Configuration ──────────────────────────────────────────────────────────
$servername = getenv('DB_HOST') ?: 'localhost';
$username   = getenv('DB_USER') ?: 'root';
$password   = getenv('DB_PASS') ?: '';
$dbname     = getenv('DB_NAME') ?: 'job_applications_db';
$port       = getenv('DB_PORT') ?: 3306;

// ─── Database Connection ────────────────────────────────────────────────────
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

$conn->set_charset('utf8mb4');

// ─── Validate Request Method ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]));
}

// ─── Collect and Sanitize Form Data ─────────────────────────────────────────
$required_fields = [
    'applicant_name', 'ssn', 'gender', 'address', 'phone',
    'dob', 'country_residence', 'country_birth',
    'county_residence', 'county_birth', 'email', 'job_type'
];

$data = [];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        die(json_encode([
            'success' => false,
            'message' => "Missing required field: $field"
        ]));
    }
    $data[$field] = trim($_POST[$field]);
}

// ─── Validate Email ─────────────────────────────────────────────────────────
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode([
        'success' => false,
        'message' => 'Invalid email address.'
    ]));
}

// ─── Handle Resume File Upload ──────────────────────────────────────────────
$resume_name = '';
$upload_dir  = __DIR__ . '/uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    // Validate file type
    $allowed_types = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    $file_type = mime_content_type($_FILES['resume']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        http_response_code(400);
        die(json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only PDF, DOC, and DOCX are allowed.'
        ]));
    }

    // Validate file size (max 5MB)
    if ($_FILES['resume']['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        die(json_encode([
            'success' => false,
            'message' => 'File too large. Maximum size is 5MB.'
        ]));
    }

    // Generate unique filename
    $ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
    $resume_name = uniqid('resume_', true) . '.' . $ext;
    $target_file = $upload_dir . $resume_name;

    if (!move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Failed to upload resume.'
        ]));
    }
} else {
    http_response_code(400);
    die(json_encode([
        'success' => false,
        'message' => 'Resume file is required.'
    ]));
}

// ─── Insert into Database (Prepared Statement) ─────────────────────────────
$sql = "INSERT INTO job_applicants (
    applicant_name, ssn, gender, address, phone,
    resume_file, dob, country_residence, country_birth,
    county_residence, county_birth, email, job_type
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Failed to prepare statement: ' . $conn->error
    ]));
}

$stmt->bind_param(
    'sssssssssssss',
    $data['applicant_name'],
    $data['ssn'],
    $data['gender'],
    $data['address'],
    $data['phone'],
    $resume_name,
    $data['dob'],
    $data['country_residence'],
    $data['country_birth'],
    $data['county_residence'],
    $data['county_birth'],
    $data['email'],
    $data['job_type']
);

if ($stmt->execute()) {
    $response = [
        'success' => true,
        'message' => 'Application submitted successfully!',
        'id'      => $stmt->insert_id
    ];
    // Redirect back with success message
    header('Location: success.html');
    exit;
} else {
    http_response_code(500);
    $response = [
        'success' => false,
        'message' => 'Failed to save application: ' . $stmt->error
    ];
    echo json_encode($response);
}

$stmt->close();
$conn->close();
?>
