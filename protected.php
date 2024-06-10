<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcelona guide</title>
    <link rel="stylesheet" href="stylesheet.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Hantera formuläret mha AJAX
            $('form[name="kommentera"]').submit(function(event) {       //funktionen körs när användaren trycker på submit
                event.preventDefault();
                var formData = new FormData(this);

                // Gör AJAX-förfrågan
                $.ajax({
                    url: 'post_comment.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'html',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#comments-container').prepend(response); 
                        $('form[name="kommentera"]')[0].reset();
                    },
                    error: function() {
                        $('.comment-section').html('An error occurred.');
                    }
                });
            });

            // Hämta nya kommentarer regelbundet med ett visst intervall
            setInterval(function() {
                var lastCommentID = $('.comments-container .comment:last').data('commentID'); // Hämta ID för senaste kommentaren

                $.ajax({
                    url: 'fetch_comments.php',
                    type: 'GET',
                    data: { lastCommentID  },
                    dataType: 'html',
                    cache: false,
                    success: function(response) {
                        $('.comments-container').append(response);
                    },
                    error: function() {
                        console.log('An error occurred while retrieving new comments.');
                    }
                });
            }, 5000); // funktionen körs var femte sekund
        });
    </script>
</head>
<body>
    <div class="navbar">
        <div class="nav">
            <ul>
                <li class="link">
                    <a href="profile.php">MY PROFILE</a>
                </li>
                <li class="link">
                    <a href="logout.php">SIGN OUT</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="header">
        <h1>Barcelona travelguide</h1>
        <h5>Share your favorite things to do in the city - where to eat, where to shop, must-visit sites,, hidden gems, museums etc!</h5>
    </div>

    <div class="comment-section">
        <h1>Comments</h1>


        <form action="search_comments.php" method="GET">
            <input type="text" name="keyword" placeholder="Search for a comment..." required>
            <input type="submit" name="search" value="Search">
        </form>

        
        <div id="comments-container">
            <?php include 'comments.php'; ?> <!-- Inkludera kommentarerna här -->
        </div>
     

        <form name="kommentera" id="comment-form" enctype="multipart/form-data">
            <label for="message">Post a comment</label>
            <textarea id="message" name="message" maxlength="500" placeholder="Write your comment here.." required></textarea>
            <label for="image">Upload an image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <input type="submit" name="submit" value="Post Comment">
        </form>
    </div>
</body>
</html>
