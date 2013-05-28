<?php

class App {

    static function liste_archives() {
        $files = scandir("./archives");
        $files = array_reverse($files);
        echo '<ul>';
        foreach ($files as $value) {
            if ($value != "." && $value != "..")
                echo '<a href="./archives/' . urlencode($value) . '" target="_blank"><li class="arch">' . utf8_decode ($value) . "</li></a>";
        }
        echo '</ul>';
    }

}

?>
