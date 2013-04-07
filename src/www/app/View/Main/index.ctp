<?php
   $this->Html->script('main', array('inline' => false));
?>
<h1>Merit Badge Counselors</h1>
<p>This page will help you find registered merit badge counselors in the following district(s)
    <ul class="data">
        <li>Gem State</li>
    <!--    <li>Centennial</li>-->
    <!--    <li>Oregon Trail</li>-->
    <!--    <li>Seven Rivers</li>-->
    </ul>
</p>

<p>You are logged in as:</p>
<div class="data" id="user_fullname">
    Scout Office User
</div>

<p>Your address is</p>
<div class="data" id="user_address">
    8901 W Franklin Road<br/>
    Boise ID 83709
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
        $s = '<label>
                <input
                    id="mb' . $badge['Meritbadge']['id'] . '"
                    class="mbcheckbox"
                    type="checkbox"
                    value="' . $badge['Meritbadge']['id'] . '"
                    name="' . $badge['Meritbadge']['name'] . '"
                >'.$badge['Meritbadge']['name'].'</input></label><br/>';
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
</div>
<p>2. Select a range of miles to search within:</p>
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
