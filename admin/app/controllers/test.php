<?php

if (!class_exists('test')) {

    class test extends mainView {

        public $model;

        function __construct() {
            parent::__construct();
            $nav = new headerMap();
            
        }

        public function index() {
            //$this->model->index();
        }

        function __destruct() {
            parent::__destruct();
        }

    }

}