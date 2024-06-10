<?php

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lastCommentID = $_GET['lastCommentID'];

    try {
        $db = new SQLite3("./db/database.db");
        $sql = "SELECT P.*, R.username FROM Post P JOIN Register R ON P.userID=R.userID WHERE P.commentID > :lastCommentID ORDER BY P.commentID DESC";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':lastCommentID', $lastCommentID, SQLITE3_INTEGER);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $username = $row['username'];
            $message = $row['message'];
            $image = $row['image'];

            echo "<div class='comment' data-comment-id='{$row['commentID']}'>";
            echo "<div class='comment-author'>";
            echo "<h4>Posted by: $username</h4>";
            echo "</div>";
            echo "<div class='comment-body'>";
            echo "<p>$message</p>";
            if ($image) {
                echo "<div class='comment-image'><img src='path/to/my/upload/directory/$image' alt='Comment Image'></div>";
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
