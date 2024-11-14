<?php
// Include external files
require_once 'db.php';
require_once 'gradingFunctions.php';

// Main code to handle form and display final grades
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDatabaseConnection();
    $student_id = $_POST['student_id'];
    $grades = [
        'homework1' => $_POST['homework1'],
        'homework2' => $_POST['homework2'],
        'homework3' => $_POST['homework3'],
        'homework4' => $_POST['homework4'],
        'homework5' => $_POST['homework5'],
        'quiz1' => $_POST['quiz1'],
        'quiz2' => $_POST['quiz2'],
        'quiz3' => $_POST['quiz3'],
        'quiz4' => $_POST['quiz4'],
        'quiz5' => $_POST['quiz5'],
        'midterm' => $_POST['midterm'],
        'final_project' => $_POST['final_project']
    ];
    
    saveGrades($conn, $student_id, $grades);
    
    // Calculate and display final grades
    $students = getStudents($conn);
    echo "<h2>Final Grades</h2>";
    if ($students->num_rows > 0) {
        echo "<table><tr><th>Student</th><th>Final Grade</th></tr>";
        while($row = $students->fetch_assoc()) {
            $final_grade = calculateFinalGrade($row);
            echo "<tr><td>" . htmlspecialchars($row['name']) . "</td><td>" . $final_grade . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No students available to display.";
    }
    
    $conn->close();
}
?>
