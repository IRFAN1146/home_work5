-- create_database.sql

CREATE DATABASE grading_tool;
USE grading_tool;

-- Students Table
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- Grades Table
CREATE TABLE grades (
    grade_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    homework1 INT,
    homework2 INT,
    homework3 INT,
    homework4 INT,
    homework5 INT,
    quiz1 INT,
    quiz2 INT,
    quiz3 INT,
    quiz4 INT,
    quiz5 INT,
    midterm INT,
    final_project INT,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Insert Sample Students
INSERT INTO students (name) VALUES ('John Doe'), ('Jane Smith'), ('Alice Johnson');
