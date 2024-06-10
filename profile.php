<?php
session_start();
if (!isset($_SESSION['userID'])) {
  header("Location: login.html");
  exit;
}

$db = new SQLite3("./db/database.db");
$userID = $_SESSION['userID'];
$sql = "SELECT username, email, password FROM Register WHERE userID = :userID";
$stmt = $db->prepare($sql);
$stmt->bindValue(':userID', $userID, SQLITE3_TEXT);
$result = $stmt->execute();

if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
  $username = $row['username'];
  $email = $row['email'];
} else {
  header("Location: error.html");
  exit();
}

$db->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My profile</title>
  <link rel="stylesheet" href="stylesheet.css">
</head>
<body>

  <div class="navbar">
    <div class="nav">
      <ul>
        <li class="link">
          <a href="protected.php">HOME</a>
        </li>
        <li class="link">
          <a href="logout.php">SIGN OUT</a>
        </li>
      </ul>
    </div>
  </div>

  <div class="header">
        <h1>My profile</h1>
    </div>

  <div class="profile">

    <div class="pcontainer">
      
      <?php
      if (isset($_SESSION['errorMessage'])) {
        echo '<div class="error-message">' . $_SESSION['errorMessage'] . '</div>';
        unset($_SESSION['errorMessage']);
      }
      if (isset($_SESSION['successMessage'])) {
        echo '<div class="success-message">' . $_SESSION['successMessage'] . '</div>';
        unset($_SESSION['successMessage']);
      }
      ?>
      <div class="col">
        <h5>USERNAME </h5>
        <h3>Current username:  <?php echo  $username; ?></h3>
        <form name="changeUsername" id="changeUsername" action="changeUsername.php" method="post" onsubmit="return testUsername();">
          <label for="newUsername">Change your username:</label>
          <input type="username" id="newUsername" name="newUsername" placeholder="Enter your new username here..." required>
          <input type="submit" name="submit" value="Change username">
        </form>
      </div>
      
      <div class="col">
        <h5>PASSWORD</h5>
        <form name="changePassword" id="changePassword" action="changePassword.php" method="post" onsubmit="return testPassword();">
        <label for="newPassword">Change your password:</label>
        <input type="password" id="newPassword" name="newPassword" placeholder="Enter your new password here..." required>
        <input type="submit" name="passwordSubmit" value="Change password">
       </form>
      </div>
    </div>
  </div>

  <script>
    function testUsername() {
      var newUsername = document.forms["changeUsername"]["newUsername"].value;
      if (!validateName(newUsername)) {
        alert("Please choose an username");
        return false;
      }
      return true;
    }

    function validateName(name) {
      if (name.trim() === "") {
        return false;
      }
      if (/^_+$/.test(name)) {
        return false;
      }
      return true;
    }


    function testPassword(){
      var newPassword=document.forms["changePassword"]["newPassword"].value;
      if(!passwordCheck(newPassword)){
        alert("Please enter a valid password. Your password must consist of at least 6 characters, including at least one uppercase letter, lowercase letter and number.");
        return false;
      }
      return true;
    }

    function passwordCheck(psw) {
        // Lösenordet måste vara minst sex tecken långt och innehålla minst en stor bokstav, minst en liten bokstav och minst en siffra.
        var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/;
        return passwordRegex.test(psw);
    }

  </script>
</body>
</html>
