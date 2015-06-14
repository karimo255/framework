<?php

if (!class_exists('mainView')) {

    class mainView {

        private $lang;
        private $template;

        public function __construct() {

            $this->template = new Template();
            $this->template->set_template('header.tpl', 'hauptSeite');

            $this->lang = new Lang();
            $lang = $this->lang->getSection('header');

            $this->template->set_light($lang);
            $this->template->set_light($this->loadScripts());
            $this->loadJQueryUi();

            ob_start();

            echo $this->template->render_light();
        }

        private function loadScripts() {
            ob_start();
            readfile('public/css/main.css');
            $css = ob_get_contents();
            ob_end_clean();

            ob_start();
            readfile('public/js/java.js');
            $java = ob_get_contents();
            ob_end_clean();
            
            return array(
                'css' => $css,
                'java' => $java
            );
        }

        private function loadJQueryUi() {
            if (!in_array(get_class($this), $this->jQueryUiNeeded())) {
                $this->template->set_light('jQueryUI', '');
                return;
            }
            ob_start();

            readfile('public/js/jquery-ui.min.js');
            $jqueryUI = ob_get_contents();
            ob_end_clean();
            $this->template->set_light('jQueryUI', $jqueryUI);
        }

        private function jQueryUiNeeded() {
            return array(
                'Index',
            );
        }

        public function convert($size) {
            $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
            return ($size / pow(1024, ($i = floor(log($size, 1024))))) . ' ' . $unit[$i];
        }



        public function print_gzipped_output() { {
                $HTTP_ACCEPT_ENCODING = $_SERVER["HTTP_ACCEPT_ENCODING"];
                if (headers_sent())
                    $encoding = false;
                else if (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false)
                    $encoding = 'x-gzip';
                else if (strpos($HTTP_ACCEPT_ENCODING, 'gzip') !== false)
                    $encoding = 'gzip';
                else
                    $encoding = false;

                if ($encoding) {
                    $contents = ob_get_clean();
                    $_temp1 = strlen($contents);
                    if ($_temp1 < 2048)    // no need to waste resources in compressing very little data
                        print($contents);
                    else {
                        header('Content-Encoding: ' . $encoding);
                        print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
                        $contents = gzcompress($contents, 9);
                        $contents = substr($contents, 0, $_temp1);
                        print($contents);
                    }
                } else
                    ob_end_flush();
            }
        }
        
        function __destruct() {
            $template = new Template();

            $lang = $this->lang->getSection('footer');
            $token = new Token();
            $tk = $token->generate('newsletter');

            $template->set_template('footer.tpl', 'hauptSeite');
            $template->set_light('token', $tk);
            $template->set_light($lang);

            echo $template->render_light();
          //  echo 'memory =>.' . $this->convert(memory_get_usage(true)); // 123 kb

            $this->print_gzipped_output();
        }        

    }

}
