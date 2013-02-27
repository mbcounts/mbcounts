<h1>MB Counselors</h1>
<table>
    <tr>
        <th>Id</th>
        <th>First Name</th>
        <th>Last Name</th>
    </tr>

    <!-- Here is where we loop through our $counselors array, printing out counselor info -->

    <?php foreach ($counselors as $counselor): ?>
    <tr>
        <td><?php echo $counselor['Counselor']['id']; ?></td>
        <td><?php echo $counselor['Counselor']['first_name']; ?></td>
        <td><?php echo $counselor['Counselor']['last_name']; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($counselor); ?>
</table>