<div class="badge-table">
    <!-- header row-->
    <div class="badge-row header-row">
        <div class="badge-cell">take action</div>
        <div class="badge-cell">#</div>
        <div class="badge-cell">BSA</div>
        <div class="badge-cell">Council</div>
    </div>

    <?php
    $i = 0;

    foreach($combinedBadges as $pair){
        $i++;
        echo('<div id="row'.$i.'" class="badge-row">');
        echo('    <div id="row'.$i.'chk" class="badge-cell"><input type="checkbox" data-rowindex="'.$i.'" class="mb-compare-checkbox" style="width: 20px;" ></div>');
        echo('    <div id="row'.$i.'num" class="badge-cell">'.$i.'</div>');
        echo('    <div id="row'.$i.'bsa" class="badge-cell">'.$pair[0].'</div>');
        echo('    <div id="row'.$i.'cou" class="badge-cell">'.$pair[1].'</div>');
        echo('</div>');
    }
    ?>
</div>

<script>
    $( ".mb-compare-checkbox" )
        .change(function () {
            checked = $(this.checked)[0];
            if (checked){
                style = {'background-color': 'lightyellow'};
            }
            else{
                style = {'background-color': 'white'};
            }
            var id = $(this).data('rowindex');
            $('#row'+id).css(style);
        });

</script>