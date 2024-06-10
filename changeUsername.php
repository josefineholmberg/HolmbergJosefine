<?php



//sidan ska bara vara tillgänglig för inloggade användare
session_start();
if (!isset($_SESSION['userID'])) {
  header("Location: login.html");
  exit;
}

if (isset($_POST['submit'])) {
  $userID = $_SESSION['userID'];
  $newUsername = $_POST['newUsername'];

  $errors = validate_input($newUsername);

  if (empty($errors)) {
    $difference = different_name($userID, $newUsername);  //$difference kommer få värdet true om användarnamnen (det nuvarande och inputen) är olika, annars false
    if ($difference === true) {
      change_name($userID, $newUsername);
      $_SESSION['successMessage'] = "Username changed successfully!";
    } else {
      $_SESSION['errorMessage'] = "The new username is the same as the existing username. Please choose a different username.";
    }
  } else {
    $_SESSION['errorMessage'] = $errors;
  }
}


//server-side validering av input-datan
function validate_input($username)
{
  $errors = array();
  $username = trim($username);

  if (!isset($username) || empty($username) || preg_match('/^_+$/', $username)) {
    $errors[] = "Please enter a new username";
  }
  return $errors;
}


//funktion som jämför användarens nuvarande username med input
function different_name($userID, $username)
{
  try {
    $db = new SQLite3("./db/database.db");

    $sql = "SELECT username FROM Register WHERE userID = :userID";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':userID', $userID);

    $result = $stmt->execute();

    if ($result === false) {
      $_SESSION['errorMessage'] = "Error executing SQL query: " . $db->lastErrorMsg();
      return false;
    }

    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row === false) {
      $_SESSION['errorMessage'] = "Error fetching data from result: " . $db->lastErrorMsg();
      return false;
    }

    if ($row['username'] == $username) {
      return false;
    } else {
      return true;
    }
  } catch (Exception $e) {
    $_SESSION['errorMessage'] = "An error occurred: " . $e->getMessage();
    return false;
  }
}

//funktion som ändrar det nuvarande värdet i databasen för kolumnen username 
function change_name($userID, $username)
{
  try {
    $db = new SQLite3("./db/database.db");

    $sql = "UPDATE Register SET username=:username WHERE userID=:userID";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
  } catch (Exception $e) {
    $_SESSION['errorMessage'] = "An error occurred: " . $e->getMessage();
  }
}

header("Location: profile.php");
exit;
?>