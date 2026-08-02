-- ============================================
-- Job Application Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS job_applications_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE job_applications_db;

-- Drop table if it exists (for clean setup)
DROP TABLE IF EXISTS job_applicants;

CREATE TABLE job_applicants (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    applicant_name     VARCHAR(100)  NOT NULL,
    ssn                VARCHAR(11)   NOT NULL,
    gender             VARCHAR(20)   NOT NULL,
    address            TEXT          NOT NULL,
    phone              VARCHAR(20)   NOT NULL,
    resume_file        VARCHAR(255)  NOT NULL,
    dob                DATE          NOT NULL,
    country_residence  VARCHAR(100)  NOT NULL,
    country_birth      VARCHAR(100)  NOT NULL,
    county_residence   VARCHAR(100)  NOT NULL,
    county_birth       VARCHAR(100)  NOT NULL,
    email              VARCHAR(150)  NOT NULL,
    job_type           VARCHAR(50)   NOT NULL,
    submitted_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_job_type (job_type),
    INDEX idx_submitted_at (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
