<?php


require 'lettre_class.php';
require 'app_class.php';

$doc = new Lettre();
$move = "";
$id_move = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Modification d'article, on a reçu un id en plus d'un titre, un contenu
    // et optionellement un lien.
    if(isset($_POST['id'])) {
        $id = $_POST['id'];
        $titre = stripslashes(htmlspecialchars($_POST['titre']));
        $contenu = stripslashes(utf8_decode($_POST['contenu']));
        if (isset($_POST['lien'])) {
            $lien = $_POST['lien'];
        } else {
            $lien = "";
        }
        $doc->mod_article($id, $titre, $contenu, $lien);
    // Sinon, ajout d'article, on n'a pas reçu d'id mais seulement un titre, 
    // un contenu et optionellement un lien.
    } elseif (isset($_POST['titre']) && isset($_POST['contenu'])) {
        $titre = stripslashes(htmlspecialchars($_POST['titre']));
        $contenu = stripslashes(utf8_decode($_POST['contenu']));
        if (isset($_POST['lien'])) {
            $lien = $_POST['lien'];
        } else {
            $lien = "";
        }

        $doc->add_article($titre, $contenu, $lien);
    // Envoi de la lettre, on a reçu une variable lettre vide, 
    // un sujet et un tableau d'adresses.
    } elseif (isset($_POST['lettre'])) {
        $sujet = stripslashes(htmlspecialchars($_POST['sujet']));
        $listes = $_POST['listes'];

        $doc->send_lettre($sujet, $listes);

        $doc = new Lettre();
    // Supression de un ou plusieurs articles, on a reçu une variable del 
    // vide ainsi qu'un tableau des id des articles à supprimer
    } elseif (isset($_POST['del'])) {
        $arts = $_POST['suppr'];
        $doc->suppr_articles($arts);
    // Mise à zéro de la lettre, on a reçu une variable maz vide    
    } elseif (isset($_POST['maz'])) {
        $doc->suppr_articles("all");
    // Affichage des archives, on a reçu une variable archives vide
    } elseif (isset($_POST['archives'])) {
        App::liste_archives();
    // Déplacement d'un article, on a reçu une varible deplace vide, idmove, 
    // l'identifiant de l'article à bouger et move, la nature du mouvement
    } elseif (isset($_POST['deplace'])) {
        $id_move = $_POST['idmove'];
        $move = $_POST['move'];
        $doc->move_art($id_move, $move);
    } else {
        echo '<h1>Vous ne drevriez pas atterrir sur cette page !</h1>';
    }
} else {
    echo '<h1>Vous ne drevriez pas atterrir sur cette page !</h1>';
}

?>
