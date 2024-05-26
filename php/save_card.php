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

if (isset($_SESSION['id']) && isset($_POST['deckId']) && isset($_POST['cardName']) && isset($_POST['quantity'])) {
    $deckId = $_POST['deckId'];
    $cardName = $_POST['cardName'];
    $quantity = $_POST['quantity'];

    // Vérifier si les champs ne sont pas vides
    if (!empty($deckId) && !empty($cardName) && !empty($quantity)) {
        // Récupérer l'ID de la carte en fonction de son nom
        $getCardIdQuery = "SELECT `id_card` FROM `card` WHERE `name`='$cardName'";
        $result = $mysqli->query($getCardIdQuery);

        // Vérifier si la requête a renvoyé un résultat
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $cardId = $row['id_card'];

            // Insérer la carte dans la table "deck_card"
            $insertCardQuery = "INSERT INTO `deck_card`(`id_deck`, `id_card`, `quantity`) VALUES ('$deckId', '$cardId', '$quantity')";
            echo $insertCardQuery;
            $mysqli->query($insertCardQuery);
        } else {
            echo "Erreur: La carte '$cardName' n'existe pas.";
        }
    } else {
        echo "Erreur: Tous les champs doivent être remplis.";
    }
} else {
    echo "Erreur: Tous les paramètres nécessaires ne sont pas fournis.";
}
?>
