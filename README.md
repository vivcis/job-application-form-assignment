# Job Application Form

A web-based job application form built with HTML, CSS, PHP, and MySQL. Part of the MWD-770 Web Technologies course assessment.

## Features

- Responsive HTML form with client-side validation
- PHP backend with prepared statements (SQL injection protection)
- File upload handling for resumes (PDF, DOC, DOCX)
- MySQL database with proper schema
- Docker Compose for one-command setup
- phpMyAdmin for database management

## Quick Start (Docker)

```bash
# Clone the repo
git clone https://github.com/vivcis/job-application-form-assignment.git
cd job-application-form

# Build and start everything
docker compose up -d --build

# Open in browser
open http://localhost:8080
```

### Services

| Service      | URL                    | Description          |
|-------------|------------------------|----------------------|
| Web App     | http://localhost:8080   | The application form |
| phpMyAdmin  | http://localhost:8081   | Database admin UI    |
| MySQL       | localhost:3307         | Database (direct)    |

### Default Credentials

- **MySQL root password:** `rootpassword`
- **Database name:** `job_applications_db`

## Manual Setup (without Docker)

### Prerequisites

- PHP 8.0+
- MySQL 8.0+
- Apache or Nginx with PHP support

### Steps

1. Create the database and table:
   ```bash
   mysql -u root -p < schema.sql
   ```

2. Update database credentials in `saveRecord.php` (or set environment variables):
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=your_password
   DB_NAME=job_applications_db
   ```

3. Ensure the `uploads/` directory is writable:
   ```bash
   chmod 755 uploads/
   ```

4. Serve with PHP's built-in server:
   ```bash
   php -S localhost:8080
   ```

5. Open http://localhost:8080 in your browser.

## Project Structure

```
job-application-form/
├── index.html            # Main application form
├── success.html          # Success page after submission
├── saveRecord.php        # PHP backend (form processing + DB insert)
├── schema.sql            # MySQL database schema
├── Dockerfile            # PHP + MySQLi extension
├── docker-compose.yml    # Docker Compose configuration
├── uploads/              # Resume uploads directory
│   └── .gitkeep
├── .gitignore
└── README.md
```

## Tech Stack

- **Frontend:** HTML5, CSS3 (responsive, no frameworks)
- **Backend:** PHP 8.2
- **Database:** MySQL 8.0
- **Containerisation:** Docker & Docker Compose

## License

MIT
