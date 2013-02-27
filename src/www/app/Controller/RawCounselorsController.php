<?php

class RawCounselorsController extends AppController {
    public $helpers = array('Html', 'Form');
    var $uses = array();

    public function index() {
        $this->set('rawcounselors', $this->RawCounselor->find('all'));
    }
}
