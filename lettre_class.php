<?php


include 'article_class.php';
include 'smtpclient_class.php';

class Lettre {

    var $html_doc;

    function Lettre() {
        $this->html_doc = new DOMDocument('1.0', 'utf-8');
        $this->html_doc->formatOutput = true;


        if (file_exists("draft.html")) {
            $this->html_doc->loadHTMLFile("draft.html");
        } else {
            $this->new_file();
        }
    }

    function new_file() {

        $skel = utf8_decode('<table cellspacing="0" cellpadding="0" border="0" style="width: 60%;">
                        <tbody>
                            <tr>
                                <td align="center"><img id="bandeau" src="./img/bandeau.gif"
                                                        alt="Lettre d\'infos de test" /><br />
                                </td>
                            </tr>
                            <tr>
                                <td cellpadding="3px"><b><tt>Description du bulletin - Édition n°<span id="numero">' . $this->count_files("./archives/") . '</span> du <span id="date"></span></tt></b><br />
                                    <br />
                                </td>
                            </tr>
                            <tr>
                                <td id="articles">
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="background-color: black;">
                                    <font face="Times New Roman, Times, serif" color="#ffffff"><b><h5>
                                            Adresse et coordonnées<br />                    
                                            pied de page<br />
                                            <a href="http://blog.developpez.com/stlenn" style="color:white;">http://blog.developpez.com/stlenn</a>
                                        </h5></b></font>
                                </td>
                            </tr>
                        </tbody>
                    </table>');
        $this->html_doc->loadHTML($skel);

        $this->html_doc->saveHTMLFile("draft.html");
    }

    /*
     * Ajoute un article dans le fichier draft.html
     */

    function add_article($titre, $contenu, $lien) {

        if ((preg_match("#^http://#", $lien) != 1) && ($lien != ""))
            $lien = "http://" . $lien;
        $art = new Article($titre, $contenu, $lien);

        $arts_node = $this->html_doc->getElementById("articles");
        $arts_node->appendChild($this->html_doc->importNode($art->get_controls(), true));
        $arts_node->appendChild($this->html_doc->importNode($art->get_content(), true));

        $this->validate_arts();

        $this->html_doc->saveHTMLFile("draft.html");
    }


    function mod_article($id, $titre, $contenu, $lien) {
        $arts = $this->html_doc->getElementById("articles");
        $domxpath = new DomXPath($this->html_doc);
        $span_nodes = $domxpath->query("//span[@class='controls']", $arts);
        $art_nodes = $domxpath->query("//table[@class='arts']", $arts);
        // On crée un tableau de tableaux à deux éléments contenant les clones des span et des tables de la zone d'article.
        $tab_arts = array();
        for ($i = 0; $i < $art_nodes->length; $i++) {
            $tab_arts[$i] = array($span_nodes->item($i)->cloneNode(true), $art_nodes->item($i)->cloneNode(true));
        }

        // On cherche en quelle position est l'article à modifier
        $el = 0;
        for ($i = 0; $i < $art_nodes->length; $i++) {
            if ($art_nodes->item($i)->getAttribute("id") == $id) {
                $el = $i;
                break;
            }
        }

        // On supprime les articles actuels
        $this->suppr_articles("all");

        $i = 0;
        do {
            if ($i == $el) {
                if ((preg_match("#^http://#", $lien) != 1) && ($lien != ""))
                    $lien = "http://" . $lien;
                $art = new Article($titre, $contenu, $lien);

                $arts_node = $this->html_doc->getElementById("articles");
                $arts_node->appendChild($this->html_doc->importNode($art->get_controls(), true));
                $arts_node->appendChild($this->html_doc->importNode($art->get_content(), true));
            } else {
                $val = current($tab_arts);
                $arts->appendChild($this->html_doc->importNode($val[0], true));
                $arts->appendChild($this->html_doc->importNode($val[1], true));
            }
            $i++;
        } while (next($tab_arts));

        $this->validate_arts();

        $this->html_doc->saveHTMLFile("draft.html");
    }

    function suppr_articles($arts) {
        $domxpath = new DomXPath($this->html_doc);
        $article_node = $this->html_doc->getElementById("articles");
        if ($arts == "all") {
            $nodes = $article_node->childNodes;
            $i = 0;
            $nodes_to_remove = array();
            while ($node = $nodes->item($i++)) {
                $nodes_to_remove[] = $node;
            }
            foreach ($nodes_to_remove as $node) {
                $article_node->removeChild($node);
            }
        } else {
            foreach ($arts as $art) {
                $filtered = $domxpath->query("//input[@value='" . $art . "']", $article_node);
                $input = $filtered->item(0);
                $parent = $input->parentNode;
                $parent->parentNode->removeChild($parent);
                $art_node = $this->html_doc->getElementById($art);
                $art_node->parentNode->removeChild($art_node);
            }

            $this->validate_arts();
        }

        $this->html_doc->saveHTMLFile("draft.html");
    }

    function move_art($id, $move) {
        $arts = $this->html_doc->getElementById("articles");
        $domxpath = new DomXPath($this->html_doc);
        $span_nodes = $domxpath->query("//span[@class='controls']", $arts);
        $art_nodes = $domxpath->query("//table[@class='arts']", $arts);
        // On crée un tableau de tableaux à deux éléments contenant les clones des span et des tables de la zone d'article.
        $tab_arts = array();
        for ($i = 0; $i < $art_nodes->length; $i++) {
            $tab_arts[$i] = array($span_nodes->item($i)->cloneNode(true), $art_nodes->item($i)->cloneNode(true));
        }

        // On cherche en quelle position est l'article à bouger
        $el = 0;
        for ($i = 0; $i < $art_nodes->length; $i++) {
            if ($art_nodes->item($i)->getAttribute("id") == $id) {
                $el = $i;
                break;
            }
        }

        // On supprime les articles actuels
        $this->suppr_articles("all");

        // On replace les clones dans le nouvel ordre
        if ($move == "Monter") {
            $i = 0;
            do {
                if ($i == ($el - 1)) {
                    next($tab_arts);
                    $val = current($tab_arts);
                    $arts->appendChild($this->html_doc->importNode($val[0], true));
                    $arts->appendChild($this->html_doc->importNode($val[1], true));

                    prev($tab_arts);
                    $val = current($tab_arts);
                    $arts->appendChild($this->html_doc->importNode($val[0], true));
                    $arts->appendChild($this->html_doc->importNode($val[1], true));
                    $i++;
                    next($tab_arts);
                } else {
                    $val = current($tab_arts);
                    $arts->appendChild($this->html_doc->importNode($val[0], true));
                    $arts->appendChild($this->html_doc->importNode($val[1], true));
                }
                $i++;
            } while (next($tab_arts));
        } elseif ($move == "Descendre") {
            $i = 0;
            do {
                if ($i == $el) {
                    next($tab_arts);
                    $val = current($tab_arts);
                    $arts->appendChild($this->html_doc->importNode($val[0], true));
                    $arts->appendChild($this->html_doc->importNode($val[1], true));

                    prev($tab_arts);
                    $val = current($tab_arts);
                    $arts->appendChild($this->html_doc->importNode($val[0], true));
                    $arts->appendChild($this->html_doc->importNode($val[1], true));
                    $i++;
                    next($tab_arts);
                } else {
                    $val = current($tab_arts);
                    $arts->appendChild($this->html_doc->importNode($val[0], true));
                    $arts->appendChild($this->html_doc->importNode($val[1], true));
                }
                $i++;
            } while (next($tab_arts));
        } else {
            echo "Move : " . $move . "<br />";
            echo "Id : " . $id;
        }

        $this->validate_arts();

        $this->html_doc->saveHTMLFile("draft.html");
    }

    /*
     * Envoie le mail et déplace le fichier HTML dans archives après l'avoir renommé de façon adéquate.
     */

    function send_lettre($sujet, $adrs) {


        include 'smtpconfig.php';
        // D'abord, on supprime les contrôles

        $article_node = $this->html_doc->getElementById("articles");
        $domxpath = new DomXPath($this->html_doc);
        $nodes = $domxpath->query("//span[@class='controls']");
        $i = 0;
        $nodes_to_remove = array();
        while ($node = $nodes->item($i++)) {
            $nodes_to_remove[] = $node;
        }
        foreach ($nodes_to_remove as $node) {
            $article_node->removeChild($node);
        }

        // On change les images vers les sources cid:
        // Le bandeau
        $band = $this->html_doc->getElementById("bandeau");
        $band->removeAttribute("src");
        $band->setAttribute("src", "cid:bandeau.gif");

        // On inscrit la date
        $date = $this->html_doc->getElementById("date");
        $txt_date = $this->html_doc->createTextNode(date("d/m/Y"));
        $date->appendChild($txt_date);

        // Écriture des modifications
        $this->html_doc->saveHTMLFile("draft.html");

        // Puis on envoie la lettre
        $res = fopen("draft.html", "r");
        $body = fread($res, filesize("draft.html"));
        fclose($res);
        $SMTPMail = new SMTPClient($SmtpServer, $SmtpPort, $from, $adrs, $sujet, $body);
        $SMTPMail->SendMail();


        // On remodifie les images pour que les chemins correspondent après déplacement
        // Le bandeau
        $band->removeAttribute("src");
        $band->setAttribute("src", "./img/bandeau.gif");

        $this->html_doc->saveHTMLFile("draft.html");

        // On déplace vers les archives et on renomme le draft
        $sujet = str_replace(" ", "_", $sujet);
        rename("draft.html", "./archives/" . date("d-m-Y_H\hi") . "_" . $sujet . ".html");
    }

    function count_articles() {
        $arts = $this->html_doc->getElementById("articles");
        $ens_arts = $arts->getElementsByTagName("table");
        $nbr_articles = $ens_arts->length;
        return $nbr_articles;
    }

    function count_files($rep) {
        if (substr($rep, -1) != '/')
            $rep .= '/';

        $dir = @opendir($rep);
        if (!$dir)
            return -1;

        $nb_files = 0;

        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..')
                continue;

            if (is_dir($rep . $file))
                continue;

            $nb_files++;
        }

        closedir($dir);
        return $nb_files;
    }

