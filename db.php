<?php
// db.php - Handles database connections

function getDatabaseConnection() {
    $servername = "localhost";
    $username = "root";  // Replace with your MySQL username
    $password = "";      // Replace with your MySQL password
    $dbname = "grading_tool"; // Replace with your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function getStudents($conn) {
    return $conn->query("SELECT student_id, name FROM students");
}

function saveGrades($conn, $student_id, $grades) {
    $stmt = $conn->prepare("INSERT INTO grades (student_id, homework1, homework2, homework3, homework4, homework5, quiz1, quiz2, quiz3, quiz4, quiz5, midterm, final_project) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiiiiiiiiiiii', $student_id, $grades['homework1'], $grades['homework2'], $grades['homework3'], $grades['homework4'], $grades['homework5'], 
                     $grades['quiz1'], $grades['quiz2'], $grades['quiz3'], $grades['quiz4'], $grades['quiz5'], 
                     $grades['midterm'], $grades['final_project']);
    $stmt->execute();
    $stmt->close();
}
?>
