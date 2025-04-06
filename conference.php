<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connectdb.php';

$message_company = "";
$message_delete  = "";
$message_session = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    switch ($_POST['task']) {
        case '10': 
            $name  = trim($_POST['companyName'] ?? '');
            $level = trim($_POST['level'] ?? '');
            if ($name === '' || $level === '') {
                $message_company = "Both name and level are required.";
            } else {
                
                $stmt = $connection->prepare(
                  "INSERT INTO company (name, level) VALUES (:name, :level)"
                );
                try {
                    $stmt->execute([':name' => $name, ':level' => $level]);
        
                    
                    $nextId = $connection
                      ->query("SELECT COALESCE(MAX(id), 0) + 1 FROM sponsor")
                      ->fetchColumn();
        
                    
                    $stmt2 = $connection->prepare(
                      "INSERT INTO sponsor (id, companyName) VALUES (:id, :name)"
                    );
                    $stmt2->execute([':id' => $nextId, ':name' => $name]);
        
                    $message_company = "Company '{$name}' added and sponsored.";
                } catch (PDOException $e) {
                    $message_company = "Error adding company: " . $e->getMessage();
                }
            }
            break;
        


        case '11':
            $del = $_POST['deleteCompany'] ?? '';
            if ($del === '') {
                $message_delete = "Please select a company to delete.";
            } else {
                $stmt = $connection->prepare("DELETE FROM company WHERE name = :name");
                $stmt->execute([':name' => $del]);
                $message_delete = "Company '{$del}' deleted.";
            }
            break;

        case '12':
            list($loc, $date, $start) = explode('|', $_POST['selectedSession']);
            $fields = [];
            $params = [':loc'=>$loc, ':date'=>$date, ':start'=>$start];
            if (!empty($_POST['newLocation'])) {
                $fields[] = "location = :newloc";
                $params[':newloc'] = $_POST['newLocation'];
            }
            if (!empty($_POST['newDate'])) {
                $fields[] = "sessionDate = :newdate";
                $params[':newdate'] = $_POST['newDate'];
            }
            if (!empty($_POST['newStartTime'])) {
                $fields[] = "startTime = :newstart";
                $params[':newstart'] = $_POST['newStartTime'];
            }
            if (!empty($_POST['newEndTime'])) {
                $fields[] = "endTime = :newend";
                $params[':newend'] = $_POST['newEndTime'];
            }
            if ($fields) {
                $sql = "UPDATE session SET " . implode(', ', $fields)
                     . " WHERE location = :loc AND sessionDate = :date AND startTime = :start";
                $stmt = $connection->prepare($sql);
                $stmt->execute($params);
                $message_session = "Session updated.";
            } else {
                $message_session = "No changes specified.";
            }
            break;
    }
}

