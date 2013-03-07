<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Spencer
 * Date: 2/16/13
 * Time: 3:58 PM
 * To change this template use File | Settings | File Templates.
 */

class MainController extends AppController
{
    public $helpers = array('Html', 'Form');
    var $uses = array('Meritbadge');

    public function index()
    {
        $mbs = $this->Meritbadge->find('all');
        $this->set('meritbadges', $mbs);
    }
}
