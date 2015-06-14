<?php

if (!class_exists('test')) {

    class test extends mainView {

        public $model;

        function __construct() {
            parent::__construct();
        }

        public function index($id) {
            
        }
        public function a($id) {
           //$id = $_SESSION['id'];
          // $file = $this->file_get_contents_utf8('http://www.alhamdulillah.net/modules.php?op=modload&name=Hadith-Bukhari&file=index&action=viewcat&cat=' . $id);
           //echo $file;
            require_once  ROOT.'/lib/db_unsecure.php';

            $db = DB::getInstance();
            $db->select_db('Moschee');
            $v = "مصالح الأمن تعاملت مع الحقيبة المجهولة بكثير من الحذر والجدية، خشية أن تكون محشوة بممنوعات أو ملغومة ";
            //$v = 'dd';
           // $v = htmlentities($v);
            $sql = "UPDATE   `Moschee`.`banner` SET `beschreibungAR` ='$v' WHERE `id`='$id' ";
            $data = array('beschreibungAR'=>$v);
            $wer = array('id'=>$id);
            $table = 'banner';
           // $data->
        }        

        private function file_get_contents_utf8($fn) {
            $content = file_get_contents($fn);
            return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
        }

        function __destruct() {
            parent::__destruct();
        }

    }

}