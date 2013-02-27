<?php

class CounselorsController extends AppController {
    public $helpers = array('Html', 'Form');
    var $uses = array();

    public function index() {
        $this->set('counselors', $this->Counselor->find('all'));
    }
}