/**
 * Created with JetBrains PhpStorm.
 * User: Spencer
 * Date: 3/8/13
 * Time: 9:24 PM
 * To change this template use File | Settings | File Templates.
 */
function updateCounselorSet(){
    var badgeIDs = getSelectedBadgeIDs();

    if (badgeIDs.length == 0){
        flashElement( $('#meritbadges') );
        return;
    }

    var postdata =
        {
            'badgeIDs' : getSelectedBadgeIDs(),
            'range' : $('#range').val(),
            'address' : $('#address').val(),
            'city' : $('#city').val(),
            'state' : $('#state').val(),
            'zip' : $('#zip').val()
        };
    $.post('vwMeritBadgeCounselors/getCounselorsForBadgeIDsNEW', {data: postdata}, function(data) {
        populateCounselorSet(data);
    });
}

function flashElement(jqElement){
    //var id = $("div#1"); // div id=1
    var color = "#FFBBBB"; // color to highlight
    var delayms = "200"; // mseconds to stay color
    jqElement.css("backgroundColor",color)
        .css("transition","all .2s ease") // you may also (-moz-*, -o-*, -ms-*) e.g
        .css("backgroundColor",color)
        .delay(delayms)
        .queue(function() {
            jqElement.css("backgroundColor","");
            jqElement.dequeue();
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

    var template = '<div class="counselor"><span class="counselorName">FIRST MIDDLE LAST</span> (DISTANCE miles)<br/>PHONE</div>';
    var i = 0;
    var objs = JSON && JSON.parse(data) || $.parseJSON(data);

    for(var obj in objs){
        var html = template;
        html = html.replace('FIRST',    objs[obj].vwmeritbadgecounselor.FirstName);
        html = html.replace('MIDDLE',   objs[obj].vwmeritbadgecounselor.MiddleName);
        html = html.replace('LAST',     objs[obj].vwmeritbadgecounselor.LastName);
        html = html.replace('DISTANCE', Number(objs[obj][0].Distance).toFixed(1) );
//        html = html.replace('ADDRESS1', objs[obj].vwmeritbadgecounselor.Address1);
//        var add2 = objs[obj].vwmeritbadgecounselor.Address2.trim();
//        if (add2.length == 0){
//            html = html.replace('ADDRESS2<br/>', '');
//        }
//        else{
//            html = html.replace('ADDRESS2', add2);
//        }
//        html = html.replace('CITY',     objs[obj].vwmeritbadgecounselor.City);
//        html = html.replace('STATE',    objs[obj].vwmeritbadgecounselor.State);
//        html = html.replace('ZIP',      objs[obj].vwmeritbadgecounselor.Zip);
        if (objs[obj].vwmeritbadgecounselor.troop_only == 'N'){
            html = html.replace('PHONE', objs[obj].vwmeritbadgecounselor.Phone);
        }
        else{
            html = html.replace('PHONE', '<span class="troopOnly">(counselor for troop only)</span>');
        }
        $('#mbcContainer' + objs[obj].vwmeritbadgecounselor.meritbadges_id).append(html);
    }
}

function addMeritBadgeContainers(){
    var labels = $( "input.mbcheckbox:checked+label" );
    if (labels.length > 0){
        for (var i = 0; i<labels.length; i++){
            var txt = $(labels[i]).text().trim();
            $('#counselorSet').append('<div id="mbcContainer' + $(labels[i]).attr('for').replace('mb','') + '" class="mbcContainer"><div class="mbcContainerTitle">'+txt+'</div></div>');
        }
    }
}

var countChecked = function(className) {
    return $( "input."+ className +":checked" ).length;
}

function clearCheckboxes(){
    $('.mbcheckbox').removeAttr('checked')
}