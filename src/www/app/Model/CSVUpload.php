<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Spencer
 * Date: 2/16/13
 * Time: 3:46 PM
 * To change this template use File | Settings | File Templates.
 */
class CSVUpload extends AppModel {
//    public $name = 'CSVUpload';
    public $useTable = false; // This model does not use a database table

    public $submittedfile;

    public function isUploadedFile($params) {
        $val = array_shift($params);
        if ((isset($val['error']) && $val['error'] == 0) ||
            (!empty( $val['tmp_name']) && $val['tmp_name'] != 'none')
        ) {
            return is_uploaded_file($val['tmp_name']);
        }
        return false;
    }
}