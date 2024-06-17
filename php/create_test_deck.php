<?php
include 'database.php'; 

session_start();

$user_id = intval($_SESSION['id']);
$insert_deck_query = "INSERT INTO `deck`(`Name`, `id_user`) VALUES ('Deck Test', $user_id)";

// Exécuter la requête pour créer le nouveau deck
if ($mysqli->query($insert_deck_query) === TRUE) {
    $new_deck_id = $mysqli->insert_id;
    
    $cartes = array(
        "Hurluberlu et Oiseau de Verrouillage" => 3,
        "Renfort de l'Armée" => 1,
        "Infini Éphémère" => 3,
        "Ogre Fantôme et Lapin des Neiges" => 2,
        "Pot de la Prospérité" => 2,
        "Scie Jumelle Griffrayeur" => 1,
        "Floraison de Cendres et Joyeux Printemps" => 3,
        "Arrivée Griffrayeur" => 2,
        "Affrontraflure Griffrayeur" => 1,
        "Fantôme du Défunt et Fraîcheur Lunaire" => 2,
        "Planète Primitive Reichphobia" => 2,
        "Astra Griffrayeur" => 3,
        "Belone Griffrayeur" => 3,
        "Acro Griffrayeur" => 3,
        "Reichheart Griffrayeur" => 3,
        "Visas Starfrost" => 2,
        "Fenrir Kashtira" => 2,
        "Kashtira Griffrayeur" => 2,
        "Féroce Astraloud" => 2,
        "Souverain Suprême de l'Âmépée - Chengying" => 1,
        "Ange du Chaos" => 1,
        "Numéro 41 : Bagooska le Tapir Terriblement Fatigué" => 1,
        "Lumière-Heart Griffrayeur" => 2,
        "Aussa la Charmeuse de Terre Inébranlable" => 1,
        "Donner, Dague Mercefourrure" => 1,
        "Licorne, Chevalier du Cauchemar" => 1,
        "Tri-Heart Griffrayeur" => 3,
        "Accècodeur Bavard" => 1,
        "Déesse des Enfers du Monde Fermé" => 1
    );

    $insert_query = "INSERT INTO `deck_card`(`id_deck`, `id_card`, `quantity`) VALUES ";

    foreach ($cartes as $nom_carte => $quantite) {
        $select_query = "SELECT id_card FROM `card` WHERE name='" . mysqli_real_escape_string($mysqli, $nom_carte) . "'";
        $result = $mysqli->query($select_query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_carte = $row['id_card'];
            $insert_query .= "($new_deck_id, $id_carte, $quantite),";
        } else {
            echo "Carte non trouvée : $nom_carte<br>";
        }
    }

    $insert_query = rtrim($insert_query, ','); // Supprimer la virgule finale

    if ($mysqli->query($insert_query) === TRUE) { // Utiliser la requête d'insertion dans `deck_card`
        header("Location: ../page/accueil.php"); // Redirection après l'insertion réussie
        exit();
    } else {
        echo "Erreur lors de l'insertion dans `deck_card` : " . $mysqli->error;
    }
} else {
    echo "Erreur lors de la création du deck : " . $mysqli->error;
}

$mysqli->close();
?>
