<p>
    This page allows administrators of [COUNCILNAME/DISTRICTNAME/ETC] to update the
    counselor list with the most up-to-date data.  If you have accessed
    this page and you are not an administrator, please report it to the
    Ore-Ida council office.
</p>

<?php

    if (isset($processCSVSuccess) && $processCSVSuccess==true) {
        echo "Successfully processed new data.";
        echo $this->Html->para("","");
        echo $this->Html->link('Main Page', '/Main');
    }
    else{
        echo $this->Form->create('CSVUploads', array('enctype' => 'multipart/form-data'));
        $submitOptions = array();
        $submitOptions['value'] = "Choose CSV File";
        echo $this->Form->file('CSVUpload.submittedfile', $submitOptions);
        echo $this->Form->submit('Replace data on the server with this CSV file');
    }