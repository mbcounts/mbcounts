Welcome, Admin

<p>This site requires very little from you, but it does require something: it is only as good as the data <b>you</b> supply to it.</p>

<p>Fortunately, it's easy to update.</p>

<p><img src="app/webroot/img/done.png" alt="done" class="checkCircle"> This site requires a basic schema.  And that's already been put in place for you by a manually run script called schema.sql.</p>

<p><img src="app/webroot/img/not_done.png" alt="not done" class="checkCircle"> You need to supply the system with the
    merit badge counselor data by uploading a CSV (Comma Separated Values) file.
    Click here to do that: <?php

        echo $this->Html->link(
            'Upload CSV File',
            array(
                'controller' => 'CSVUploads',
                'action' => 'index',
                'full_base' => true
            )
        );
    ?>  </p>
<!--<p><img src="app/webroot/img/periodically_done.png" alt="not done" class="checkCircle"> Periodically you should get the latest merit badge list from the BSA site.  Click <a href="/meritBadges">here</a> to do that.</p>-->

<p><img src="app/webroot/img/not_done.png" alt="not done" class="checkCircle"> This system maintains geocode data by address hash for future use.  What that means is that it stores a latitude and longitude value along side a hash of an address (a hash is just a long string of characters that represents a value.  For example, the scout office hashed (sha1) address (full address including city, state and zip; upper-cased; space separated) might look something like "6d99700f39df4616da28d0a7586785c067367dcb".  The hash is generated by the database engine.  The geocode data is requested from Texas A&M  </p>
<p><img src="app/webroot/img/not_done.png" alt="not done" class="checkCircle"> </p>
<p><img src="app/webroot/img/not_done.png" alt="not done" class="checkCircle"> </p>

<p></p>

<p></p>

<p></p>

<p></p>

<p></p>

<p></p>

<p></p>

<p></p>

