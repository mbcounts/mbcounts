<?php

//App::Import('ConnectionManager');
App::uses('ConnectionManager', 'Model', 'File', 'Utility');

class CSVUploadsController extends AppController {
    public $helpers = array('Html', 'Form');
    var $uses = array('MeritBadges');

    public function index() {
        $this->autoRender = true;

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

                $drop = "DROP TABLE IF EXISTS `councils`;
                         DROP TABLE IF EXISTS `counselors`;
                         DROP TABLE IF EXISTS `districts`;
                         DROP TABLE IF EXISTS `meritbadgecounselors`;
                         DROP TABLE IF EXISTS `meritbadges`;
                         DROP TABLE IF EXISTS `raw_counselors`;
                ";

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

                $create = "
                    CREATE TABLE `councils` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(50) DEFAULT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

                    insert into councils (name)
                    select distinct `Council Name`
                    FROM mbcounts.raw_counselors;
                    -- ------------------------------------------------------------------------------

                    -- DISTRICT TABLE ---------------------------------------------------------------
                    CREATE TABLE `districts` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(50) DEFAULT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

                    insert into districts (name)
                    select distinct `District Name`
                    FROM mbcounts.raw_counselors;
                    -- ------------------------------------------------------------------------------


                    -- COUNSELORS TABLE -------------------------------------------------------------
                    CREATE TABLE IF NOT EXISTS `counselors` (
                      `ID` int NOT NULL,
                      `Council` int NULL,
                      `District` int NULL,
                      `Prefix` varchar(15) DEFAULT NULL,
                      `FirstName` varchar(30) DEFAULT NULL,
                      `MiddleName` varchar(30) DEFAULT NULL,
                      `LastName` varchar(30) DEFAULT NULL,
                      `Suffix` varchar(10) DEFAULT NULL,
                      `Address1` varchar(50) DEFAULT NULL,
                      `Address2` varchar(50) DEFAULT NULL,
                      `Address3` varchar(50) DEFAULT NULL,
                      `Address4` varchar(50) DEFAULT NULL,
                      `Address5` varchar(50) DEFAULT NULL,
                      `City` varchar(25) DEFAULT NULL,
                      `State` varchar(2) DEFAULT NULL,
                      `ZIPCode` varchar(10) DEFAULT NULL,
                      `PhoneType` varchar(1) DEFAULT NULL,
                      `PhoneNo` varchar(20) DEFAULT NULL,
                      `PhoneExt` varchar(10) DEFAULT NULL,
                      `PhoneType1` varchar(1) DEFAULT NULL,
                      `PhoneNo1` varchar(50) DEFAULT NULL,
                      `PhoneExt1` varchar(10) DEFAULT NULL,
                      `EffectiveDate` datetime DEFAULT NULL,
                      `ExpireDate` datetime DEFAULT NULL,
                      PRIMARY KEY (`ID`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

                    INSERT INTO counselors (ID, Council, District, Prefix, FirstName, MiddleName, LastName,
                    Suffix, `Address1`, `Address2`, `Address3`, `Address4`, `Address5`, City, State, `ZIPCode`,
                    `PhoneType`, `PhoneNo`, `PhoneExt`, `PhoneType1`, `PhoneNo1`, `PhoneExt1`, `EffectiveDate`, `ExpireDate`)
                    SELECT DISTINCT
                    `Person ID` AS id,
                      (select id from councils where name = `Council Name`),
                      (select id from districts where name = `District Name`),
                      Prefix, `First Name`, `Middle Name`, `Last Name`, Suffix,
                      `Address 1`, `Address 2`, `Address 3`, `Address 4`, `Address 5`,
                      City, State, `ZIP Code`, `Phone Type`, `Phone No`, `Phone Ext`,
                      `Phone Type1`, `Phone No1`, `Phone Ext1`, STR_TO_DATE(`Effective Date`,'%m/%d/%Y'),
                      STR_TO_DATE(`Expire Date`,'%m/%d/%Y')
                    FROM mbcounts.raw_counselors;
                    -- ------------------------------------------------------------------------------


                    -- MERITBADGES TABLE --------------------------------------------------
                    create table meritbadges(
                      id INT primary key not null auto_increment,
                      name varchar(40)
                    );


                    INSERT INTO meritbadges (name)
                    SELECT DISTINCT Badge from raw_counselors ORDER BY Badge ASC;
                    -- ------------------------------------------------------------------------------


                    -- MERITBADGE COUNSELORS TABLE --------------------------------------------------

                    CREATE TABLE IF NOT EXISTS `meritbadgecounselors` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `counselors_id` int(11) NOT NULL,
                      `meritbadges_id` int(11) NOT NULL,
                      `troop_only` varchar(1) NOT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `unique merit badge counselor combo` (`counselors_id`,`meritbadges_id`),
                      KEY `counselors` (`counselors_id`),
                      KEY `meritbadges` (`meritbadges_id`),
                      KEY `troop_only` (`troop_only`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=8196 DEFAULT CHARSET=latin1;


                    insert into meritbadgecounselors (counselors_id, meritbadges_id, troop_only)
                    select distinct rc.`person id`, mb.id, rc.`Troop Only`
                    from raw_counselors rc join meritbadges mb on rc.Badge = mb.name
                    order by rc.`Person ID`, rc.Badge;
                    -- ------------------------------------------------------------------------------


                    -- MERITBADGE COUNSELORS VIEW ---------------------------------------------------
                    DROP VIEW IF EXISTS `vwmeritbadgecounselors`;

                    CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost`
                    SQL SECURITY DEFINER VIEW `vwmeritbadgecounselors`
                    AS select `mbc`.`counselors_id` AS `counselors_id`,`mbc`.`meritbadges_id`
                    AS `meritbadges_id`,`c`.`FirstName` AS `FirstName`,left(`c`.`MiddleName`,1) AS `MiddleName`,
                    `c`.`LastName` AS `LastName`,`c`.`Address1`
                    AS `Address1`,`c`.`Address2` AS `Address2`,`c`.`City` AS `City`,`c`.`State`
                    AS `State`,`c`.`ZIPCode` AS `Zip`,`c`.`PhoneNo` AS `Phone`
                    from (`counselors` `c` join `meritbadgecounselors` `mbc` on((`c`.`ID` = `mbc`.`counselors_id`)))
                    where ((now() <= `c`.`ExpireDate`) and (`mbc`.`troop_only` = 'N'));
                    -- ------------------------------------------------------------------------------
                ";
                $db->rawQuery($create);

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