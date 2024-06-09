<?php 
  session_start();
  include 'database.php';

  function get_tournament_info($mysqli)
    {
        $user_id = intval($_SESSION['id']);
        $query = "SELECT tournoi.type_tournoi, tournoi.name FROM tournoi_user JOIN tournoi ON tournoi_user.id_tournoi = tournoi.id_tournoi WHERE tournoi_user.id_user = '$user_id'";
        
        $result = $mysqli->query($query);
        
        
        if ($result->num_rows > 0) {
        echo "<div class='box'>";
        echo "<h2>Tournoi(s) inscrit :</h2>";
        echo "<table class='content-table'>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Nom</th>
                    </tr>
                </thead>
                <tbody>";
        // Afficher les données
        while ($row = $result->fetch_assoc()) {
            // Remplacer les types par les noms correspondants
            switch ($row['type_tournoi']) {
            case 1:
                $type = "YuGiOh";
                break;
            case 2:
                $type = "Pokemon";
                break;
            case 3:
                $type = "Magic";
                break;
            default:
                $type = "Inconnu";
            }
            echo "<tr>";
            echo "<td>$type</td>";
            echo "<td>".$row['name']."</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
        } else {
        echo "<div class='box'>Vous n'êtes inscrit à aucun tournoi.</div>";
        }
    
    }  

    function get_tournament_non_incrit($mysqli) {
        $user_id = intval($_SESSION['id']);
        
        // Query to retrieve tournaments where the user is not registered
        $query = "SELECT tournoi.type_tournoi, tournoi.name, tournoi.id_tournoi
                  FROM tournoi 
                  LEFT JOIN tournoi_user 
                  ON tournoi.id_tournoi = tournoi_user.id_tournoi 
                  AND tournoi_user.id_user = '$user_id' 
                  WHERE tournoi_user.id_user IS NULL";
        
        $result = $mysqli->query($query);
        
        if ($result->num_rows > 0) {
            echo "<div class='box'>";
            echo "<h2>Tournoi(s) non-inscrit :</h2>";
            echo "<table class='content-table'>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";
            // Afficher les données
            while ($row = $result->fetch_assoc()) {
                // Remplacer les types par les noms correspondants
                switch ($row['type_tournoi']) {
                    case 1:
                        $type = "YuGiOh";
                        break;
                    case 2:
                        $type = "Pokemon";
                        break;
                    case 3:
                        $type = "Magic";
                        break;
                    default:
                        $type = "Inconnu";
                }
                echo "<tr>";
                echo "<td>$type</td>";
                echo "<td>".$row['name']."</td>";
                echo "<td><button class='inscriptionBtn' data-tournoi-id='".$row['id_tournoi']."'>S'inscrire</button></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "</div>";
        } else {
            echo "<div class='box'>Vous êtes inscrit à tous les tournois disponibles.</div>";
        }
    }

    function get_deck_info($mysqli)
  {
    $user_id = intval($_SESSION['id']);
    $query = "SELECT * FROM deck WHERE deck.id_user = '$user_id'";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
      echo "<div class='box'>";
      echo "<h2>Vos Decks :</h2>";
      echo "<table class='content-table'>
              <thead>
                  <tr>
                      <th>Nom</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody>";

      while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row['Name']."</td>";
        echo "<td>Actions</td>";
        echo "</tr>";
      }

      echo "</tbody></table>";
      echo "</div>";
    } else {
      echo "<div class='box'>Vous n'avez aucun deck pour le moment. <a href='deck_creation.php'>Créer un nouveau deck</a></div>";
    }
  }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = intval($_SESSION['id']);
        $tournoi_id = intval($_POST['tournoi_id']);

        $stmt = $mysqli->prepare("INSERT INTO `tournoi_user` (`id_tournoi`, `id_user`) VALUES (?, ?)");
        $stmt->bind_param("ii", $tournoi_id, $user_id);
        $stmt->execute();
        $stmt->close();

        $stmt_userscore = $DatabaseGame->prepare("INSERT INTO `userscore` (`user_id`, `tournament_id`) VALUES (?, ?)");
        $stmt_userscore->bind_param("ii", $user_id, $tournoi_id);
        $stmt_userscore_result = $stmt_userscore->execute();
        $stmt_userscore->close();
    
        $stmt_usertournament = $DatabaseGame->prepare("INSERT INTO `usertournament` (`user_id`, `tournament_id`) VALUES (?, ?)");
        $stmt_usertournament->bind_param("ii", $user_id, $tournoi_id);
        $stmt_usertournament_result = $stmt_usertournament->execute();
        $stmt_usertournament->close();
    
        if ($stmt_userscore_result && $stmt_usertournament_result) {
            echo "Registration successful. You can now login.";
        } else {
            echo "Error: " . $DatabaseGame->error;
        }
    }
    
?>
