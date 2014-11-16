<?
    $i = 0;
    $processedIds = isset($processedIds) ? $processedIds : array();

    echo $header . "<br/><br/>";

    foreach ($processedIds as $id){
        $i++;
        echo $i . ' &nbsp;'. $id . '<br/>';
    }

?>
