DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS student_subjects;
DROP TABLE IF EXISTS students;

CREATE TABLE students (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE student_subjects (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT           NOT NULL,
    subject    VARCHAR(100)  NOT NULL,
    grade      VARCHAR(10)   NOT NULL,
    marks      DECIMAL(5,2)  NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Sample data
INSERT INTO students (name, email) VALUES
('Aarav Sharma',  'aarav@example.com'),
('Priya Patel',   'priya@example.com'),
('Rohan Mehta',   'rohan@example.com');

INSERT INTO student_subjects (student_id, subject, grade, marks) VALUES
(1, 'Mathematics',      'A',  92.5),
(1, 'Science',          'B+', 84.0),
(1, 'English',          'A+', 96.0),
(2, 'Computer Science', 'A+', 97.3),
(2, 'Mathematics',      'B',  76.5),
(3, 'History',          'B+', 85.0),
(3, 'Physics',          'A',  91.2),
(3, 'Chemistry',        'B',  78.4);
