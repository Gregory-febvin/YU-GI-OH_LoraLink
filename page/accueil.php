<?php 
include '../php/user_info.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php"); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Yu-Gi-Oh! LoRaLink</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/user.css">
    <link rel="icon" type="image/x-icon" href="../yugi.ico">
  </head>
  <body>
    <div>
      <div class="modal" id="inscriptionModal">
        <div class="modal-content">
          <span class="close">&times;</span>
          <p>Voulez-vous vraiment vous inscrire à ce tournoi ?</p>
          <div id="deckSelectionContainer"></div>
          <button id="confirmInscription">Confirmer</button>
        </div>
      </div>

      <div class="container">
        <div class="right">
          <?php 
            get_tournament_info($mysqli);
            get_tournament_non_incrit($mysqli);
          ?>
        </div>
        
        <div class="left">
          <?php 
            get_deck_info($mysqli);
          ?>
          <a href="deck_creation.php">Deck creation</a>
        </div>
      </div>

      <script src="../js/accueil.js"></script>
    </div>
  </body>
</html>