<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connectdb.php';

$output = "";


$queryRank = "SELECT name, level FROM company ORDER BY FIELD(level, 'Gold', 'Platinum', 'Silver', 'Bronze'), name ASC";
$stmtRank = $connection->query($queryRank);
$companiesRank = $stmtRank->fetchAll(PDO::FETCH_ASSOC);

$output .= "<h1>Companies and Sponsor Rank</h1>";
if ($companiesRank) {
    $output .= "<table border='1' cellpadding='5'>";
    $output .= "<tr><th>Company Name</th><th>Sponsor Level</th></tr>";
    foreach ($companiesRank as $comp) {
        $output .= "<tr>
                      <td>" . htmlspecialchars($comp['name']) . "</td>
                      <td>" . htmlspecialchars($comp['level']) . "</td>
                    </tr>";
    }
    $output .= "</table>";
} else {
    $output .= "<p>No companies found.</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sponsor Levels</title>
</head>
<body>
  <?php echo $output; ?>
  <br>
</body>
</html>
