<?php 
  include 'php/database.php';
?>

<!DOCTYPE html>
<html>

<head>
  <title>Yu-Gi-Oh! LoRaLink</title>
  <link rel="stylesheet" href="../css/style.css">
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <link rel="icon" type="image/x-icon" href="yugi.ico">
</head>

<body>

  <section class="login">
    <form id="Connexion" method="POST">

      <h1>Se connecter</h1>

      <div class="inputs">
        <input type="text" id="usernameConnexion" name="usernameConnexion" placeholder="Pseudo" />
        <input type="password" id="passwordConnexion" name="passwordConnexion" placeholder="Mot de passe">
      </div>

      <div align="center">
        <a class="changement_page" href="#" onclick="toggleForms()">Je n'ai pas de compte.</a>
        <button type="submit" name="connexion">Se connecter</button>
      </div>

    </form>

    <form id="Inscription" method="POST" style="display:none;">

      <h1>S'inscrire</h1>

      <div class="inputs">
        <input type="text" id="usernameInscription" name="usernameInscription" placeholder="Pseudo" />
        <input type="password" id="passwordInscription" name="passwordInscription" placeholder="Mot de passe">
        <input type="password" id="confirmPasswordConnexion" name="confirmPasswordConnexion" placeholder="Confimer le mot de passe">
      </div>

      <div class="inputs">
        <input type="text" id="firstNameInscription" name="firstNameInscription" placeholder="First Name" />
        <input type="text" id="idKonamieInscription" name="idKonamieInscription" placeholder="Id Konamie" />
      </div>

      <div align="center">
        <a class="changement_page" href="#" onclick="toggleForms()">J'ai déjà un compte.</a>
        <button type="submit" name="inscription">S'inscrire</button>
      </div>

    </form>
  </section>

  <script>
    function toggleForms() {
      var loginForm = document.getElementById("Connexion");
      var registerForm = document.getElementById("Inscription");

      if (loginForm.style.display === "none") {
        loginForm.style.display = "block";
        registerForm.style.display = "none";
      } else {
        loginForm.style.display = "none";
        registerForm.style.display = "block";
      }
    }
  </script>

</body>
</html>