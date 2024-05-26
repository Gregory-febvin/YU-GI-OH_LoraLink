<?php 
  session_start();
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
    <style>
        h3 {
            text-align: center;
            background: #888;
            color: #fff;
            font-size: 1.4rem;
            padding: 0;
            margin: 0;
            border-bottom: 3px solid #555;
        }
        .card_name {
            display: flex;
            align-items: center;
        }
        .card_name input{
            border: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
        }
        .container {
            max-width: 960px;
            width: 80%;
            margin: auto;
            padding: 0px 0px 350px 0px;
            display: flex;
            flex-wrap: wrap;
        }
        .table-container {
            flex: 1;
            max-width: 30%;
            min-width: 300px;
            border-collapse: collapse;
            margin: 0.1em;
            border: 3px solid #555;
        }
        .content-table {
            border-collapse: collapse;
            width: 100%;
        }
        .content-table th,
        .content-table td {
            padding: 4px;
            border: 1px solid #ddd;
        }
        .content-table th {
            background-color: #f2f2f2;
        }
        .nb_card{
            width: 30px;
            display: block;
            margin: 0 auto;
            border: 1px solid #888;
            background-color: #fff;
            box-sizing: border-box;
        }
        .suggestions {
            width: 302px;
            position: absolute;
            top: 100%;
            margin-top: 20px; /* 20px en dessous de l'input */
            border: solid 3px #000;
            z-index: 10;
            max-height: 320px;
            overflow-y: auto;
            background-color: #fff; /* Optionnel : pour avoir un fond blanc */
            display: none; /* Caché par défaut */
        }

        .suggestion {
            padding: 5px;
            cursor: pointer;

            border: solid 1px #000;
        }

        .suggestion:hover {
            background-color: #f0f0f0;
        }
        .num_total{
            width: 100%;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 20px 0 10px;
        }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../js/deck.js"></script>

</head>
<body>
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
</body>
</html>
