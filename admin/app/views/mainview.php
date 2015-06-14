<?php

if (!class_exists('mainView')) {

    class mainView {

        function __construct() {
            ob_start('ob_gzhandler');
            readfile('../public/css/cms.css');
            $css = ob_get_contents();
            ob_end_clean();

            ob_start('ob_gzhandler');
            readfile('../public/js/jquery-2.0.2.min.js');
            $jquery = ob_get_contents();
            ob_end_clean();

            ob_start('ob_gzhandler');
            readfile('../public/js/java.js');
            $java = ob_get_contents();
            ob_end_clean();

            ob_start('ob_gzhandler');
            readfile('../public/js/jquery-ui.min.js');
            $jqueryUI = ob_get_contents();
            ob_end_clean();

            $template = new Template();
            $template->set_template('header.tpl', 'admin');
            $array = array(
                'css' => $css,
                'jQuery' => $jquery,
                'java' => $java,
                'jqueryUI' => $jqueryUI
            );
            $template->set($array);
            ob_start('ob_gzhandler');
            echo $template->render();
            include_once TEMPLATES . 'admin/menu_aside.tpl';
        }

        function __destruct() {

            $template = new Template();
            $template->set_template('footer.tpl', 'admin');
            $template->set('footer', 'footer');
            echo $template->render();
            ob_end_flush();
        }

    }

}
