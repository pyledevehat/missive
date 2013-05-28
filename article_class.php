<?php



class Article {

    var $lettre;
    var $html_doc;
    var $table_node;
    var $controls_node;

    function Article($titre, $contenu, $lien) {

        $this->lettre = new Lettre();
        $this->html_doc = $this->lettre->html_doc;
        $id = uniqid();

        $this->set_controls($id);
        $this->set_content($id, $titre, $contenu, $lien);
    }

    function set_controls($id) {
        // Contrôles de l'article
        // Checkbox de sélection
        $input_node = $this->html_doc->createElement("input");
        $input_node->setAttribute("type", "checkbox");
        $input_node->setAttribute("name", "suppr[]");
        $input_node->setAttribute("value", $id);

        // Bouton modifier
        $mod = $this->html_doc->createElement("input");
        $mod->setAttribute("type", "submit");
        $mod->setAttribute("name", "mod" . $id);
        $mod->setAttribute("value", "Modifier");
        
        // Flèche monter
        $monter = $this->html_doc->createElement("input");
        $monter->setAttribute("type", "submit");
        $monter->setAttribute("name", "move" . $id);
        $monter->setAttribute("value", "Monter");

        // Flèche descendre
        $descendre = $this->html_doc->createElement("input");
        $descendre->setAttribute("type", "submit");
        $descendre->setAttribute("name", "move" . $id);
        $descendre->setAttribute("value", "Descendre");

        $controls_node = $this->html_doc->createElement("span");
        $controls_node->setAttribute("class", "controls");

        $controls_node->appendChild($input_node);
        $controls_node->appendChild($mod);
        $controls_node->appendChild($monter);
        $controls_node->appendChild($descendre);

        $this->controls_node = $controls_node;
    }

    function set_content($id, $titre, $contenu, $lien) {

        $br_node = $this->html_doc->createElement("br");
        // Première cellule
        $titre_txt_node = $this->html_doc->createTextNode($titre);
        $font1_node = $this->html_doc->createElement("font");
        $font1_node->appendChild($titre_txt_node);
        $font1_node->setAttribute("face", "Times New Roman, Times, serif");
        $font1_node->setAttribute("style", "font-size: 1.6em;");
        $b_node = $this->html_doc->createElement("b");
        $b_node->appendChild($font1_node);
        $td1_node = $this->html_doc->createElement("td");
        $td1_node->appendChild($b_node);
        $td1_node->setAttribute("style", "width: 100%; background-color: #b29ebc;");
        $tr1_node = $this->html_doc->createElement("tr");
        $tr1_node->appendChild($td1_node);
        $tr1_node->appendChild($br_node);

        // Deuxième cellule
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadHTML($contenu);
        $body = $doc->getElementsByTagName("body")->item(0);
        $font2_node = $this->html_doc->createElement("font");
        $this->parcours_node($body, $font2_node);
        $font2_node->setAttribute("face", "Times New Roman, Times, serif");
        $font2_node->setAttribute("style", "font-size: 1em;");
        $td2_node = $this->html_doc->createElement("td");
        $td2_node->appendChild($font2_node);
        $td2_node->setAttribute("style", "padding: 8px;");
        $tr2_node = $this->html_doc->createElement("tr");
        $tr2_node->appendChild($td2_node);

        // Tableau ensemble
        $tbody_node = $this->html_doc->createElement("tbody");
        $tbody_node->appendChild($tr1_node);
        $tbody_node->appendChild($tr2_node);
        if ($lien != "") {
            // Troisième cellule
            $savoirplus_txt_node = $this->html_doc->createTextNode("en savoir plus...");
            $a_node = $this->html_doc->createElement("a");
            $a_node->appendChild($savoirplus_txt_node);
            $a_node->setAttribute("href", $lien);
            $a_node->setAttribute("style", "color: black;");
            $font3_node = $this->html_doc->createElement("font");
            $font3_node->appendChild($a_node);
            $font3_node->setAttribute("face", "Times New Roman, Times, serif");
            $i_node = $this->html_doc->createElement("i");
            $i_node->appendChild($font3_node);
            $td3_node = $this->html_doc->createElement("td");
            $td3_node->appendChild($i_node);
            $td3_node->setAttribute("align", "right");
            $tr3_node = $this->html_doc->createElement("tr");
            $tr3_node->appendChild($td3_node);
            $tr3_node->appendCHild($br_node);
            $tbody_node->appendChild($tr3_node);
        }

        $table_node = $this->html_doc->createElement("table");
        $table_node->appendChild($tbody_node);
        $table_node->setAttribute("id", $id);
        $table_node->setAttribute("class", "arts");
        $table_node->setAttribute("cellspacing", "2");
        $table_node->setAttribute("cellpadding", "4");
        $table_node->setAttribute("border", "0");
        $table_node->setAttribute("style", "width: 100%;");

        $this->table_node = $table_node;
    }

    function get_controls() {
        return $this->controls_node;
    }

    function get_content() {
        return $this->table_node;
    }

    function parcours_node($node_in, $node_out) {
        if ($node_in->hasChildNodes()) {
            $childs = $node_in->childNodes;
            foreach ($childs as $n) {
                $cloned = $n->cloneNode(true);
                $node_out->appendChild($this->html_doc->importNode($cloned, true));
            }
        }
    }

}

?>
