<?php
// Database connection settings
$servername = "localhost";
$username = "root";      // Replace with your MySQL username
$password = "";          // Replace with your MySQL password
$dbname = "grading_tool"; // Replace with your database name

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to calculate the final grade
function calculateFinalGrade($grades) {
    $homework_avg = array_sum(array_slice($grades, 0, 5)) / 5;
    $quiz_scores = array_slice($grades, 5, 5);
    sort($quiz_scores);
    array_shift($quiz_scores);
    $quiz_avg = array_sum($quiz_scores) / 4;
    $midterm = $grades['midterm'];
    $final_project = $grades['final_project'];
    $final_grade = ($homework_avg * 0.2) + ($quiz_avg * 0.1) + ($midterm * 0.3) + ($final_project * 0.4);
    return round($final_grade);
}

// Retrieve students for the dropdown
$students = $conn->query("SELECT student_id, name FROM students");

// Save grades if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $homework1 = $_POST['homework1'];
    $homework2 = $_POST['homework2'];
    $homework3 = $_POST['homework3'];
    $homework4 = $_POST['homework4'];
    $homework5 = $_POST['homework5'];
    $quiz1 = $_POST['quiz1'];
    $quiz2 = $_POST['quiz2'];
    $quiz3 = $_POST['quiz3'];
    $quiz4 = $_POST['quiz4'];
    $quiz5 = $_POST['quiz5'];
    $midterm = $_POST['midterm'];
    $final_project = $_POST['final_project'];

    $stmt = $conn->prepare("INSERT INTO grades (student_id, homework1, homework2, homework3, homework4, homework5, 
                            quiz1, quiz2, quiz3, quiz4, quiz5, midterm, final_project) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiiiiiiiiii", $student_id, $homework1, $homework2, $homework3, $homework4, $homework5,
                      $quiz1, $quiz2, $quiz3, $quiz4, $quiz5, $midterm, $final_project);

    if ($stmt->execute()) {
        $message = "Grades saved successfully for " . htmlspecialchars($_POST['student_name']) . "!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Retrieve students and their grades to calculate final grades
$sql = "SELECT s.student_id, s.name, g.homework1, g.homework2, g.homework3, g.homework4, g.homework5, 
        g.quiz1, g.quiz2, g.quiz3, g.quiz4, g.quiz5, g.midterm, g.final_project 
        FROM students s JOIN grades g ON s.student_id = g.student_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Personalized Grading Tool</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; }
        h2 { color: #333; }
        form { background-color: #f9f9f9; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f4f4; }
        .message { padding: 10px; color: #333; background-color: #dff0d8; border: 1px solid #d6e9c6; }
        .error { padding: 10px; color: #a94442; background-color: #f2dede; border: 1px solid #ebccd1; }
    </style>
</head>
<body>
    <h2>Enter Grades for a Student</h2>

    <?php if (!empty($message)): ?>
        <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Student:
            <select name="student_id" required>
                <option value="">Select a student</option>
                <?php while($student = $students->fetch_assoc()): ?>
                    <option value="<?php echo $student['student_id']; ?>">
                        <?php echo htmlspecialchars($student['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label><br><br>

        <h3>Homework Scores</h3>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <label>Homework <?php echo $i; ?>: <input type="number" name="homework<?php echo $i; ?>" required></label><br>
        <?php endfor; ?>

        <h3>Quiz Scores</h3>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <label>Quiz <?php echo $i; ?>: <input type="number" name="quiz<?php echo $i; ?>" required></label><br>
        <?php endfor; ?>

        <h3>Midterm and Final Project</h3>
        <label>Midterm: <input type="number" name="midterm" required></label><br>
        <label>Final Project: <input type="number" name="final_project" required></label><br><br>

        <button type="submit">Submit Grades</button>
    </form>

    <h2>Final Grades</h2>
    <?php
    if ($result->num_rows > 0) {
        echo "<table><tr><th>Student</th><th>Final Grade</th></tr>";
        while($row = $result->fetch_assoc()) {
            $grades = [
                'homework1' => $row['homework1'],
                'homework2' => $row['homework2'],
                'homework3' => $row['homework3'],
                'homework4' => $row['homework4'],
                'homework5' => $row['homework5'],
                'quiz1' => $row['quiz1'],
                'quiz2' => $row['quiz2'],
                'quiz3' => $row['quiz3'],
                'quiz4' => $row['quiz4'],
                'quiz5' => $row['quiz5'],
                'midterm' => $row['midterm'],
                'final_project' => $row['final_project']
            ];
            $final_grade = calculateFinalGrade($grades);
            echo "<tr><td>" . htmlspecialchars($row['name']) . "</td><td>" . $final_grade . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No grades available to display.";
    }
    ?>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
