<?php
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

// Vérifier si la requête provient de la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données de la requête AJAX
    $keyword = '%' . $_POST['keyword'] . '%';
    $cardType = $_POST['cardType'];

    $sql = "SELECT * FROM `card` WHERE `card_type` = ? AND `name` LIKE ?";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        echo "Erreur de préparation de la requête: " . $mysqli->error;
        exit();
    }

    $stmt->bind_param("is", $cardType, $keyword);
    $stmt->execute();

    $result = $stmt->get_result();
    $results = array();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row['name'];
    }

    $stmt->close();
    $mysqli->close();

    echo json_encode($results);
}
?>