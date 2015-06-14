<?php

if (!class_exists('test_')) {

    class test_ {

        function __construct() {
            
        }

        public function index(){
            $db = new DB();
            $db->select_db('Posts');
            $table = 'posts';
            
            $was = array ('id','thema','titel');
            
           $wer = array('id'=>31,'thema'=>'android');             
           $logic_op = 'or';   
            
            
            //$wer = range (30,33);            
            //$logic_op = 'id';
            
            
            $result = $db->select($table, $was, $wer, $logic_op);
            
            $template = new Template();
            $template->set_template('content.tpl');
            $template->set($result->rows);
            
            
            echo $template->render();
            //print_r($result);
        }

        public function __destruct() {
            
        }

    }

}

