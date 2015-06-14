<?php

if (!class_exists('Index')) {

    class Index extends mainView {

        public $model;
        private $registry;

        public function __construct($registry) {
            parent::__construct();
            $this->registry = $registry;
            $this->model = new Index_($registry);
        }

        public function index() {
            include_once TEMPLATES .'admin/home.tpl';
        }

        public function __destruct() {
            parent::__destruct();
        }

    }

}