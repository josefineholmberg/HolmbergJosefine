<?php





//endast inloggade användare ska kunna komma åt denna sida
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit;
}


if (isset($_POST['passwordSubmit'])) {
    $userID = $_SESSION['userID'];
    $newPassword = $_POST['newPassword'];

    $errors = validate_input($newPassword);

    if (empty($errors)) {
        $difference = different_password($userID, $newPassword);   //$difference kommer få värdet true om lösenorden är olika, annars false
        if ($difference === true) {
            $hashedPassword = hash_password($newPassword);
            change_password($userID, $hashedPassword);
            $_SESSION['successMessage'] = "Password changed successfully";
        } else {
            $_SESSION['errorMessage'] = "The new password is the same as the existing password. Please choose a different password.";
        }
    } else {
        $_SESSION['errorMessage'] = $errors;
    }
}

//validering av input-datan på server-sidan
function validate_input($password)
{
    $errors = array();

    if (empty($password)) {
        $errors[] = "Please enter a password";
    }
    if ((strlen($password) < 6 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password))) {
        $errors[] = "Your password must consist of at least 6 characters, containing at least one number, uppercase letter, and lowercase letter";
    }

    return $errors;
}



//funktion som jämför det nuvarande lösenordet med inputen från formuläret (det nya lösenordet). Om det är samma lösenord returneras false, annars true
function different_password($userID, $newPassword)
{
    try {
        $db = new SQLite3("./db/database.db");

        $sql = "SELECT password FROM Register WHERE userID=:userID";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userID', $userID);

        $result = $stmt->execute();

        if ($result === false) {
            $_SESSION['errorMessage'] = "Error executing SQL query: " . $db->lastErrorMsg();
            return false;
        }

        $row = $result->fetchArray(SQLITE3_ASSOC);

        if ($row === false) {
            $_SESSION['errorMessage'] = "Error executing SQL query: " . $db->lastErrorMsg();
            return false;
        }

        $oldPassword = $row['password'];

        if (password_verify($newPassword, $oldPassword)) {
            return false;
        } else {
            return true;
        }
    } catch (Exception $e) {
        $_SESSION['errorMessage'] = "An error occurred: " . $e->getMessage();
        return false;
    }
}



//funktion som ändrar värdet för kolumnen password till det nya lösenordet (i databasen)
function change_password($userID, $password)
{
    try {
        $db = new SQLite3("./db/database.db");
        $sql = "UPDATE Register SET password=:password WHERE userID=:userID";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':password', $password, SQLITE3_TEXT);
        $stmt->bindParam(':userID', $userID, SQLITE3_TEXT);
        $stmt->execute();

    } catch (Exception $e) {
        $_SESSION['errorMessage'] = "An error occurred: " . $e->getMessage();
    }
}


//hjälpfunktion som hashar det nya lösenordet och returnerar det
function hash_password($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}


header("Location: profile.php");
exit;

?>