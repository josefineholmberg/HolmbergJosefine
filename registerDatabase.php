<?php


if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $psw = $_POST['password'];

    $errors = validate_input($username, $email, $psw);

    if (empty($errors)) {
        $result = check_user_exists($email);
        if ($result === false) {
            register_user($username, $email, $psw);
            header("Location: login.html");
            exit;
        } else {
            echo "This email is already registered";
        }
    } else {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}


//funktion för server-side validering av input
function validate_input($username, $email, $psw)
{
    $errors = array();
    $username = trim($username);

    if (!isset($username) || empty($username) || preg_match('/^_+$/', $username)) {
        $errors[] = "Please enter an username";
    }
    if (empty($email)) {
        $errors[] = "Please enter your email";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email";
    }
    if (empty($psw)) {
        $errors[] = "Please enter a password";
    }
    if ((strlen($psw) < 6 || !preg_match('/[a-z]/', $psw) || !preg_match('/[A-Z]/', $psw) || !preg_match('/[0-9]/', $psw))) {
        $errors[] = "Your password must consist of at least 6 characters, containing at least one number, uppercase letter, and lowercase letter";
    }

    return $errors;
}


//funktionen kollar om input-emailen användaren angett redan finns registrerad i databasen
function check_user_exists($email)
{
    try {
        $db = new SQLite3("./db/database.db");
        $sql = "SELECT * FROM 'Register' WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);

        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($result !== false) {
            return $result;
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


//funktion som lagrar användarens input i databasen
function register_user($username, $email, $psw)
{
    try {

        $db = new SQLite3("./db/database.db");

        $sql = "INSERT INTO 'Register'('username','email','password')VALUES(:username , :email, :password)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username, SQLITE3_TEXT);
        $stmt->bindParam(':email', $email, SQLITE3_TEXT);
        $hashedPassword = password_hash($psw, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword, SQLITE3_TEXT);

        if ($stmt->execute()) {
            echo "Registered!";
        } else {
            echo "Failed to register";
        }

        $db->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
