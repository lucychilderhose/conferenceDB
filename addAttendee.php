<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connectdb.php';

$attendeeType = $_GET['attendeeType'] ?? ($_POST['attendeeType'] ?? '');
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fName = trim($_POST['fName'] ?? '');
    $lName = trim($_POST['lName'] ?? '');
    
    if ($_POST['attendeeType'] === "Student") {
        $fee = "50";
    } elseif ($_POST['attendeeType'] === "Professional") {
        $fee = "100";
    } elseif ($_POST['attendeeType'] === "Sponsor") {
        $fee = "0";
    } else {
        $fee = "0";
    }
    $attendeeType = $_POST['attendeeType'] ?? '';
    
    if ($fName && $lName && $attendeeType) {
      
        $stmtId = $connection->query("SELECT COALESCE(MAX(id), 0) + 1 AS newId FROM attendee");
        $row = $stmtId->fetch(PDO::FETCH_ASSOC);
        $newId = $row['newId'];
        

        $stmtInsert = $connection->prepare("INSERT INTO attendee (id, fName, lName, fee) VALUES (?, ?, ?, ?)");
        $stmtInsert->execute([$newId, $fName, $lName, $fee]);
        
        if ($attendeeType === "Student") {
            $roomNum = $_POST['studentRoom'] ?? '';
            if ($roomNum) {
                $stmtStudent = $connection->prepare("INSERT INTO student (id, roomNum) VALUES (?, ?)");
                $stmtStudent->execute([$newId, $roomNum]);
                $message = "New student added with ID $newId.";
            } else {
                $message = "Error: Please select a hotel room.";
            }
        } elseif ($attendeeType === "Professional") {
            $stmtProf = $connection->prepare("INSERT INTO professional (id) VALUES (?)");
            $stmtProf->execute([$newId]);
            $message = "New professional added with ID $newId.";
        } elseif ($attendeeType === "Sponsor") {
            $sponsorCompany = $_POST['sponsorCompany'] ?? '';
            if ($sponsorCompany) {
                $stmtSponsor = $connection->prepare("INSERT INTO sponsor (id, companyName) VALUES (?, ?)");
                $stmtSponsor->execute([$newId, $sponsorCompany]);
                $message = "New sponsor added with ID $newId.";
            } else {
                $message = "Error: Please select a sponsoring company.";
            }
        } else {
            $message = "Error: Invalid attendee type selected.";
        }
    } else {
        $message = "Error: All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add New Attendee</title>
</head>
<body>
  <h1>Add New Attendee</h1>
  <?php
  if (!empty($message)) {
      echo "<p>" . htmlspecialchars($message) . "</p>";
  }
  ?>
  <p>Attendee Type: <?php echo htmlspecialchars($attendeeType); ?></p>
  
  <form method="post" action="addAttendee.php">
 
    <input type="hidden" name="attendeeType" value="<?php echo htmlspecialchars($attendeeType); ?>">
    
    <label for="fName">First Name:</label>
    <input type="text" name="fName" id="fName" required>
    <br>
    
    <label for="lName">Last Name:</label>
    <input type="text" name="lName" id="lName" required>
    <br>
    
   
    <p>Registration Fee: 
      <?php
        if ($attendeeType === "Student") {
            echo "$50";
        } elseif ($attendeeType === "Professional") {
            echo "$100";
        } elseif ($attendeeType === "Sponsor") {
            echo "$0 (Free)";
        }
      ?>
    </p>
    
    <?php if ($attendeeType === "Student"): ?>
     
      <label for="studentRoom">Select Hotel Room:</label>
      <select name="studentRoom" id="studentRoom" required>
        <?php
         
          $queryRoom = "SELECT num FROM room";
          $stmtRoom = $connection->query($queryRoom);
          $rooms = $stmtRoom->fetchAll(PDO::FETCH_ASSOC);
          if ($rooms && count($rooms) > 0) {
              foreach ($rooms as $row) {
                  echo '<option value="' . htmlspecialchars($row['num']) . '">' . htmlspecialchars($row['num']) . '</option>';
              }
          } else {
              echo '<option value="">No rooms available</option>';
          }
        ?>
      </select>
      <br>
    <?php elseif ($attendeeType === "Sponsor"): ?>
   
      <label for="sponsorCompany">Select Sponsoring Company:</label>
      <select name="sponsorCompany" id="sponsorCompany" required>
        <?php
          $queryComp = "SELECT name FROM company ORDER BY name ASC";
          $stmtComp = $connection->query($queryComp);
          $companies = $stmtComp->fetchAll(PDO::FETCH_ASSOC);
          if ($companies && count($companies) > 0) {
              foreach ($companies as $row) {
                  echo '<option value="' . htmlspecialchars($row['name']) . '">' . htmlspecialchars($row['name']) . '</option>';
              }
          } else {
              echo '<option value="">No companies available</option>';
          }
        ?>
      </select>
      <br>
    <?php endif; ?>
    
    <input type="submit" value="Add Attendee">
  </form>
  <p><a href="conference.php">Return to Main Page</a></p>
</body>
</html>
