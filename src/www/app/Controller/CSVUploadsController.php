<?php
App::uses('File', 'Utility');

class CSVUploadsController extends AppController {
    public $helpers = array('Html', 'Form');
    var $uses = array();

    public function index() {
        //$this->set('counselors', $this->Counselor->find('all'));
        //$this->autoRender = false;

//        $upload = $this->request->data['CSVUpload']['submittedfile'];
//        print_r($upload);
        if (isset($this->request->data['CSVUpload'])){
            $filePath = $this->request->data['CSVUpload']['submittedfile']['tmp_name'];

            if (file_exists($filePath)){
                $this->loadModel('RawCounselor');
                $handle = fopen($filePath, 'r');

                $headerRow = fgetcsv($handle);
                //print_r($headerRow);


                $groupCount = 50;

                while( ($row = fgetcsv($handle)) !== FALSE){
                    $i = 0;
                    $rowData = array();
                    foreach($headerRow as $key){
                        $rowData[$key] = $row[$i];
                        $i++;
                    }
                    $rowSet['RawCounselor'] = $rowData;

                    array_push($saveData, $rowSet);
                    print_r($saveData);
                    //echo('<br/>');
                }

                $this->RawCounselor->SaveAll($saveData);

                //print_r($modelRows);

                fclose($handle);
            }

// THIS WORKS, BUT IT RUNS OUT OF MEMORY IF FILE IS TOO BIG!!
//            if (file_exists($filePath)){
//                $this->loadModel('RawCounselor');
//                $handle = fopen($filePath, 'r');
//
//                $headerRow = fgetcsv($handle);
//                //print_r($headerRow);
//
//
//                $rowSet = array();
//                $saveData = array();
//
//                while( ($row = fgetcsv($handle)) !== FALSE){
//                    $i = 0;
//                    $rowData = array();
//                    foreach($headerRow as $key){
//                        $rowData[$key] = $row[$i];
//                        $i++;
//                    }
//                    $rowSet['RawCounselor'] = $rowData;
//
//                    array_push($saveData, $rowSet);
//                    print_r($saveData);
//                    //echo('<br/>');
//                }
//
//                $this->RawCounselor->SaveAll($saveData);
//
//                //print_r($modelRows);
//
//                fclose($handle);
//            }



        }
//        $this->request->data['Document']['submittedfile'] = array(
//            'name' => 'counselorData_'.date("Y_m_d__H_i_s").'.csv',
//            'type' => 'application/csv',
//            'tmp_name' => 'counselorData_'.date("Y_m_d__H_i_s").'.csv',
//            'error' => 0,
//            'size' => 41737,
//        );

    }
}