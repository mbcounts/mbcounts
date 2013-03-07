<html>
<body>
RAW COUNSELORS
<?php foreach ($rawcounselors as $c): ?>

    <?php echo $c['RawCounselor']['First Name']." "; ?>
    <?php echo $c['RawCounselor']['Last Name']." "; ?>
    <?php echo $c['RawCounselor']['Address 1']; ?><br/>

<?php endforeach; ?>
<?php unset($c); ?>

</body>
</html>
