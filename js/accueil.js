document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("inscriptionModal");
    var btns = document.getElementsByClassName("inscriptionBtn");

    // Ouvrir la fenêtre modale lors du clic sur le bouton d'inscription
    Array.from(btns).forEach(function(btn) {
        btn.addEventListener("click", function() {
            var tournoiId = btn.getAttribute("data-tournoi-id");
            document.getElementById("confirmInscription").setAttribute("data-tournoi-id", tournoiId);
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
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                window.location.reload();
            }
        };
        xhr.open("POST", "../php/user_info.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("tournoi_id=" + tournoiId);
    });
});