
$(document).ready(function() {
    
    var wait_delay = 1000;
    var anim_time = 2000;
    var id_modif = "";
    
    // Affichage de la lettre en cours dans la zone d'affichage dédiée'
    function affLettre() {
        $.get('draft.html', function(donnees){
            $("#lettre").html(donnees);
        });
    }
    
    // Grise l'élément passé en paramètre pendant wait_delay
    function affWraper(el) {
        $(el).append('<div class="wrapper"><img class="wait" src="./img/wait.gif" /></div>');
        $(".wrapper").delay(wait_delay).queue(function() {
            $(this).remove();
        });
    }
    
    // Mise à zéro des champs de formulaire de la zone article
    function mazArticle() {
        $("#ajout_article").find(":input").not(":submit").each(function() {
            $(this).val('');
        });
        $('#f2 iframe').contents().find('body').each(function() {
            $(this).html('<br>');
        });
    }
    
    // Changement de l'apparence du titre du formulaire 
    // cliqué et déploiement du formulaire correspondant
    function setLegendShow(el) {
        el.css("background-color", "white");
        el.css("border-style", "double");
        el.css("border-color", "red");
        el.css("border-width", "1px");
        el.next("form").show(anim_time);
    }
    
    $("#archive_wrapper, #archive_win").hide(); // On cache la fenêtre 
                                                // d'affichage des archives
    // Initialisation des différentes zones
    $("form:not(:first)").hide();
    setLegendShow($(".legend:first"));
    $(".legend").click(function() {
        if($(this).next("form").is(":visible")) {
            $(this).css("background-color", "#aaaaaa");
            $(this).css("border-color", "white #666666 #666666 white");
            $(this).css("border-style", "solid");
            $(this).css("border-width", "2px");
            $(this).next("form").hide(anim_time);
        } else {
            setLegendShow($(this));
        }
                    
    });
     
    // Ajout ou modification d'article 
    $("#ajout_article").submit(function(e) {
        affWraper('#f1');
        affWraper('#f2');
        e.preventDefault();
        var titre = $('#f2 :input[name=titre]').val();
        var contenu = $('#f2 iframe').contents().find('body').html();
        contenu = '<body>' + contenu + '</body>';
        var lien = $('#f2 :input[name=lien]').val();
        var donnees = {
            titre: titre,
            contenu: contenu,
            lien: lien
        };
        // Si id_modif a été renseigné, on a affaire à une modification
        if(id_modif != "") { 
            $('#b2 h2').html("Ajout d'un article");
            donnees.id = id_modif;
            id_modif = "";
        } 
        $.ajax({
            url : 'control.php',
            type : 'POST',
            dataType : 'text',
            data : donnees,
            success : function(data) {
                affLettre();
                mazArticle();
            },
            error : function (res, statut, erreur) {
                alert('Loupé !');
            }
        });
    });
    
    
    // Envoi de la lettre
    $("#envoi_lettre").submit(function(e) {
        affWraper('#f1');
        affWraper('#f3');
        e.preventDefault();
        var adrs = [];
        $(":checkbox:checked[name=listes\\[\\]]").each(function() {
            adrs.push($(this).val());
        });
        var suj = $(":text[name=sujet]").val();
        $.ajax({
            url : 'control.php',
            type : 'POST',
            dataType : 'text',
            data : {
                lettre: "",
                sujet: suj,
                listes: adrs
            },
            success : function(data) {
                $("#envoi_lettre").find(":text[name=sujet]").each(function() {
                    $(this).val('');
                });
                $("#envoi_lettre").find(":checkbox:checked").each(function() {
                    $(this).attr('checked', false);
                });
                affLettre();
                $.post('control.php', { // On récupère et affiche la nouvelle
                    archives: ""        // liste des archives.
                }, function(donnees){
                    $("#archives").html(donnees);
                });
                
            },
            error : function (res, statut, erreur) {
                alert('Loupé !');
            }
        });
    });
    
    
    // Pour toutes les actions du formulaire lettre, 
    // griser et annuler l'événement par défaut
    $("#lettre_controls").submit(function(e) {
        affWraper('#f1');
        e.preventDefault();
    });

    // Monter ou descendre un élément dans la liste des articles
    $("#lettre").on('click', '.controls :submit[value!="Modifier"]', function() {
        var id = $(this).parent().children(":checkbox").attr('value');
        var value = $(this).attr('value');
        
        $.ajax({
            url : 'control.php',
            type : 'POST',
            dataType : 'text',
            data : {
                deplace: "",
                idmove: id,
                move: value
            },
            success : function(data) {
                affLettre()
            },
            error : function (res, statut, erreur) {
                alert('Loupé !');
            }
        });
    });
    
    // Modifier un article
    $("#lettre").on('click', '.controls :submit[value="Modifier"]', function() {
        var id = $(this).parent().children(":checkbox").attr('value');
        var art = $(this).parent().parent().find('.arts[id="' + id + '"] tbody').first();
        var titre = art.find('tr td b font').first().html();
        var contenu = art.find('tr:nth-child(2) td font').first().html();
        var lien = "";
        if (art.find('tr').size() == 3) {
            var lien = art.find('tr:nth-child(3) td i font a').first().attr('href');
        }

        var res = true;
        // Si un champ de la zone article n'est pas vide, on demande d'abord
        // si l'utilisateur veut abandonner l'article en cours
        if($('#f2 input[name="titre"]').val() != "" || 
            $('#f2 iframe').contents().find('body').html() != "<br>" ||
            $('#f2 input[name="lien"]').val() != "") {
            res = confirm("La modification d'un article entrainera\r\n\
la perte de l'article en cours de r\351daction.\r\n\
\312tes-vous certain-e de vouloir faire \347a ?")
        }
        
        if(res) {
            $('#b2 h2').html("Modification d'un article");
            $('#f2 input[name="titre"]').val(titre);
            $('#f2 iframe').contents().find('body').html(contenu);
            $('#f2 input[name="lien"]').val(lien);
            if($('#ajout_article').is(":hidden")) {
                setLegendShow($('#b2'));
            }
            affWraper('#f2');
            id_modif = id;
        }
    });
    
    // Suppression d'articles
    $("#del").click(function() {
        var supprimes = [];
        $(":checkbox:checked[name=suppr\\[\\]]").each(function() {
            supprimes.push($(this).val());
        });

        $.ajax({
            url : 'control.php',
            type : 'POST',
            dataType : 'text',
            data : {
                del: "",
                suppr: supprimes
            },
            success : function(data) {
                affLettre();
            },
            error : function (res, statut, erreur) {
                alert('Loupé !');
            }
        });
            
    });
    
    // Mise à zéro de la lettre
    $("#maz").click(function() {
        $.ajax({
            url : 'control.php',
            type : 'POST',
            dataType : 'text',
            data : {
                del: "",
                suppr: "all"
            },
            success : function(data) {
                affLettre();
            },
            error : function (res, statut, erreur) {
                alert('Loupé !');
            }
        });
            
    });
    
    // Ouverture d'une archive'
    $("#archives").on('click', 'a', function() {
        $("#archive_wrapper, #archive_win").show();
        var link = $(this).attr('href');
        $("#archive_content").load(link);
        return false;
    });
    
    // Fermeture de l'archive ouverte'
    $("#close").click(function() {
        $("#archive_wrapper, #archive_win").hide();
        return false;
    });
          
});
