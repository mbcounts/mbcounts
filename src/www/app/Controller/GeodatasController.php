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
        $lat = 0;
        $lon = 0;
        $processedCount = 0;
        $processedDetails = array();

        ini_set('max_execution_time', 120); //120 seconds = 2 minutes

//        $sql = "select
//                Counselor.ID,
//                Counselor.Address1 as Address1,
//                Counselor.Address2 as Address2,
//                Counselor.City as City,
//                Counselor.State as State,
//                Counselor.ZIPCode as ZIPCode
//                from counselors Counselor
//                LEFT join geodatas g
//                on Counselor.id = g.counselors_id
//                where g.counselors_id is null
//                LIMIT 30";

        $sql = "
            select
                Counselor.ID,
                Counselor.Address1 as Address1,
                Counselor.Address2 as Address2,
                Counselor.City as City,
                Counselor.State as State,
                Counselor.ZIPCode as ZIPCode  ,
                sha1( upper(trim(address1)) + ' ' + upper(trim(address2)) + ' ' + upper(trim(city)) + ' ' + upper(trim(state)) ) as sha1
            from counselors Counselor
            left join geodatas g
                on sha1( upper(trim(address1)) + ' ' + upper(trim(address2)) + ' ' + upper(trim(city)) + ' ' + upper(trim(state)) ) = g.sha1
            where g.sha1 is null
            order by Counselor.LastName, Counselor.FirstName
            LIMIT 50
        ";

        $counselorWithoutGeodata = $this->Counselor->query($sql);

        $i = 0;

        $sha1 = '';

        foreach($counselorWithoutGeodata as $item){
            $sha1 = $item[0]['sha1'];
            if ( ! $this->shaExistsInTable($sha1)){

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

                $success = $this->GetLatLonFromAddress($add1, $city, $state, $zip, $lat, $lon);

                $this->Geodata->create();
                $data = Array
                (
                    'Geodata' => Array
                    (
                        'sha1' => $sha1,
                        'lat' => $lat,
                        'lon' => $lon
                    )
                );
                $this->Geodata->save($data);
                $processedCount++;
                array_push( $processedDetails , $item['Counselor']['ID'] );

                // if ($i > 28) return;
            }
            else{
                echo('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;already exists: ' . $sha1 . '<br/>');
            }
        }
        if ($processedCount == 0){
            $this->set('header', 'No data processed.  It appears the geodata table is up to date.');
        }
        else{
            $this->set('header', $processedCount . ' address(es) processed');
            $this->set('processedIds', $processedDetails);
        }
    }

    private function shaExistsInTable($sha1){
        $conditions = array(
            'sha1' => $sha1
        );
        return $this->Geodata->hasAny($conditions);
    }

    /**
     * @param $address
     * @param $city
     * @param $state
     * @param $zip
     * @param &$lat
     * @param &$lon
     * @returns boolean for success
     */
    public function GetLatLonFromAddress($address, $city, $state, $zip, &$lat, &$lon) {
        $result = true;

        // source: http://geoservices.tamu.edu/About/Legal/

        // PRIVACY POLICY of Texas A&M Geoservices:
        // [sw added emphasis asterisks] The privacy of your data is among our primary concerns. Your data is your data.
        // Unless you choose for us to keep track of your data, ***we will not. Nor will we share any of your
        // information without your permission.***

        // SECURITY POLICY
        // Our servers are kept in the Texas A&M GeoSciences data center, in our building. This facility is secure, so
        // is your data. Our data security policies, staff training, and technical infrastructure help keep your data that way.

        $template = "streetAddress=[ADDRESS]&city=[CITY]&state=[STATE]&zip=[ZIP]";
        $urlBase = "https://geoservices.tamu.edu/Services/Geocode/WebService/GeocoderWebServiceHttpNonParsed_V04_01.aspx?apiKey=dc8d804f1fda4107ade5369f478f5182&version=4.01&";

        $replString = str_replace("[ADDRESS]", urlencode($address), $template);
        $replString = str_replace("[CITY]", urlencode($city), $replString);
        $replString = str_replace("[STATE]", urlencode($state), $replString);
        $replString = str_replace("[ZIP]", urlencode($zip), $replString);

        $result = file_get_contents($urlBase . $replString);

        if ($result == false) {
            echo "problem with: " . $replString . "<br/><br/>";
            $lat = 0;
            $lon = 0;
        }

        $data = explode(",", $result);
        $lat = $data[3];
        $lon = $data[4];

        if ( ! isset($lat)) {
            $lat = 0;
        }
        if ( ! isset($lon)) {
            $lon = 0;
        }

        return $result;
    }

    private function backUpGeodatasTable(){
        $this->query('SELECT * FROM foo WHERE id=? OR somefield=?', array(123, 'foo'));


    }
}