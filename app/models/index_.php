<?php

if (!class_exists('Index_')) {

    class Index_ {
        private $registry;
        private $lang;
        public function __construct($registry) {
            $this->registry = $registry;
            $this->db = $registry->get('db');
           // $this->db->select_db('Blog');
            $this->lang = $this->registry->get('lang');
            
        }

        public function index(){

        }


 
          
        public function __destruct() {
            
        }

    }

}

