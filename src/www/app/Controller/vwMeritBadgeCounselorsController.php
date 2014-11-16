<?php

class vwmeritbadgecounselorscontroller extends AppController {
    public $helpers = array('Html', 'Form');
    var $uses = array('Geodatas', 'VwMeritBadgeCounselor');

    public function index() {
        $this->set('vwmeritbadgecounselors', $this->VwMeritBadgeCounselor->find('all'));
    }

    public function getCounselorsForBadgeIDs(){
        $this->autoRender = false;

        $MBIDs = $this->request->data;

        $vwmeritbadgecounselors = $this->vwmeritbadgecounselor->find('all',

            array(
                'conditions' => array('meritbadges_id' => $MBIDs))

        );

        echo json_encode($vwmeritbadgecounselors);
    }


    public function getCounselorsForBadgeIDsNEW(){
        $this->autoRender = false;

        $MBIDs = $this->request->data['badgeIDs'];
        $range = $this->request->data['range'];
        $address = $this->request->data['address'];
        $city = $this->request->data['city'];
        $state = $this->request->data['state'];
        $zip = $this->request->data['zip'];

        $MBIDsJoined = join(",", $MBIDs);

        $lat = 0;
        $lon = 0;
        App::import('Controller', 'Geodatas');
        $geodatas = new GeodatasController();
        $geodatas->GetLatLonFromAddress($address, $city, $state, $zip, $lat, $lon);

        // Thanks to Movable Type Scripts!!
        // source: http://www.movable-type.co.uk/scripts/latlong-db.html
        // License:
        // I offer these formulæ & scripts for free use and adaptation as my contribution to the open-source info-sphere
        // from which I have received so much. You are welcome to re-use these scripts [under a simple attribution
        // license, without any warranty express or implied] provided solely that you retain my copyright notice and a
        // link to this page.  (Attribution license: http://creativecommons.org/licenses/by/3.0/)
//        $lat = 43.604763;  // (Ore-Ida office) latitude of centre of bounding circle in degrees
//        $lon = -116.292893;  // (Ore-Ida office) longitude of centre of bounding circle in degrees
        $rad = $range;  // radius of bounding circle in miles

        $R = 3959;  // earth's radius, miles

        // first-cut bounding box (in degrees)
        $maxLat = $lat + rad2deg($rad/$R);
        $minLat = $lat - rad2deg($rad/$R);
        // compensate for degrees longitude getting smaller with increasing latitude
        $maxLon = $lon + rad2deg($rad/$R/cos(deg2rad($lat)));
        $minLon = $lon - rad2deg($rad/$R/cos(deg2rad($lat)));

        // convert origin of filter circle to radians
        $lat = deg2rad($lat);
        $lon = deg2rad($lon);

        $sql = "
            Select counselors_id as counselors_id, meritbadges_id, FirstName, MiddleName, LastName, /* Address1, Address2, */ City, State, Zip, Phone, Lat, Lon, troop_only,
                   acos(sin($lat)*sin(radians(Lat)) + cos($lat)*cos(radians(Lat))*cos(radians(Lon)-$lon))*$R As Distance
            From (
              Select counselors_id, meritbadges_id, FirstName, MiddleName, LastName, Address1, Address2, City, State, Zip, Phone, Lat, Lon, troop_only
              From vw_merit_badge_counselors
              Where meritbadges_id IN ($MBIDsJoined)
                AND Lat>$minLat And Lat<$maxLat
                And Lon>$minLon And Lon<$maxLon
              ) As vwmeritbadgecounselor
            Where
              acos(sin($lat)*sin(radians(Lat)) + cos($lat)*cos(radians(Lat))*cos(radians(Lon)-$lon))*$R < $rad
            Order by Distance";

        $mbcounselors = $this->VwMeritBadgeCounselor->query($sql);

        echo json_encode($mbcounselors);
    }
}