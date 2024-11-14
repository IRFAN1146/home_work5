<?php
// gradingFunctions.php - Handles grade calculation logic

// Function to calculate average of the top N scores (e.g., homework or quiz scores)
function calculateAverage($scores, $count) {
    sort($scores);
    $topScores = array_slice($scores, 0, $count);
    return array_sum($topScores) / count($topScores);
}

// Function to calculate the final grade
function calculateFinalGrade($grades) {
    $homework_avg = calculateAverage(array_slice($grades, 0, 5), 5); // Homework 1-5
    $quiz_avg = calculateAverage(array_slice($grades, 5, 5), 4); // Quiz 1-5, drop the lowest score
    $midterm = $grades['midterm'];
    $final_project = $grades['final_project'];

    // Final grade formula
    $final_grade = ($homework_avg * 0.2) + ($quiz_avg * 0.1) + ($midterm * 0.3) + ($final_project * 0.4);
    return round($final_grade);
}
?>
