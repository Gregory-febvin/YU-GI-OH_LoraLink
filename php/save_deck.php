<?php
session_start();
// Connexion à la base de données
$databaseHost = '127.0.0.1';
$databaseUsername = 'yugioh';
$databasePassword = 'yugioh';
$databaseName = 'yugioh';

$mysqli = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

// Vérifier la connexion
if ($mysqli->connect_errno) {
    echo "Échec de la connexion à la base de données: " . $mysqli->connect_error;
    exit();
}

if (isset($_SESSION['id']) && isset($_POST['deckName'])) {
    $deckName = $_POST['deckName'];
    
    // Insérer le deck dans la table "deck"
    $insertDeckQuery = "INSERT INTO `deck`(`Name`) VALUES ('$deckName')";
    $mysqli->query($insertDeckQuery);

    // Récupérer l'ID du deck nouvellement inséré
    $deckId = $mysqli->insert_id;

    // Insérer l'ID du deck pour l'utilisateur dans la table "user"
    $insertUserDeckQuery = "UPDATE `user` SET `id_deck`='$deckId' WHERE `id_user`='" . $_SESSION['id'] . "'";
    $mysqli->query($insertUserDeckQuery);

    echo $deckId; // Renvoyer l'ID du deck
}
?>
