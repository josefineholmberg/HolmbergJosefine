<?php


session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit;
  }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_SESSION['userID'];
    $message = $_POST['message'];

    if(!empty($message)){
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image']['name'];
            $imageTmpPath = $_FILES['image']['tmp_name'];
            $imageSize = $_FILES['image']['size'];
    
        
            $targetDirectory = "path/to/my/upload/directory/";
            $targetFilePath = $targetDirectory . $image;
    
            if (move_uploaded_file($imageTmpPath, $targetFilePath)) {
                // Lagra kommentaren och bildens filnamn i databasen
                try {
                    $db = new SQLite3("./db/database.db");
                    $stmt = $db->prepare("INSERT INTO Post (userID, message, image) VALUES (:userID, :message, :image)");
                    $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
                    $stmt->bindValue(':message', $message, SQLITE3_TEXT);
                    $stmt->bindValue(':image', $image, SQLITE3_TEXT);
                    $stmt->execute();
    
                    $commentID = $db->lastInsertRowID();
    
                    $stmt = $db->prepare("SELECT R.username FROM Register R WHERE r.userID=:userID");
                    $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
                    $result = $stmt->execute();
    
                    $row = $result->fetchArray(SQLITE3_ASSOC);
                    $name = $row['username'];
                    $db->close();
                 
    
                    // Skriv ut den nya kommentaren
                    echo "<div class='comment' data-comment-id='$commentID'>";
                    echo "<div class='comment-author'>";
                    echo "<h4> Posted by: $name</h4>";
                    echo "</div>";
                    echo "<div class='comment-body'>";
                    echo "<p>$message</p>";
                    echo "<div class='comment-image'><img src='path/to/my/upload/directory/$image' alt='Comment Image'></div>";
                    echo "</div>";
                    echo "</div>";
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                echo "Error: Failed to move the uploaded file.";
            }
        } else {
            // Lagra kommentaren i databasen utan bild
            try {
                $db = new SQLite3("./db/database.db");
                $stmt = $db->prepare("INSERT INTO Post (userID, message) VALUES (:userID, :message)");
                $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
                $stmt->bindValue(':message', $message, SQLITE3_TEXT);
                $stmt->execute();
    
                $commentID = $db->lastInsertRowID();
    
    
                $stmt = $db->prepare("SELECT R.username FROM Register R WHERE r.userID=:userID");
                $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
                $result = $stmt->execute();
                $row = $result->fetchArray(SQLITE3_ASSOC);
                $name = $row['username'];
    
                
                $db->close();
    
                // Skriv ut den nya kommentaren
                echo "<div class='comment' data-comment-id='$commentID'>";
                echo "<div class='comment-author'>";
                echo "<h4> Posted by: $name</h4>";
                echo "</div>";
                echo "<div class='comment-body'>";
                echo "<p>$message</p>";
                echo "</div>";
                echo "</div>";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }

    }else{
        echo "Textarea for comment cannot be left empty";
    }

    
   
}
?>


