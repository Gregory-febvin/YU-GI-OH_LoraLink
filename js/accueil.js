document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("inscriptionModal");
    var btns = document.getElementsByClassName("inscriptionBtn");
    var deckSelectionContainer = document.getElementById("deckSelectionContainer");

    // Ouvrir la fenêtre modale lors du clic sur le bouton d'inscription
    Array.from(btns).forEach(function(btn) {
        btn.addEventListener("click", function() {
            var tournoiId = btn.getAttribute("data-tournoi-id");
            document.getElementById("confirmInscription").setAttribute("data-tournoi-id", tournoiId);

            // Récupérer les decks de l'utilisateur
            fetchUserDecks(function(decks) {
                if (decks.length > 0) {
                    var selectHTML = '<select id="deckSelect">';
                    decks.forEach(function(deck) {
                        selectHTML += '<option value="' + deck.id_deck + '">' + deck.Name + '</option>';
                    });
                    selectHTML += '</select>';
                    deckSelectionContainer.innerHTML = selectHTML;
                } else {
                    deckSelectionContainer.innerHTML = 'Vous n\'avez aucun deck pour le moment. <a href="deck_creation.php">Créer un nouveau deck</a>';
                }
            });

            modal.style.display = "block";
        });
    });

    // Fermer la fenêtre modale lors 'un clic en dehors de la modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Fermer la fenêtre modale lors du clic sur la croix
    document.getElementsByClassName("close")[0].addEventListener("click", function() {
        modal.style.display = "none";
    });

    // Inscription confirmée
    document.getElementById("confirmInscription").addEventListener("click", function() {
        var tournoiId = this.getAttribute("data-tournoi-id");
        var deckId = document.getElementById("deckSelect").value;
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                window.location.reload();
            }
        };
        xhr.open("POST", "../php/user_info.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        console.log("tournoi_id=" + tournoiId + "&deck_id=" + deckId);
        xhr.send("tournoi_id=" + tournoiId + "&deck_id=" + deckId);
    });

    // Fonction pour récupérer les decks de l'utilisateur
    function fetchUserDecks(callback) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var decks = JSON.parse(this.responseText);
                callback(decks);
            }
        };
        xhr.open("POST", "../php/user_info.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("action=get_decks");
    }
});
