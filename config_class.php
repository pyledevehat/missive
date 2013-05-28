<?php

class Config {
    var $xml_doc;
    function Config() {
        $this->xml_doc = new DOMDocument('1.0', 'utf-8');
        $this->xml_doc->formatOutput = true;
        if (file_exists("config.xml")) {
            $this->xml_doc->loadXMLFile("config.xml");
        } else {
            $this->newFile();
        }
        
        
    }
    
    function newFile() {
        // La racine
        $root = $this->xml_doc->createElement("config");
        $this->xml_doc->appendChild($root);
        
        // La section adresses
        $adresses = $this->xml_doc->createElement("adresses");
        $root->appendChild($adresses);
        
        // Le titre
        $titre = $this->xml_doc->createElement("titre");
        $root->appendChild($titre);
        
        // Le sous-titre
        $sous_titre = $this->xml_doc->createElement("soustitre");
        $root->appendChild($sous_titre);
        
        // Le pied de page
        $pied_page = $this->xml_doc->createElement("pieddepage");
        $root->appendChild($pied_page);
        
        $this->xml_doc->save("config.xml");
    }
    
    function addAdress($adr) {
        $adresses = $this->xml_doc->getElementsByTagName("adresses");
        $adr_node = $this->xml_doc->createElement("adr");
        $adr_txt = $this->xml_doc->createTextNode($adr);
        $adr_node->appendChild($adr_txt);
        $adresses->item(0)->appendChild($adr_node);
        $this->xml_doc->save("config.xml");
    }
    
    function removeAdress($adr) {
        $domxpath = new DOMXPath($this->xml_doc);
        $nodes = $domxpath->query('/config/adresses/adr[text()="' . $adr . '"]');
        $adresses = $this->xml_doc->getElementsByTagName("adresses");
        $adresses->item(0)->removeChild($nodes->item(0));
    }
}

?>
