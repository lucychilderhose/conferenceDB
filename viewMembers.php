<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>View Members</title>
</head>
<body>
<?php
    include 'connectdb.php';

    if (!isset($_POST["category"])) {
        echo "No category selected.";
        exit;
    }
    $whichCategory = $_POST["category"];
    if ($whichCategory === "Professional") {
        $query = "
            SELECT a.fName, a.lName
            FROM attendee a
            JOIN professional p ON a.id = p.id
        ";
    } elseif ($whichCategory === "Student") {
        $query = "
            SELECT a.fName, a.lName
            FROM attendee a
            JOIN student s ON a.id = s.id
        ";
    } elseif ($whichCategory === "Sponsor") {
        $query = "
            SELECT a.fName, a.lName
            FROM attendee a
            JOIN sponsor sp ON a.id = sp.id
        ";
    } else {
       
        echo "Invalid category selected.";
        exit;
    }

  
    $stmt = $connection->prepare($query);
    $stmt->execute();
?>

<h1>List of <?php echo htmlspecialchars($whichCategory); ?> Members</h1>
<table border="1">
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
    </tr>
<?php
   
    while ($row = $stmt->fetch()) {
        echo "<tr><td>" . htmlspecialchars($row["fName"]) . "</td>
                  <td>" . htmlspecialchars($row["lName"]) . "</td>
              </tr>";
    }
?>
</table>

<?php
    
    $connection = null;
?>
</body>
</html>
