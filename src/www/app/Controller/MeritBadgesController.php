<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Spencer
 * Date: 2/16/13
 * Time: 3:58 PM
 * To change this template use File | Settings | File Templates.
 */

class MeritBadgesController extends AppController
{
    var $uses = array('Meritbadge');

    function index()
    {
        $this->showBsaMeritBadgeList();
    }

    public function testit(){
        $this->autoRender = false;
        return 'from testit';
    }

    public function showBsaMeritBadgeList(){
        $MBs = $this->getMeritBadgeListFromBSASite();
        $this->set('mblist', $MBs);
    }

    public function compareBsaListWithCouncilList(){
        $combinedBadges = $this->getCombinedArray();
        $this->set('combinedBadges', $combinedBadges);
    }

    private function getCombinedArray(){
        $bsaList = $this->getMeritBadgeListFromBSASite();
        $councilList = $this->getCouncilBadges();

        $cntBSA = count($bsaList);
        $cntCoun = count($councilList);

        $count =  ($cntBSA > $cntCoun) ? $cntBSA : $cntCoun;

        $iB = 0;
        $iC = 0;

        $combined = array();

        for ($i=0; $i<$count; $i++){
            $valB = $bsaList[$iB];
            $valC = $councilList[$iC];

            if ($valB == $valC){
                array_push($combined, array($valB, $valC));
                $iB++;  $iC++;
            }
            else{
                if ($valB > $valC){
                    array_push($combined, array("", $valC));
                    $iC++;
                }
                else{
                    array_push($combined, array($valB, ""));
                    $iB++;
                }
            }
        }

        return $combined;
    }

    private function getCouncilBadges(){
        $sql = "SELECT name AS name FROM meritbadges ORDER BY name;";
        $badges = $this->Meritbadge->query($sql);

        $i = 0;
        $councilBadges = array();
        foreach($badges as $row){
            array_push($councilBadges, $row['meritbadges']['name']);
        }
        return $councilBadges;
    }

    private function getMeritBadgeListFromBSASite(){
        $localSource = false;
        Configure::load('mbcounts', 'default');
        $url = Configure::read('uris.BSAMeritBadgeList');

        $html = file_get_contents($url);

        $local = $_SERVER['DOCUMENT_ROOT'].'/app/tmp/latest_mbs_from_bsa.html';

        if ( ! $html ){
            $localSource = true;
            $html = file_get_contents($local);
        }

        $start = strpos($html, '<h2>Merit Badge Requirements');
        $firstParaClose = strpos($html, '</p>', $start);
        $firstParaClose += 4;
        $end = strpos($html, '</p>', $firstParaClose );

        $frag = substr($html, $firstParaClose, $end - $firstParaClose);

        $arr1 = explode('.aspx">', $frag);

        array_shift($arr1);

        $i = 0;
        $MBs = array();
        foreach($arr1 as $val){
            $i++;
            if ($i % 2 == 0 ) {
                array_push($MBs, substr($val, 0, strpos($val, '<') ));
            }
        }

        if ( ! $localSource && count($MBs) > 120){
            file_put_contents($local, $html);
        }


        return $MBs;


    }
}
