<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'connectdb.php';


if (!isset($_POST["SubCommittee"])) {
    echo "No subcommittee selected. Please go back and choose one.";
    exit;
}

$subcommittee = $_POST["SubCommittee"];


$query = "SELECT m.fName, m.lName 
          FROM member m
          JOIN memberOf mo ON m.id = mo.id
          WHERE mo.subcommittee = :subcommittee";


$stmt = $connection->prepare($query);
$stmt->bindValue(':subcommittee', $subcommittee);
$stmt->execute();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Members in SubCommittee "<?php echo htmlspecialchars($subcommittee); ?>"</title>
</head>
<body>
<h1>Members in SubCommittee "<?php echo htmlspecialchars($subcommittee); ?>"</h1>
<table border="1">
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
    </tr>
    <?php

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>" . htmlspecialchars($row["fName"]) . "</td>
                  <td>" . htmlspecialchars($row["lName"]) . "</td></tr>";
    }
    ?>
</table>
<?php

$connection = null;
?>
</body>
</html>
