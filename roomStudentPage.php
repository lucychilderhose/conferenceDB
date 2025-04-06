<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'connectdb.php';


if (!isset($_POST["roomNum"])) {
    echo "No room selected. Please go back and choose a room.";
    exit;
}

$roomNum = $_POST["roomNum"];


$query = "SELECT a.fName, a.lName 
          FROM student s
          JOIN attendee a ON s.id = a.id
          WHERE s.roomNum = :roomNum";


$stmt = $connection->prepare($query);
$stmt->bindValue(':roomNum', $roomNum);
$stmt->execute();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Students in Room <?php echo htmlspecialchars($roomNum); ?></title>
</head>
<body>
    <h1>Students in Room <?php echo htmlspecialchars($roomNum); ?></h1>
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
