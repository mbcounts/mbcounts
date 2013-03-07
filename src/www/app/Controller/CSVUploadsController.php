<?php

//App::Import('ConnectionManager');
App::uses('ConnectionManager', 'Model', 'File', 'Utility');

class CSVUploadsController extends AppController {
    public $helpers = array('Html', 'Form');
    var $uses = array();

    public function index() {
        //$this->set('counselors', $this->Counselor->find('all'));
        //$this->autoRender = false;

//        $upload = $this->request->data['CSVUpload']['submittedfile'];
//        print_r($upload);

        $db = ConnectionManager::getDataSource('default');

        if (isset($this->request->data['CSVUpload'])){
            $filePath = $this->request->data['CSVUpload']['submittedfile']['tmp_name'];

            if (file_exists($filePath)){
                $this->loadModel('RawCounselor');
                $handle = fopen($filePath, 'r');

                $headerRow = fgetcsv($handle);
                //print_r($headerRow);

                $sql = "INSERT INTO raw_counselors ([COLUMNS]) VALUES [VALUES]";
                $columns = "";
                $comma = "";
                $colspec = "";

                $columnNamesUsed = array();

                foreach($headerRow as $column){
                    //handle dup column names
                    $c=0;
                    $columnName = $column;
                    while (array_key_exists($columnName, $columnNamesUsed)){
                        $c++;
                        $columnName = $column.$c;
                    }
                    $columnNamesUsed[$columnName] = true;

                    // for insert
                    $columns .= $comma . "`$columnName`";
                    // for create
                    $colspec .= $comma . "`$columnName` varchar(50) DEFAULT NULL";

                    $comma = ", ";
                }

                //echo "COLUMNS: ".$columns."<br/>";
                //echo "COLSPEC: ".$colspec."<br/>";

                $drop = "DROP TABLE IF EXISTS raw_counselors;";
                $db->rawQuery($drop);

                $create = "CREATE TABLE raw_counselors([COLSPEC])";
                $create = str_replace("[COLSPEC]", $colspec, $create);
                $db->rawQuery($create);

                $sql = str_replace("[COLUMNS]", $columns, $sql);
                //echo "SQL: ".$sql."<br/>";
                //echo "<br/><br/>";

                $groupCount = 50;
                $comma1 = "";
                $valueLines = "";
                $j = 0;

                while( ($row = fgetcsv($handle)) !== FALSE){

                    $comma = "";
                    $valueLine = "";
                    $size = sizeof($row);

                    for ($i=0; $i<$size; $i++){
                        $valueLine .= $comma ."'". $row[$i] . "'"; //TODO?? escape?
                        $comma = ",";
                    }
                    $valueLine = "(".$valueLine.")";

                    $valueLines .= $comma1.$valueLine;
                    $comma1 = ",";
                    $j++;

                    if ($j > $groupCount){
                        $this->runSql($valueLines, $sql, $db);
                        $j = 0;
                        $comma1 = "";
                        $valueLines = "";
                    }
                }

                // final query
                if ($valueLines != ""){
                    $this->runSql($valueLines, $sql, $db);
                }

                fclose($handle);

                // fixes to incorrect raw data from our council
                $db->rawQuery("update raw_counselors set Badge = 'Communication' where Badge = 'Communications';");
                $db->rawQuery("update raw_counselors set Badge = 'Fly-Fishing' where Badge = 'Fly Fishing';");
                $db->rawQuery("update raw_counselors set Badge = 'Motorboating' where Badge = 'Motor Boating';");
                $db->rawQuery("update raw_counselors set Badge = 'Small-Boat Sailing' where Badge = 'Small Boat Sailing';");

                $this->set('processCSVSuccess', true);
                $this->render();
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

    private function runSql($valueLines, $sql, $db) {
        $finalsql = str_replace("[VALUES]", $valueLines, $sql);
        //echo $finalsql;
        $db->rawQuery($finalsql);
        //echo "<br/>---------------------------------<br/><br/>";
    }
}