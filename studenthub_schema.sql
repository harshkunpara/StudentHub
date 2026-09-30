CREATE DATABASE IF NOT EXISTS studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;

DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS courses;

CREATE TABLE courses (
    course_id INT NOT NULL AUTO_INCREMENT,
    course_name VARCHAR(100) NOT NULL UNIQUE,
    PRIMARY KEY (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO courses (course_name) VALUES
('Information Technology'),
('Computer Engineering'),
('Mechanical Engineering'),
('Civil Engineering');

CREATE TABLE students (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mobile VARCHAR(20) NOT NULL,
    enrollment_number VARCHAR(30) NOT NULL UNIQUE,
    course_id INT NOT NULL,
    semester TINYINT UNSIGNED NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    gender VARCHAR(30) NULL,
    city VARCHAR(100) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_students_course
        FOREIGN KEY (course_id) REFERENCES courses(course_id)
        ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE events (
    event_id INT NOT NULL AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NULL,
    PRIMARY KEY (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO events (title, event_date, location, category, description) VALUES
('Tech Fest 2026', '2026-09-20', 'Main Auditorium', 'Technical', 'Student technology festival.'),
('Hackathon 2026', '2026-10-10', 'Innovation Lab', 'Hackathon', 'Build and present a working project.'),
('AI & Innovation Workshop', '2026-10-18', 'Seminar Hall', 'Workshop', 'Hands-on session on AI and innovation.');

CREATE TABLE registrations (
    reg_id INT NOT NULL AUTO_INCREMENT,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registered_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (reg_id),
    UNIQUE KEY uq_student_event (student_id, event_id),
    CONSTRAINT fk_reg_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reg_event
        FOREIGN KEY (event_id) REFERENCES events(event_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
