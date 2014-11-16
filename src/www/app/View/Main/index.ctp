<?php
    $this->Html->script('main', array('inline' => false));

    Configure::load('mbcounts', 'default');
    $address = Configure::read('council_info.address');
    $city = Configure::read('council_info.city');
    $state = Configure::read('council_info.state');
    $zip = Configure::read('council_info.zip');

?>
<h1>Merit Badge Counselors</h1>
<p>This page will help you find registered merit badge counselors</p>

<p>Your address is</p>
<div class="data">
    Address <input id="address" type="text" value="<?=$address?>" ><br/>
    City <input id="city" type="text" value="<?=$city?>" ><br/>
    State <input id="state" type="text" value="<?=$state?>"><br/>
    Zip <input id="zip" type="text" value="<?=$zip?>"><br/>
</div>

<p>1. Select one or more merit badges:</p>
<div class="data" id="meritbadges" >
<?php
    $colcnt = 4;
    $chxPerCol = 130/$colcnt;
    $i = 0;
    $c=0;

    foreach ($meritbadges as $badge){
        if ($i == 0){
            $c++;
            echo '<div class="mbcol" id="mbcol"'.$c.'>';
        }

        $s = '<input
                    id="mb' . $badge['Meritbadge']['id'] . '"
                    class="mbcheckbox"
                    type="checkbox"
                    value="' . $badge['Meritbadge']['id'] . '"
                    name="' . $badge['Meritbadge']['name'] . '"
                ><label class="mbcheckbox" for="mb' . $badge['Meritbadge']['id'] . '">'.$badge['Meritbadge']['name'].'</label><br/>';
        echo $s;

        if ($i > $chxPerCol){
            $i = 0;
            echo '</div>';
        }
        else{
            $i++;
        }
    }
    echo '</div>  <div style="clear: both;"></div>';
?>

    <a style="font-size: x-small;" onclick="clearCheckboxes(); return false;" href="">reset checkboxes</a>
</div>

<p>2. Select a range of miles to search within (as the crow flies):</p>
    <select id="range" class="data">
        <option value="0.5">0.5 mile</option>
        <option value="1.0">1 mile</option>
        <option value="2.0">2 miles</option>
        <option value="3.0" selected="selected">3 miles</option>
        <option value="5.0">5 miles</option>
        <option value="10">10 miles</option>
        <option value="1000">Any distance</option>
    </select>

<p>3. Update results <input type="button" value="Update" onclick="updateCounselorSet();"></p>
<div id="counselorSet">

</div>