$subcommittees = $connection->query("SELECT DISTINCT name FROM subcommittee")->fetchAll(PDO::FETCH_ASSOC);
$rooms          = $connection->query("SELECT num FROM room")->fetchAll(PDO::FETCH_ASSOC);
$companies      = $connection->query("SELECT name FROM company ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$totalFees = $connection
    ->query("SELECT SUM(CASE fee WHEN '50' THEN 50 WHEN '100' THEN 100 ELSE 0 END) FROM attendee")
    ->fetchColumn() ?: 0;

// levels -> amounts
$mapping = ['Platinum'=>10000,'Gold'=>5000,'Silver'=>3000,'Bronze'=>1000];
$cases = [];
foreach ($mapping as $lvl => $amt) {
    $cases[] = "WHEN '$lvl' THEN $amt";
}
$sql = "
    SELECT SUM(
        CASE level
            " . implode("\n            ", $cases) . "
            ELSE 0
        END
    ) 
    FROM company 
    WHERE name IN (SELECT DISTINCT companyName FROM sponsor)
";
$totalSponsorship = $connection->query($sql)->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Conference Database</title>
  <style>
    :root {
      --card-bg:       #ffffff;
      --card-shadow:   rgba(0,0,0,0.1);
      --radius:        10px;
      --gap:           20px;
      --padding:       16px;
      --font:          'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      --btn-blue:      #007BFF;
      --btn-green:     #28A745;
      --btn-red:       #DC3545;
      --text-dark:     #333333;
    }
    body {
      background-image: url('bg.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-position: center;
      font-family: var(--font);
      margin: 0;
      padding: 24px;
      color: var(--text-dark);
    }
    h1 { 
      text-align: center; margin-bottom: 24px; 
      color: white;
      font-size: 2.5rem;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: var(--gap);
    }
    .card {
      background: var(--card-bg);
      border-radius: var(--radius);
      padding: var(--padding);
      box-shadow: 0 4px 8px var(--card-shadow);
    }
    .card h2 {
      margin-top: 0;
      font-size: 1.1rem;
    }
    .card label,
    .card select,
    .card input[type="date"],
    .card input[type="time"],
    .card input[type="number"] {
      width: 100%;
      margin: 8px 0;
    }
    .card p { margin: 8px 0; }
    .card form { display: flex; flex-direction: column; }
    .card input[type="submit"] {
      margin-top: 12px;
      padding: 10px;
      border: none;
      border-radius: 6px;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
    }
    .btn-view    { background: var(--btn-blue); }
    .btn-add     { background: var(--btn-green); }
    .btn-delete  { background: var(--btn-red); }
  </style>
</head>
<body>

  <h1>Conference Database</h1>
  <div class="grid">

    <div class="card">
      <h2>1. View Sub-Committee Members</h2>
      <form action="subcmemberPage.php" method="post">
        <label for="SubCommittee">Sub-Committee:</label>
        <select name="SubCommittee" id="SubCommittee">
          <?php foreach ($subcommittees as $r): ?>
            <option><?= htmlspecialchars($r['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="submit" value="View Members" class="btn-view">
      </form>
    </div>

    <div class="card">
      <h2>2. View Guests in Room</h2>
      <form action="roomStudentPage.php" method="post">
        <label for="roomNum">Room:</label>
        <select name="roomNum" id="roomNum">
          <?php foreach ($rooms as $r): ?>
            <option><?= htmlspecialchars($r['num']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="submit" value="View Students" class="btn-view">
      </form>
    </div>

    <div class="card">
      <h2>3. View Conference Schedule</h2>
      <form action="schedulePage.php" method="post">
        <label for="conferenceDate">Date:</label>
        <input type="date" name="conferenceDate" id="conferenceDate" required>
        <input type="submit" value="View Schedule" class="btn-view">
      </form>
    </div>

    <div class="card">
      <h2>4. View Sponsors and Rank</h2>
      <form action="sponsorRankPage.php" method="post">
        <p>All companies & their sponsor rank:</p>
        <input type="submit" value="View Sponsor Ranks" class="btn-view">
      </form>
    </div>

    <div class="card">
      <h2>5. View Jobs at Companies</h2>
      <form action="jobListPage.php" method="post">
        <label for="companyJob">Company:</label>
        <select name="companyJob" id="companyJob">
          <option value="all">All Companies</option>
          <?php foreach ($companies as $r): ?>
            <option><?= htmlspecialchars($r['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="submit" value="View Jobs" class="btn-view">
      </form>
    </div>

    <div class="card">
      <h2>6. View Attendees</h2>
      <form action="viewmembers.php" method="post">
        <label for="category">Category:</label>
        <select name="category" id="category">
          <option>Student</option>
          <option>Professional</option>
          <option>Sponsor</option>
        </select>
        <input type="submit" value="View Members" class="btn-view">
      </form>
    </div>

    <div class="card">
      <h2>7. Add a New Attendee</h2>
      <form action="addAttendee.php" method="get">
        <label for="attendeeType">Type:</label>
        <select name="attendeeType" id="attendeeType" required>
          <option value="">Select Type</option>
          <option>Student</option>
          <option>Professional</option>
          <option>Sponsor</option>
        </select>
        <input type="submit" value="Add Attendee" class="btn-add">
      </form>
    </div>

    <div class="card">
      <h2>8. Show Total Intake</h2>
      <p>Registration Fees: $<?= htmlspecialchars($totalFees) ?></p>
      <p>Sponsorship: $<?= htmlspecialchars($totalSponsorship) ?></p>
    </div>

    <div class="card">
      <h2>9. Add Sponsoring Company</h2>
      <?php if ($message_company): ?><p><?= htmlspecialchars($message_company) ?></p><?php endif; ?>
      <form method="post" action="conference.php">
        <label for="companyName">Name:</label>
        <input type="text" name="companyName" id="companyName" required>
        <label for="level">Level:</label>
        <select name="level" id="level" required>
          <option value="">Select Level</option>
          <option>Platinum</option>
          <option>Gold</option>
          <option>Silver</option>
          <option>Bronze</option>
        </select>
        <input type="hidden" name="task" value="10">
        <input type="submit" value="Add Company" class="btn-add">
      </form>
    </div>

    <div class="card">
      <h2>10. Delete Sponsoring Company</h2>
      <?php if ($message_delete): ?><p><?= htmlspecialchars($message_delete) ?></p><?php endif; ?>
      <form method="post" action="conference.php">
        <label for="deleteCompany">Company:</label>
        <select name="deleteCompany" id="deleteCompany" required>
          <?php
            $scs = $connection
              ->query("SELECT DISTINCT c.name FROM company c JOIN sponsor s ON c.name=s.companyName")
              ->fetchAll(PDO::FETCH_ASSOC);
            foreach ($scs as $r): ?>
            <option><?= htmlspecialchars($r['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="task" value="11">
        <input type="submit" value="Delete Company" class="btn-delete">
      </form>
    </div>

    <div class="card">
      <h2>11. Switch Session Details</h2>
      <?php if ($message_session): ?><p><?= htmlspecialchars($message_session) ?></p><?php endif; ?>
      <form method="post" action="conference.php">
        <label for="selectedSession">Event:</label>
        <select name="selectedSession" id="selectedSession" required>
          <option value="">--Select--</option>
          <?php
            $sess = $connection
              ->query("SELECT location,sessionDate,startTime FROM session ORDER BY sessionDate,startTime")
              ->fetchAll(PDO::FETCH_ASSOC);
            foreach ($sess as $s):
              $val = "{$s['location']}|{$s['sessionDate']}|{$s['startTime']}";
              $lbl = "Loc: {$s['location']}, Date: {$s['sessionDate']}, Start: {$s['startTime']}";
          ?>
            <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($lbl) ?></option>
          <?php endforeach; ?>
        </select>
        <label for="newLocation">New Location:</label>
        <input type="number" name="newLocation" id="newLocation">
        <label for="newDate">New Date:</label>
        <input type="date" name="newDate" id="newDate">
        <label for="newStartTime">New Start Time:</label>
        <input type="time" name="newStartTime" id="newStartTime">
        <label for="newEndTime">New End Time:</label>
        <input type="time" name="newEndTime" id="newEndTime">
        <input type="hidden" name="task" value="12">
        <input type="submit" value="Update Session" class="btn-view">
      </form>
    </div>

  </div>
</body>
</html>
