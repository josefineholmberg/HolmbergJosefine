<?php

session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit;
}

echo "<link rel='stylesheet' type='text/css' href='stylesheet.css'>";



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search results</title>
</head>
<body>
    <div class="navbar">
            <div class="nav">
                <ul>
                  
                    <li class="link">
                        <a href="protected.php">HOME</a>
                    </li>
                    <li class="link">
                        <a href="profile.php">MY PROFILE</a>
                    </li>
                    <li class="link">
                        <a href="logout.php">SIGN OUT</a>
                    </li>
                </ul>
            </div>
        </div>
    <div class="comment-section">
        <h1>Search results </h1>


    <?php
        


    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $keyword = $_GET['keyword'];

        try {
            $db = new SQLite3("./db/database.db");
            $stmt = $db->prepare("SELECT P.*, R.username FROM Post P JOIN Register R ON P.userID = R.userID WHERE P.message LIKE :keyword ORDER BY P.commentID DESC");
            $stmt->bindValue(':keyword', "%$keyword%", SQLITE3_TEXT);
            $result = $stmt->execute();

            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        
                echo "<div class='comment' data-comment-id='{$row['commentID']}'>";
                echo "<div class='comment-author'>";
                echo "<h4>Posted by: {$row['username']}</h4>";
                echo "</div>";
                echo "<div class='comment-body'>";
                echo "<p>{$row['message']}</p>";
                if (!empty($row['image'])) {
                    echo "<img src='path/to/my/upload/directory/{$row['image']}' alt='Comment Image' width='200'>";
                }
                echo "</div>";
                echo "</div>";
            }

            $db->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    ?>
    </div> 
</body>
</html>

