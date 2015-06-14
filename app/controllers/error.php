<?php

if (!class_exists('error')) {

    class error extends mainview {
        private  $registry;
        public function __construct($registry) {
            parent::__construct();
            $this->registry = $registry;
            $this->index();
        }

        public function index() {
            echo 'error';
        }

        public function __destruct() {
            parent::__destruct();
        }

    }

}
