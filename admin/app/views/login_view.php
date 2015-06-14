<?php
if (!class_exists('Login_view')) {

    class Login_view {


//ERRORS    
        public function loginErrors($error_nummber, $email = '') {

            switch ($error_nummber) {
                case 1:
                    header("Location:/admin");
                    break;
                case 2:
                    $text = '<div class="feed_fehler">Falsche Daten!</div>';
                    break;
                case 3:
                    $text = '<div class="feed_erfolg">Registration erfolgreich.checken Sie Ihr Postfach<br>'
                            . '<a href="/user/LogMeIn">Zur Login</a></div>';
                    $text .= '<script>
                       $(document).ready(function(){
                            $(".register").remove();
                        });    
                            </script>';
                    break;
                case 4:
                    $text = '<div class="feed_warnung">Feld darf nicht leer sein</div>';

                    break;
                case 5:
                    header("Location:/");
                    $text = '<div class="login_feed_container"><div class="feed_erfolg">Erfolgreich ausgeloggt,Sie werden in <span style="color:black;" class="sek">2</span> Sekunden weitergeleitet</div></div>';
                    break;
                case 6:
                    $text = '<div class="feed_hinweis">Benutzername existiert bereits</div>';

                    break;
                case 7:
                    $text = '<div class="feed_warnung">Passwörter stimmen nicht überein!</div>';

                    break;
                case 8:
                    $text = '<h6>Eingellogt mit Cockies</h6>';
                    break;
                case 9:
                    $text = '<div class="feed_erfolg">Es wurde eine E-mail mit den neuen Zugangsdaten an diese Adresse geschickt : ' . $email . '</div>';
                    break;
                case 10:
                    $text = '<div class="feed_fehler">E-mail Adresse existiert nicht!</div>';
                    break;
                case 11:
                    $text = '<div class="login_feed_container"><div class="feed_hinweis">Sie sind Schon angemeldet</div></div>';
                    break;
                case 12:
                    $text = '<div class="feed_fehler">Bitte geben Sie eine valide Email ein!</div>';
                    break;
                case 13:
                    $text = 'Bitte geben Sie eine valide Email!';
                    break;
                case 14:
                    $text = '<div class="feed_warnung">Sie müssen angemeldet sein, um das Passwort ändern zu können</div>';
                    $text .= '<script>
                       $(document).ready(function(){
                            setTimeout(function(){
                                window.location.replace("/user/LogMeIn");                                
                            },1500);                           
                        });    
                        </script>';
                    break;
                case 15:
                    $text = '<div class="feed_fehler">Falsches altes Passwort</div>';
                    break;
                case 16:
                    $text = '<div class="feed_warnung">Neue Passwörter stimmen nicht überein</div>';
                    break;
                case 17:
                    $text = '<div class="feed_erfolg">Passwort erfolgreich upgedatet</div>';
                    break;
                case 18:
                    $text = '<div class="feed_fehler">Fehler aufgetretten</div>';
                    break;
                case 20:
                    $text = '<div class="feed_warnung">Passwort muss mindestens eine Zahl enthalten</div>';
                    break;
                case 21:
                    $text = '<div class="feed_warnung">Passwort muss mindestens eine kleine Buchstabe  enthalten</div>';
                    break;
                case 22:
                    $text = '<div class="feed_warnung">Passwort muss mindestens eine grosse Buchstabe  enthalten</div>';
                    break;
                case 23:
                    $text = '<div class="feed_warnung">Passwort muss mindestens 6 Zeichen lang sein</div>';
                    break;
                case 24:
                    $text = '<div class="feed_warnung">Ihr Konto ist noch nicht aktiv.<a href="/user/verify_code/' . $this->code . '"><b>Konto aktivieren</b></a></div>';
                    break;
                default:
                    $text = '<div class="feed_fehler">Unbekannter Fehler</div>';
                    break;
            }
            echo $text;
        }

    }

}

