<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Missive - Gestion de lettre d'info</title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
        <script src="newsletter.js"></script>
        <link rel="stylesheet" href="./tinyeditor/tinyeditor.css">
        <script src="./tinyeditor/tiny.editor.packed.js"></script>
        <link rel="stylesheet" href="newsletter.css" type="text/css">
    </head>
    <?php
    include 'lettre_class.php';
    include 'app_class.php';
    $doc = new Lettre();
    ?>
    <body style="padding: 50px;">
        
        <h1>Missive - Gestion de lettre d'infos</h1>

        <button id="b1" class="legend" type="button"><h2>Lettre en cours</h2></button>
        <form id="lettre_controls" action="control.php" method="POST" accept-charset="UTF-8">
            <fieldset id="f1">
                <div id="lettre" style="background-color: white; position: relative; display: block; overflow: auto; height: 400px; width: 650px; padding: 20px;">
                    <?php include 'draft.html'; ?>
                </div>
                <br/>

                <input id="del" type="submit" name="del" value="Supprimer les articles s&eacute;lectionn&eacute;s">&nbsp;&nbsp;<input id="maz" type="submit" name="maz" value="Abandonner la lettre d'infos"/>
            </fieldset>
        </form>
        <br/>
        <br/>


        <button id="b2" class="legend" type="button"><h2>Ajout d'un article</h2></button>
        <form id="ajout_article" action="control.php" method="POST"  accept-charset="UTF-8">
            <fieldset id="f2">
                Titre :<br />
                <input type="text" name="titre" style="width: 500px;"/><br/>
                Contenu :<br />
                <textarea id="tinyeditor" name="contenu" style="width: 400px; height: 200px;"></textarea><br/>
                Lien (optionnel) :<br/>
                <input type="text" name="lien" style="width: 500px;"/><br/><br/>
                <input type="submit" name="article" value="Envoyer"/>
            </fieldset>
        </form>       
        <br/>
        <br/>
        <button id="b3" class="legend" type="button"><h2>Envoi de la lettre d'info</h2></button>
        <form id="envoi_lettre" name="lettre" action="control.php" method="POST"  accept-charset="UTF-8">
            <fieldset id="f3">
                Sujet :<br/>
                <input type="text" name="sujet" style="width: 500px;"><br/>
                <input type="checkbox" name="listes[]" value="adr1@gmail.com"/>Adresse 1
                &nbsp;&nbsp;
                <input type="checkbox" name="listes[]" value="adr2@gmail.com"/>Adresse 2
                &nbsp;&nbsp;
                <input type="checkbox" name="listes[]" value="py.ledevehat@laposte.net"/>Pierre-Yves Le Dévéhat
                <br/>
                <input type="checkbox" name="listes[]" value="adr3@gmail.com"/>Adresse 3
                &nbsp;&nbsp;
                <input type="checkbox" name="listes[]" value="adr4@gmail.com"/>Adresse 4
                <br/>
                <input type="submit" name="lettre" value="Envoyer"/>
            </fieldset>
        </form>
        <br/> 
        <br/>
        <button id="b4" class="legend" type="button"><h2>Archives</h2></button>
        <form>
            <fieldset id="f4">
                <div id="archives" style="position: relative; display: block; overflow: auto; height: 200px; width: 650px; background-color: #aaaaaa; padding: 10px;">
                    <?php
                    App::liste_archives();
                    ?>
                </div>
            </fieldset>
        </form>
        <div id="archive_wrapper"></div>
        <div id="archive_win">
            <a href="" id="close">
                <img src="./img/close.gif" />
            </a><div id="archive_content"></div>
        </div>
        <script>
            var editor = new TINY.editor.edit('editor', {
                id: 'tinyeditor',
                width: 500,
                height: 175,
                cssclass: 'tinyeditor',
                controlclass: 'tinyeditor-control',
                rowclass: 'tinyeditor-header',
                dividerclass: 'tinyeditor-divider',
                controls: ['bold', 'italic', 'underline', 'strikethrough', '|', 'subscript', 'superscript', '|',
                    'orderedlist', 'unorderedlist', '|', 'undo', 'redo', '|', 'link', 'unlink'],
                footer: false,
                fonts: ['Verdana','Arial','Georgia','Trebuchet MS'],
                xhtml: true,
                bodyid: 'editor',
                footerclass: 'tinyeditor-footer',
                toggle: {text: 'source', activetext: 'wysiwyg', cssclass: 'toggle'},
                resize: {cssclass: 'resize'}
            });
        </script>
    </body>
</html>
