<?php

session_start();

if (isset($_POST['submit'])) {

    $email = $_POST['email'];
    $psw = $_POST['password'];

    $errors = validate_input($email, $psw);

    if (empty($errors)) {
        $result = check_user_exists($email);
        if ($result === false) {
            echo "User does not exist";
        } else {
            $hash = $result['password'];
            if (password_verify($psw, $hash)) {
                echo "Login successful";
                $_SESSION['userID'] = $result["userID"];
                header("Location: protected.php");
                exit;
            } else {
                echo "Invalid password";
            }
        }
    } else {
        echo $errors;
    }
}


//server-side validering av log-in formulärdatan
function validate_input($email, $psw)
{
    $errors = array();
    if (empty($email)) {
        $errors[] = "Please enter your email";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email";
    }
    if (empty($psw)) {
        $errors[] = "Please enter your password";
    }
    if ((strlen($psw) < 6 || !preg_match('/[a-z]/', $psw) || !preg_match('/[A-Z]/', $psw) || !preg_match('/[0-9]/', $psw))) {
        $errors[] = "Your password must consist of at least 6 characters, containing at least one number, uppercase letter, and lowercase letter";
    }
    return implode("<br>", $errors);
}

//funktion som kollar ifall användaren är registrerad 
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
?>
