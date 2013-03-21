<?php

class GeodatasController extends AppController {
    public $helpers = array('Html', 'Form');
    var $uses = array('Counselor', 'Geodata');

    public function index() {
        $this->set('geodatas', $this->Geodata->find('all'));
    }

    public function info(){
        $w = stream_get_wrappers();
        echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "<br/>";
        echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "<br/>";
        echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "<br/>";
        echo 'wrappers: ', var_dump($w);
    }

// FREE--does it suit??
// http://www.openstreetmap.org/copyright

    public function testthis(){
        echo "<html><body>";
        $this->autoRender = false;

        $sql = "select
                Counselor.ID,
                Counselor.Address1 as Address1,
                Counselor.Address2 as Address2,
                Counselor.City as City,
                Counselor.State as State,
                Counselor.ZIPCode as ZIPCode
                from counselors Counselor
                LEFT join geodatas g
                on Counselor.id = g.counselors_id
                where g.counselors_id is null
                LIMIT 30";

        $result = $this->Counselor->query($sql);

        foreach($result as $item){
            $add1 = trim($item['Counselor']['Address1']." ");
            $add2 = trim($item['Counselor']['Address2']." ");
            if (strlen($add2) > 0){
                $add1 = $add2;
                $add2 = "";
            }
            $city =  $item['Counselor']['City'];
            $state = $item['Counselor']['State'];
            $zip =   $item['Counselor']['ZIPCode'];

            echo join(" ", array($add1,$add2,$city,$state,$zip))."<br/>";
        }
        echo "</body></html>";

    }

    // you can call this as much as you want.  if all counselors already have geo data, then nothing will happen
    public function populateCounselorGeoData(){
        $sql = "select
                Counselor.ID,
                Counselor.Address1 as Address1,
                Counselor.Address2 as Address2,
                Counselor.City as City,
                Counselor.State as State,
                Counselor.ZIPCode as ZIPCode
                from counselors Counselor
                LEFT join geodatas g
                on Counselor.id = g.counselors_id
                where g.counselors_id is null
                LIMIT 30";

        $counselorWithoutGeodata = $this->Counselor->query($sql);

        $template = "streetAddress=[ADDRESS]&city=[CITY]&state=[STATE]&zip=[ZIP]";
        $urlBase = "https://geoservices.tamu.edu/Services/Geocode/WebService/GeocoderWebServiceHttpNonParsed_V04_01.aspx?apiKey=dc8d804f1fda4107ade5369f478f5182&version=4.01&";
        $i = 0;
        foreach($counselorWithoutGeodata as $item){
            $i++;
            $add1 = trim($item['Counselor']['Address1']." ");
            $add2 = trim($item['Counselor']['Address2']." ");
            if (strlen($add2) > 0){
                $add1 = $add2;
                $add2 = "";
            }
            $city =  $item['Counselor']['City'];
            $state = $item['Counselor']['State'];
            $zip =   $item['Counselor']['ZIPCode'];

            $replString = str_replace("[ADDRESS]", urlencode($add1), $template);
            $replString = str_replace("[CITY]", urlencode($city), $replString);
            $replString = str_replace("[STATE]", urlencode($state), $replString);
            $replString = str_replace("[ZIP]", urlencode($zip), $replString);

            $result = file_get_contents($urlBase . $replString);

            if ($result == false){
                echo "problem with: ".$replString."<br/><br/>";
                $lat = 0;
                $lon = 0;
            }

            $data = explode(",", $result);
            $lat = $data[3];
            $lon = $data[4];

            if ( ! isset($lat) ){ $lat = 0; }
            if ( ! isset($lon) ){ $lon = 0; }

            $this->Geodata->create();
            $data = Array
            (
                'Geodata' => Array
                (
                    'counselors_id' => $item['Counselor']['ID'],
                    'lat' => $lat,
                    'lon' => $lon
                )
            );
            $this->Geodata->save($data);

            if ($i > 28) return;
        }


        //echo $result;
// -------------
//        $data = "a984776b-846f-4225-a248-19039f667d31,4.01,200,43.6458771026941,-116.305665275003,02,Parcel,100,Exact,Success,1,Parcel,811.502160638571,Meters,LOCATION_TYPE_STREET_ADDRESS,0,";
//
//        $data = explode(",", $data);
//        echo $data[3];
//        echo $data[4];

    }
}