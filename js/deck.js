$(document).ready(function(){
    $('.keyword').keyup(function(){
        var inputId = $(this).attr('id');
        var keyword = $(this).val();
        var cardType;
        
        // Déterminer le type de carte en fonction de l'ID de l'input
        if (inputId.startsWith('monm')) {
            cardType = 1;
        } else if (inputId.startsWith('spnm')) {
            cardType = 2;
        } else if (inputId.startsWith('trnm')) {
            cardType = 3;
        } else if (inputId.startsWith('exnm')) {
            cardType = 4;
        }

        // Effectuer la requête AJAX pour récupérer les résultats
        $.ajax({
            url: '../php/deck.php',
            method: 'POST',
            data: {keyword: keyword, cardType: cardType},
            success: function(response) {
                // Convertir la réponse JSON en tableau JavaScript
                var results = JSON.parse(response);

                // Sélectionner l'élément où afficher les suggestions
                var suggestionsDiv = $('#suggestions_' + inputId);
                console.log(suggestionsDiv);

                // Vider les suggestions actuelles
                suggestionsDiv.empty();

                // Ajouter une div pour chaque résultat de la requête
                results.forEach(function(result) {
                    var suggestionDiv = $('<div>', {
                        class: 'suggestion',
                        text: result
                    });

                    // Ajouter un gestionnaire d'événement au clic sur la suggestion
                    suggestionDiv.click(function(){
                        $('#' + inputId).val(result); // Remplir l'input avec la suggestion
                        suggestionsDiv.empty(); // Vider les suggestions
                        suggestionsDiv.hide(); // Cacher les suggestions
                        updateTotalCounts(); // Mettre à jour les totaux
                    });

                    suggestionsDiv.append(suggestionDiv);
                });

                // Afficher ou cacher les suggestions en fonction des résultats
                if (results.length > 0) {
                    suggestionsDiv.show(); // Afficher les suggestions
                } else {
                    suggestionsDiv.hide(); // Cacher les suggestions
                }

                updateTotalCounts(); // Mettre à jour les totaux
            }
        });
    });
});

$(document).ready(function(){
    $('#registerDeck').click(function(event) {
        event.preventDefault(); // Empêcher le formulaire de se soumettre automatiquement

        var deckName = $('#deck_name').val(); // Récupérer le nom du deck
        var mainDeckCount = 0;
        var extraDeckCount = 0;
        var hasError = false;

        // Calculer le nombre de cartes dans le main deck et l'extra deck
        $('[id^=monum_], [id^=spnum_], [id^=trnum_]').each(function() {
            var quantity = parseInt($(this).val());
            if (!isNaN(quantity)) {
                mainDeckCount += quantity;
            }
        });

        $('[id^=exnum_]').each(function() {
            var quantity = parseInt($(this).val());
            if (!isNaN(quantity)) {
                extraDeckCount += quantity;
            }
        });

        // Vérifier les contraintes sur le nombre de cartes
        if (mainDeckCount < 40 || mainDeckCount > 60) {
            $('#error_message').text("Le deck principal doit contenir entre 40 et 60 cartes.").show();
            hasError = true;
        } else if (extraDeckCount > 15) {
            $('#error_message').text("L'extra deck ne peut pas contenir plus de 15 cartes.").show();
            hasError = true;
        } else {
            $('#error_message').hide();
        }

        // Si aucune erreur, enregistrer le deck
        if (!hasError) {
            // Effectuer la requête SQL pour insérer le deck dans la table "deck"
            $.ajax({
                url: '../php/save_deck.php', // Chemin vers le script PHP pour l'enregistrement du deck
                method: 'POST',
                data: {deckName: deckName},
                success: function(deckId) {
                    // Une fois que le deck est enregistré, enregistrer les cartes dans le deck
                    console.log(deckId);
                    saveCards(deckId);
                }
            });
        }
    });

    // Fonction pour enregistrer les cartes dans le deck
    function saveCards(deckId) {
        console.log("Debug 0");
        // Parcourir chaque type de carte (monstres, magies, pièges, extra deck)
        $('[id^=monm_], [id^=spnm_], [id^=trnm_], [id^=exnm_]').each(function() {
            var inputId = $(this).attr('id');
            var cardName = $(this).val(); // Récupérer le nom de la carte
            var quantityId = inputId.replace('nm', 'num');
            var quantity = $('#' + quantityId).val(); // Récupérer la quantité de la carte

            console.log("inputId: " + inputId);
            console.log("cardName: " + cardName);
            console.log("quantityId: " + quantityId);
            console.log("quantity: " + quantity);

            if (cardName && quantity) {
                // Effectuer la requête SQL pour insérer la carte dans la table "deck_card"
                $.ajax({
                    url: '../php/save_card.php', // Chemin vers le script PHP pour l'enregistrement de la carte
                    method: 'POST',
                    data: {deckId: deckId, cardName: cardName, quantity: quantity},
                    success: function(response) {
                        console.log('Carte enregistrée avec succès.');
                    }
                });
            }
        });
    }

    // Mise à jour du total des cartes et validation de l'entrée pour n'accepter que 0, 1, 2, 3
    function updateCardCounts() {
        var mainDeckCount = 0;
        var extraDeckCount = 0;

        $('[id^=monum_], [id^=spnum_], [id^=trnum_]').each(function() {
            var quantity = parseInt($(this).val());
            if (!isNaN(quantity)) {
                mainDeckCount += quantity;
            }
        });

        $('[id^=exnum_]').each(function() {
            var quantity = parseInt($(this).val());
            if (!isNaN(quantity)) {
                extraDeckCount += quantity;
            }
        });

        $('#total_deck_count').text(mainDeckCount);
        $('#total_extra_deck_count').text(extraDeckCount);
    }

    $('.nb_card').on('input', function() {
        var value = $(this).val();
        if (!/^[0-3]$/.test(value)) {
            $(this).val('');
        }
        updateCardCounts();
    });

    $('.nb_card').each(function() {
        updateCardCounts();
    });
});

$(document).ready(function() {
    $('#createTestDeck').click(function() {
        console.log("ok");
        $.ajax({
            url: '../php/create_test_deck.php',
            method: 'POST',
            success: function(response) {
                console.log(response);
                window.location.replace('../page/accueil.php');
            }
        });
    });
});
