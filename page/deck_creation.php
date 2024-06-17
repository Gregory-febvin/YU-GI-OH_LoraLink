<?php 
  include '../php/user_info.php';

  if (!isset($_SESSION['id'])) {
    header("Location: ../index.php"); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
  }

  //var_dump($_SESSION['id']);
  //var_dump($_SESSION['username']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../yugi.ico">
    <link rel="stylesheet" href="../css/deck.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../js/deck.js"></script>

</head>
<body>
    <a href="accueil.php" class="link">
        <h1 style="">Accueil</h1>
    </a>
    
    <div>
        <form class="container" id="decklist" name="decklist">

            <div class="table-container">
                <h3>Carte Monstre</h3>
                <table class='content-table'>
                    <tbody>
                        <?php
                            for ($i = 1; $i <= 20; $i++) {
                                echo "<tr class='row'>";
                                echo "<th class='row_num'><span>$i</span></th>";
                                echo "<td>";
                                echo "<div class='card_name' style='position: relative;'>";
                                echo "<input id='monm_$i' name='monm_$i' type='text' class='keyword' placeholder='Nom de la carte'>";
                                echo "<div id='suggestions_monm_$i' class='suggestions'></div>";
                                echo "</div>";
                                echo "</td>";
                                echo "<td>";
                                echo "<input id='monum_$i' name='monum_$i' class='nb_card' placeholder='Nb'  type='text'>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h3>Carte Magie</h3>
                <table class='content-table'>
                    <tbody>
                        <?php
                            for ($i = 1; $i <= 20; $i++) {
                                echo "<tr class='row'>";
                                echo "<th class='row_num'><span>$i</span></th>";
                                echo "<td>";
                                echo "<div class='card_name' style='position: relative;'>";
                                echo "<input id='spnm_$i' name='spnm_$i' type='text' class='keyword' placeholder='Nom de la carte'>";
                                echo "<div id='suggestions_spnm_$i' class='suggestions'></div>";
                                echo "</div>";
                                echo "</td>";
                                echo "<td>";
                                echo "<input id='spnum_$i' name='spnum_$i' class='nb_card' placeholder='Nb'  type='text'>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h3>Carte Piège</h3>
                <table class='content-table'>
                    <tbody>
                        <?php
                            for ($i = 1; $i <= 20; $i++) {
                                echo "<tr class='row'>";
                                echo "<th class='row_num'><span>$i</span></th>";
                                echo "<td>";
                                echo "<div class='card_name' style='position: relative;'>";
                                echo "<input id='trnm_$i' name='trnm_$i' type='text' class='keyword' placeholder='Nom de la carte'>";
                                echo "<div id='suggestions_trnm_$i' class='suggestions'></div>";
                                echo "</div>";
                                echo "</td>";
                                echo "<td>";
                                echo "<input id='trnum_$i' name='trnum_$i' class='nb_card' placeholder='Nb'  type='text'>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h3>Extra Deck</h3>
                <table class='content-table'>
                    <tbody>
                        <?php
                            for ($i = 1; $i <= 20; $i++) {
                                echo "<tr class='row'>";
                                echo "<th class='row_num'><span>$i</span></th>";
                                echo "<td>";
                                echo "<div class='card_name' style='position: relative;'>";
                                echo "<input id='exnm_$i' name='exnm_$i' type='text' class='keyword' placeholder='Nom de la carte'>";
                                echo "<div id='suggestions_exnm_$i' class='suggestions'></div>";
                                echo "</div>";
                                echo "</td>";
                                echo "<td>";
                                echo "<input id='exnum_$i' name='exnum_$i' class='nb_card' placeholder='Nb'  type='text'>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        
        
            <div class="num_total">
                <a id="total_deck">Total Cartes dans le Deck: <span id="total_deck_count">0</span></a>
                <a id="total_extra_deck">Total Cartes dans l'Extra Deck: <span id="total_extra_deck_count">0</span></a>
            </div>
            <div class="num_total">
                <input id="deck_name"' placeholder='Nom du deck'  type='text'>
                <button id="registerDeck">Sauvegarder</button>
            </div>
            <div id="error_message" style="color: red; display: none; margin-top: 10px;"></div>
        </form>
    </div>
    <button id="createTestDeck">Créer un deck test</button>
</body>
</html>
