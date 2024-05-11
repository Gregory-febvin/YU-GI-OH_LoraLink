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
<html>
  <head>
    <meta charset="utf-8">
    <title>Yu-Gi-Oh! LoRaLink</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/user.css">
  </head>
  <body>
  <div>

  <div class="modal" id="inscriptionModal">
      <div class="modal-content">
          <span class="close">&times;</span>
          <p>Voulez-vous vraiment vous inscrire à ce tournoi ?</p>
          <button id="confirmInscription">Confirmer</button>
      </div>
  </div>

    <?php 
      get_tournament_info($mysqli);
      get_tournament_non_incrit($mysqli);
    ?>
  </div>
    <script src="../js/accueil.js" ></script>
  </body>
</html>


