<?php

class CounselorsController extends AppController {
    public $helpers = array('Html', 'Form');
    var $uses = array();

    public function index() {
        $this->set('counselors', $this->Counselor->find('all'));
    }

    public function getCounselorsForBadgeIDs(){
        $this->autoRender = false;
        $a = array('couns'=>'bobster');
        echo json_encode( $a);
    }

    public function getCounselorsForBadgeWithinXMiles($centerLat, $centerLon, $radius, $badgeId){
    }
}