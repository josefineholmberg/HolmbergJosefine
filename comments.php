<?php




session_start();
//endast inloggade användare ska ha tillgång till denna sida
if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit;
}

try {
    $db = new SQLite3("./db/database.db");
    $lastCommentID = isset($_GET['lastCommentID']) ? $_GET['lastCommentID'] : 0;

    $sql = "SELECT Register.username, Post.message, Post.image
            FROM Register
            JOIN Post ON Register.userID = Post.userID
            WHERE Post.commentID > :lastCommentID";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':lastCommentID', $lastCommentID, SQLITE3_INTEGER);
    $result = $stmt->execute();

    $commentsHTML = ""; 

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $name = $row['username'];
        $message = $row['message'];
        $image = $row['image'];

        $commentHTML = "<div class='comment'>";
        $commentHTML .= "<div class='comment-author'>";
        $commentHTML .= "<h4>Posted by: $name </h4>";
       
        $commentHTML .= "</div>";
        $commentHTML .= "<div class='comment-body'>";
        $commentHTML .= "<p>$message</p>";
        if ($image) {
            $commentHTML .= "<div class='comment-image'><img src='path/to/my/upload/directory/$image' alt='Comment Image'></div>";
        }
        $commentHTML .= "</div>";
        $commentHTML .= "</div>";

        $commentsHTML = $commentHTML . $commentsHTML; 




    }

    $db->close();
} catch (Exception $e) {
    $commentsHTML = "Error: " . $e->getMessage();
}

echo $commentsHTML; // Skicka den genererade HTML-koden som svar på AJAX-förfrågan
?>



