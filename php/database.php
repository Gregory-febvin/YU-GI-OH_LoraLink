<?php
session_start();

$databaseHost = '127.0.0.1';
$databaseUsername = 'yugioh';
$databasePassword = 'yugioh';
$databaseName = 'yugioh';

$mysqli = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

function sanitizeSQL($query) {
    $pattern = '/[\t\r\n]|(--[^\r\n]*)|(\/\*[\w\W]*?(?=\*)\*\/)/i';
    return preg_replace($pattern, '', $query);
}

if (isset($_POST['connexion'])) {
    $username = $_POST['usernameConnexion'];
    $password = $_POST['passwordConnexion'];

    $username = $mysqli->real_escape_string($username);
    $password = $mysqli->real_escape_string($password);

    $query = sanitizeSQL("SELECT `id_user`, `surname`, `firstname`, `id_konami` FROM `user` WHERE `surname` = '$username' AND `password` = '$password'");
    
    $result = $mysqli->query($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc(); 
        $_SESSION['id'] = $row['id_user']; 
        $_SESSION['surname'] = $row['surname']; 

        header("Location: page/accueil.php");
        exit();
    } else {
        echo "Invalid username or password";
    }
}

// Handle registration form submission
if (isset($_POST['inscription'])) {
    $username = $_POST['usernameInscription'];
    $password = $_POST['passwordInscription'];
    $confirmpassword = $_POST['confirmPasswordConnexion'];
    $firstname = $_POST['firstNameInscription'];
    $idkonamie = $_POST['idKonamieInscription'];

    // Check if passwords match and are not empty
    if (($password == $confirmpassword && $password != '') || ($password == '' && $confirmpassword == '')) {
        // Sanitize inputs (You should perform proper validation and sanitation here)
        $username = $mysqli->real_escape_string($username);
        $password = $mysqli->real_escape_string($password);
        $firstname = $mysqli->real_escape_string($firstname);
        $idkonamie = $mysqli->real_escape_string($idkonamie);

        // Sanitize SQL query
        $insert_query = sanitizeSQL("INSERT INTO `user` (`surname`, `firstname`, `password`, `id_konami`) VALUES ('$username', '$firstname', '$password', '$idkonamie')");
        
        // Insert new user into database
        if ($mysqli->query($insert_query) === TRUE) {
            echo "Registration successful. You can now login.";
        } else {
            echo "Error: " . $mysqli->error;
        }
    } else {
        echo "Passwords do not match or are empty.";
    }
}

// Close the database connection
//$mysqli->close();
?>