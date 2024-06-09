<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Tournoi</title>
    <link rel="stylesheet" href="css/tournament.css">
    <link rel="icon" type="image/x-icon" href="yugi.ico">
    <script>
        setInterval(function() {
            window.location.reload();
        }, 60000); // reload toute les 1 minute

        function goBack() {
            window.location.href = 'dashboard.php';
        }
    </script>
</head>
<body>
<div class="container">
        <button class="back-button" onclick="goBack()">Retour</button>
        <?php
        if (isset($_GET['id'])) {
            $tournament_id = $_GET['id'];

            $conn = new mysqli('127.0.0.1', 'yugioh', 'yugioh', 'test_yu');

            if ($conn->connect_error) {
                die("Connexion échouée : " . $conn->connect_error);
            }

            $sql = "SELECT name FROM tournament WHERE tournament_id = $tournament_id";
            $result = $conn->query($sql);
            $tournament = $result->fetch_assoc();

            $sql = "SELECT * FROM round WHERE tournament_id = $tournament_id ORDER BY round_number DESC LIMIT 1";
            $result = $conn->query($sql);
            $round = $result->fetch_assoc();

            if ($round) {
                $round_id = $round['round_id'];
                $sql = "SELECT m.num_table, u1.username AS player1, u2.username AS player2, m.isFinish, m.callJudge 
                        FROM matche m
                        JOIN user u1 ON m.player1_id = u1.user_id
                        JOIN user u2 ON m.player2_id = u2.user_id
                        WHERE m.round_id = $round_id";
                $matches = $conn->query($sql);
            }

            echo "<h1>" . $tournament['name'] . "</h1>";
            if ($round) {
                echo "<h2>Dernier Round : " . $round['round_number'] . " (" . $round['round_date'] . ")</h2>";
                if ($matches->num_rows > 0) {
                    echo "<div class='matches'>";
                    while($match = $matches->fetch_assoc()) {
                        $class = '';
                        if ($match['isFinish'] == 1) {
                            $class = 'finished';
                        } elseif ($match['callJudge'] == 1) {
                            $class = 'call-judge';
                        } else {
                            $class = 'in-progress';
                        }
                        echo "<div class='match $class'>
                                <div class='table-number'>Table " . $match['num_table'] . "</div>
                                <div class='player'>" . $match['player1'] . "</div>
                                <div class='player'>" . $match['player2'] . "</div>
                              </div>";
                    }
                    echo "</div>";
                } else {
                    echo "<p>Aucun match trouvé pour ce round.</p>";
                }
            } else {
                echo "<p>Aucun round trouvé pour ce tournoi.</p>";
            }

            $conn->close();
        } else {
            echo "<p>ID du tournoi non spécifié.</p>";
        }
        ?>
        <div class="legend">
            <h3>Légende des Couleurs :</h3>
            <div><span style="background-color: #28a745;"></span> En cours</div>
            <div><span style="background-color: #ffa500;"></span> Appel au juge</div>
            <div><span style="background-color: #007bff;"></span> Terminé</div>
        </div>
    </div>
</body>
</html>
