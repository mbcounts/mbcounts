<?php

class RawCounselorsController extends AppController {
    public $helpers = array('Html', 'Form');
    var $uses = array();

    public function index() {
        echo $this->RawCounselor->tableExists('raw_counselors');
        $this->set('rawcounselors', $this->RawCounselor->find('all',
            array(
                'order' => array(
                    '`last name`' => 'asc'
                )
            )
        )
        );
    }
}
