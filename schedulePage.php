<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connectdb.php';

$output = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['conferenceDate'])) {
    $conferenceDate = $_POST['conferenceDate'];
    
    
    $query = "SELECT s.location, s.sessionDate, s.startTime, s.endTime, a.fName, a.lName 
              FROM session s 
              JOIN speaker sp ON s.speakerID = sp.id 
              JOIN attendee a ON sp.id = a.id 
              WHERE s.sessionDate = ?
              ORDER BY s.startTime";
    $stmt = $connection->prepare($query);
    $stmt->execute([$conferenceDate]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $output .= "<h1>Conference Schedule for " . htmlspecialchars($conferenceDate) . "</h1>";
    if ($sessions) {
        $output .= "<table border='1' cellpadding='5'>";
        $output .= "<tr><th>Location</th><th>Start Time</th><th>End Time</th><th>Speaker</th></tr>";
        foreach ($sessions as $session) {
            $speaker = htmlspecialchars($session['fName']) . " " . htmlspecialchars($session['lName']);
            $output .= "<tr>
                         <td>" . htmlspecialchars($session['location']) . "</td>
                         <td>" . htmlspecialchars($session['startTime']) . "</td>
                         <td>" . htmlspecialchars($session['endTime']) . "</td>
                         <td>" . $speaker . "</td>
                        </tr>";
        }
        $output .= "</table>";
    } else {
        $output .= "<p>No sessions scheduled for this date.</p>";
    }
} else {
    $output .= "<p>Please select a valid conference date.</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Conference Schedule</title>
</head>
<body>
  <?php echo $output; ?>
  <br>
</body>
</html>
