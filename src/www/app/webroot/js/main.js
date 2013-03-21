/**
 * Created with JetBrains PhpStorm.
 * User: Spencer
 * Date: 3/8/13
 * Time: 9:24 PM
 * To change this template use File | Settings | File Templates.
 */
function updateCounselorSet(){
    var postdata =
        {
            'badgeIDs' : getSelectedBadgeIDs(),
            'range' : $('#range').val()
        };
    $.post('vwMeritBadgeCounselors/getCounselorsForBadgeIDsNEW', {data: postdata}, function(data) {
        populateCounselorSet(data);
    });
}

function getSelectedBadgeIDs(){
    return $( "input.mbcheckbox:checked").map(
        function(){
            return this.value;
        }
    ).get();
}

function populateCounselorSet(data){
    $('#counselorSet').empty();

    addMeritBadgeContainers();

    var template = '<div class="counselor">FIRST MIDDLE LAST (DISTANCE miles)<br/>ADDRESS1<br/>ADDRESS2<br/>CITY, STATE ZIP<br/>PHONE</div>';
    var i = 0;
    var objs = JSON && JSON.parse(data) || $.parseJSON(data);

    for(var obj in objs){
        var html = template;
        html = html.replace('FIRST',    objs[obj].vwmeritbadgecounselor.FirstName);
        html = html.replace('MIDDLE',   objs[obj].vwmeritbadgecounselor.MiddleName);
        html = html.replace('LAST',     objs[obj].vwmeritbadgecounselor.LastName);
        html = html.replace('DISTANCE', Number(objs[obj][0].Distance).toFixed(1) );
        html = html.replace('ADDRESS1', objs[obj].vwmeritbadgecounselor.Address1);
        var add2 = objs[obj].vwmeritbadgecounselor.Address2.trim();
        if (add2.length == 0){
            html = html.replace('ADDRESS2<br/>', '');
        }
        else{
            html = html.replace('ADDRESS2', add2);
        }
        html = html.replace('CITY',     objs[obj].vwmeritbadgecounselor.City);
        html = html.replace('STATE',    objs[obj].vwmeritbadgecounselor.State);
        html = html.replace('ZIP',      objs[obj].vwmeritbadgecounselor.Zip);
        html = html.replace('PHONE',    objs[obj].vwmeritbadgecounselor.Phone);
        $('#mbcContainer' + objs[obj].vwmeritbadgecounselor.meritbadges_id).append(html);
    }
}

function addMeritBadgeContainers(){
    var labels = $( "input.mbcheckbox:checked" ).parent();
    if (labels.length > 0){
        for (var i = 0; i<labels.length; i++){
            var txt = $(labels[i]).text().trim();
            $('#counselorSet').append('<div id="mbcContainer' + $(labels[i]).children()[0].value + '" class="mbcContainer"><div class="mbcContainerTitle">'+txt+'</div></div>');
        }
    }
}

var countChecked = function(className) {
    return $( "input."+ className +":checked" ).length;
}
