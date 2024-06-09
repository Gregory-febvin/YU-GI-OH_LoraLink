<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Tournois</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" type="image/x-icon" href="yugi.ico">
    <script>
        setInterval(function() {
            window.location.reload();
        }, 60000); // reload toute les 1 minute
    </script>
</head>
<body>
<div class="container">
        <h1>Liste des Tournois</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = new mysqli('127.0.0.1', 'yugioh', 'yugioh', 'test_yu');

                if ($conn->connect_error) {
                    die("Connexion échouée : " . $conn->connect_error);
                }

                $sql = "SELECT * FROM tournament";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['tournament_id'] . "</td>
                                <td><a href='tournament.php?id=" . $row['tournament_id'] . "'>" . $row['name'] . "</a></td>
                                <td>" . $row['date'] . "</td>
                                <td>" . $row['status'] . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Aucun tournoi trouvé</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
