<?php

class SMTPClient {

    function SMTPClient($SmtpServer, $SmtpPort, $from, $to, $subject, $body) {

        $this->SmtpServer = $SmtpServer;
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;

        if ($SmtpPort == "") {
            $this->PortSMTP = 25;
        } else {
            $this->PortSMTP = $SmtpPort;
        }
    }

    function SendMail() {

        $talk = "";
        if ($SMTPIN = fsockopen($this->SmtpServer, $this->PortSMTP)) {

            fputs($SMTPIN, "EHLO " . $this->SmtpServer . "\r\n");

            fputs($SMTPIN, "ETRN localhost\r\n");

            fputs($SMTPIN, "MAIL FROM:<" . $this->from[1] . ">\r\n");

            // Gestion des adresses multiples
            foreach ($this->to as $a) {
                fputs($SMTPIN, "RCPT TO:<" . $a . ">\r\n");
            }

            fputs($SMTPIN, "DATA\r\n");

            // On génère les boundary
            $boundary_alt = uniqid("--boundary--", true);

            // En tête du mail
            $sujet = '=?UTF-8?B?' . base64_encode($this->subject) . '?=';
            $from_name = '=?UTF-8?B?' . base64_encode($this->from[0]) . '?=';
            $contenu = 'Subject:' . $sujet . "\r\n";
            $contenu .= 'From:' . $from_name . "<" . $this->from[1] . ">\r\n";
            $contenu .= 'Reply-To:' . $from_name . "<" . $this->from[1] . ">\r\n";
            $contenu .= 'To:' . $from_name . "<" . $this->from[1] . ">\r\n";
            $contenu .= 'MIME-Version: 1.0' . "\r\n";
            $contenu .= 'Content-Type: multipart/alternative; boundary="' . $boundary_alt . '"' . "\r\n\r\n\r\n";


            $contenu .= '--' . $boundary_alt . "\n";

            // Contenu en texte
            $contenu .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
            $contenu .= 'Content-Transfer-Encoding: 8bit' . "\r\n\r\n\r\n";

            $contenu .= Lettre::to_text_ver($this->body) . "\r\n\r\n\r\n";
            
            $contenu .= '--' . $boundary_alt . "\n";

            // Contenu en html
            $contenu .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $contenu .= 'Content-Transfer-Encoding: 8bit' . "\r\n\r\n\r\n";

            $contenu .= $this->body . "\r\n\r\n";

            $contenu .= '--' . $boundary_alt . "\n";

            // Pièce jointe
            $contenu .= 'Content-Type: image/gif; name="bandeau.gif"' . "\r\n";
            $contenu .= 'Content-Transfer-Encoding: base64' . "\r\n";
            $contenu .= 'Content-Disposition: attachment; filename="bandeau.gif"' . "\r\n";
            $contenu .= 'Content-ID: <bandeau.gif>' . "\r\n\r\n\r\n";

            fputs($SMTPIN, $contenu);
            $bandeau = file_get_contents("img/bandeau.gif");
            $bandeau = chunk_split(base64_encode($bandeau));
            fputs($SMTPIN, "\r\n" . $bandeau . "\r\n\r\n\r\n");

            $contenu = "\r\n" . '--' . $boundary_alt . "--\n";

            $contenu .= "\r\n.\r\n";

            fputs($SMTPIN, $contenu);
            sleep(1);

            //CLOSE CONNECTION AND EXIT ...
            fputs($SMTPIN, "QUIT\r\n");

            fclose($SMTPIN);
        }
        return $talk;
    }

}

?>