    function validate_arts() {
        $arts = $this->html_doc->getElementById("articles");
        $domxpath = new DomXPath($this->html_doc);
        $buttons = $domxpath->query("//input[@type='submit']", $arts);

        foreach ($buttons as $b) {
            if ($b->hasAttribute("disabled"))
                $b->removeAttribute("disabled");
        }
        if ($buttons->length != 0) {
            $buttons->item(1)->setAttribute("disabled", "disabled");
            $buttons->item($buttons->length - 1)->setAttribute("disabled", "disabled");
        }
    }

    static function to_text_ver($html_body) {
        $doc = new Lettre();

        $txt_body = "LETTRE D'INFOS DE TEST\r\n";
        $txt_body .= "=====================\r\n";

        $num = $doc->html_doc->getElementById("numero")->nodeValue;
        $date = $doc->html_doc->getElementById("date")->nodeValue;

        $txt_body .= "Description lettre d'info - Édition n°" . $num . " du " . $date . "\r\n\r\n\r\n";

        $arts = $doc->html_doc->getElementById("articles");
        $arts_nodes = $arts->getElementsByTagName("tbody");
        foreach ($arts_nodes as $art) {
            $fonts = $art->getElementsByTagName("font");
            $txt_body .= $fonts->item(0)->nodeValue . "\r\n";
            for ($i = 0; $i < strlen($fonts->item(0)->nodeValue); $i++)
                $txt_body .= "-";
            $txt_body .= "-\r\n";
            $txt_body .= $fonts->item(1)->nodeValue . "\r\n";
            if ($fonts->length == 3) {
                $txt_body .= "\r\n";
                $txt_body .= "en savoir plus : " . $fonts->item(2)->firstChild->getAttribute("href") . "\r\n";
            }
            $txt_body .= "\r\n\r\n";
        }

        $txt_body .= "Contact pied de page\r\n";
        $txt_body .= "avec adresse\r\n";
        $txt_body .= "http://blog.developpez.com/stlenn\r\n\r\n";
        return $txt_body;
    }

}

?>
