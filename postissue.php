<?php
// Include database connection
include 'db_connect.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $batch = $_POST['batch'];
    $dept = $_POST['dept'];
    $place = $_POST['place'];
    $description = $_POST['description'];
    $imageName = "";

    // Handle image upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    try {
        // Insert data into database using PDO
        $sql = "INSERT INTO issues (batch, dept, place, description, image) 
                VALUES (:batch, :dept, :place, :description, :image)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':batch', $batch);
        $stmt->bindParam(':dept', $dept);
        $stmt->bindParam(':place', $place);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $imageName);

        $stmt->execute();

        // Redirect to all posts page after successful save
        header("Location: Allpost.html?success=1");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
