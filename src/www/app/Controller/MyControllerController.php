<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Spencer
 * Date: 2/16/13
 * Time: 3:58 PM
 * To change this template use File | Settings | File Templates.
 */
App::uses('ConnectionManager', 'Model');
// tested with CakePHP r1892
class MyControllerController extends AppController
{
    // var $uses = null; works too
    var $uses = array();

    function index()
    {
        echo 'in<br/>';
        $db = ConnectionManager::getDataSource('default');
        echo 'connected<br/>';
        $db->rawQuery("INSERT INTO mbcounts.counselors
                        (id, first_name, last_name, city, zip, phone, address, state)
                        VALUES (id, 'cake', 'raw', 'query', '83711', '222-333-4444', '9890 W Secr', 'ID');
                        ");
        echo $db->lastInsertId();
//        $db->rawQuery("SELECT id, name FROM mbcounts.meritbadges");
    }
}