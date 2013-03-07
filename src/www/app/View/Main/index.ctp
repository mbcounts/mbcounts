<h1>Merit Badge Counselors</h1>
<p>This page will help you find registered merit badge counselors in the following districts</p>
<input class="data" type="checkbox" value="" >Gem State</input>

<p>You are logged in as:</p>
<div class="data" id="user_fullname">
    Jim Magelby
</div>

<p>Your address is</p>
<div class="data" id="user_address">
    1029 W. Sequin Ct<br/>
    Parma, ID 85675
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
        $s = '<input class="mbcheckbox" type="checkbox">'.$badge['Meritbadge']['name'].'</input><br/>';
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
<p></p>