<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deck info</title>
    <link rel="stylesheet" href="../css/display_deck.css">
    <link rel="icon" type="image/x-icon" href="../yugi.ico">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.card-name').hover(
                function() {
                    var cardId = $(this).data('card-id');
                    var imageUrl = generateImageUrl(cardId);
                    $('#card-image').attr('src', imageUrl);
                    $('.card-image-container').show();
                },
                function() {
                    $('.card-image-container').hide();
                }
            );

            function generateImageUrl(cardId) {
                var numStr = String(cardId);
                var xLeft = numStr.length === 5 ? '1' : '0';
                numStr = numStr.slice(-4).padStart(4, '0');
                var firstPart = numStr.slice(0, 2);
                var secondPart = numStr.slice(2);
                firstPart = firstPart === '00' ? '0' : String(parseInt(firstPart, 10));
                secondPart = secondPart === '00' ? '0' : String(parseInt(secondPart, 10));
                return `https://artworks-en-n.ygoresources.com/${xLeft}/${firstPart}/${secondPart}_1.png`;
            }
        });
    </script>
</head>
<body>
    <div class="header">
        <div class="header_navbar">
            <a href="accueil.php" class="link">Accueil</a>
        </div>
    </div>
    <div class="content">
    <?php
    include '../php/database.php';

    // Fonction pour récupérer le total de quantité pour chaque type de carte
    function getTotalQuantity($mysqli, $deck_id, $card_type) {
        $query = "SELECT SUM(deck_card.quantity) AS total_quantity FROM deck_card JOIN card ON deck_card.id_card = card.id_card WHERE deck_card.id_deck = '$deck_id' AND card.card_type = $card_type";
        $result = $mysqli->query($query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total_quantity'];
        } else {
            return 0;
        }
    }

    // Vérifier si l'ID du deck est présent dans l'URL
    if (isset($_GET['id'])) {
        $deck_id = intval($_GET['id']);

        // Récupérer les détails du deck depuis la base de données
        $query = "SELECT * FROM deck WHERE id_deck = '$deck_id'";
        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $deck_name = $row['Name'];
            $deck_created_at = $row['date_creation'];

            // Affichage des cartes du deck par type s'il y a des cartes de ce type dans le deck
            echo "<h2 style='text-align: center;'>Contenu du Deck \"$deck_name\"</h2>";
            echo "<table class='content-table'>";

            // Affichage des Monstres s'il y a des monstres dans le deck
            $monsters_total = getTotalQuantity($mysqli, $deck_id, 1);
            if ($monsters_total > 0) {
                echo "<thead><tr><th>Monstre ($monsters_total)</th><th>Nb jouée</th></tr></thead>";
                echo "<tbody>";
                $monsters_query = "SELECT card.id_card, card.name, deck_card.quantity FROM deck_card JOIN card ON deck_card.id_card = card.id_card WHERE deck_card.id_deck = '$deck_id' AND card.card_type = 1";
                $monsters_result = $mysqli->query($monsters_query);
                while ($card_row = $monsters_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='card-name' data-card-id='".$card_row['id_card']."'>".$card_row['name']."</td>";
                    echo "<td>".$card_row['quantity']."</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
            }

            // Affichage des Magies s'il y a des magies dans le deck
            $spells_total = getTotalQuantity($mysqli, $deck_id, 2);
            if ($spells_total > 0) {
                echo "<thead><tr><th>Magie ($spells_total)</th><th>Nb jouée</th></tr></thead>";
                echo "<tbody>";
                $spells_query = "SELECT card.id_card, card.name, deck_card.quantity FROM deck_card JOIN card ON deck_card.id_card = card.id_card WHERE deck_card.id_deck = '$deck_id' AND card.card_type = 2";
                $spells_result = $mysqli->query($spells_query);
                while ($card_row = $spells_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='card-name' data-card-id='".$card_row['id_card']."'>".$card_row['name']."</td>";
                    echo "<td>".$card_row['quantity']."</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
            }

            // Affichage des Pièges s'il y a des pièges dans le deck
            $traps_total = getTotalQuantity($mysqli, $deck_id, 3);
            if ($traps_total > 0) {
                echo "<thead><tr><th>Piège ($traps_total)</th><th>Nb jouée</th></tr></thead>";
                echo "<tbody>";
                $traps_query = "SELECT card.id_card, card.name, deck_card.quantity FROM deck_card JOIN card ON deck_card.id_card = card.id_card WHERE deck_card.id_deck = '$deck_id' AND card.card_type = 3";
                $traps_result = $mysqli->query($traps_query);
                while ($card_row = $traps_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='card-name' data-card-id='".$card_row['id_card']."'>".$card_row['name']."</td>";
                    echo "<td>".$card_row['quantity']."</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
            }

            // Affichage des Monstres Extra s'il y a des monstres extra dans le deck
            $extra_monsters_total = getTotalQuantity($mysqli, $deck_id, 4);
            if ($extra_monsters_total > 0) {
                echo "<thead><tr><th>Monstre Extra ($extra_monsters_total)</th><th>Nb jouée</th></tr></thead>";
                echo "<tbody>";
                $extra_monsters_query = "SELECT card.id_card, card.name, deck_card.quantity FROM deck_card JOIN card ON deck_card.id_card = card.id_card WHERE deck_card.id_deck = '$deck_id' AND card.card_type = 4";
                $extra_monsters_result = $mysqli->query($extra_monsters_query);
                while ($card_row = $extra_monsters_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='card-name' data-card-id='".$card_row['id_card']."'>".$card_row['name']."</td>";
                    echo "<td>".$card_row['quantity']."</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
            }

            echo "</table>";

        } else {
            echo "<p>Aucun deck trouvé avec cet ID.</p>";
        }
    } else {
        echo "<p>Aucun ID de deck fourni.</p>";
    }
    ?>

    <div class="card-image-container">
        <img id="card-image" class="card-image" src="">
    </div>

    </div>
</body>
</html>
