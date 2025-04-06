<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connectdb.php';

$output = "";
$companyJob = "";
$sortOption = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['companyJob'])) {
        $companyJob = $_POST['companyJob'];
    }
    if (isset($_POST['sortOption'])) {
        $sortOption = $_POST['sortOption'];
    }
}

if ($companyJob === "all") {
    
    if (empty($sortOption)) {
        $sortOption = "alphabetical";
    }
    
  
    if ($sortOption === "alphabetical") {
        
        $query = "SELECT companyName, jobTitle, salary, location 
                  FROM jobAd 
                  ORDER BY companyName ASC, jobTitle ASC";
    } elseif ($sortOption === "salary") {
       
        $query = "SELECT companyName, jobTitle, salary, location 
                  FROM jobAd 
                  ORDER BY salary ASC, companyName ASC";
    } else {
        
        $query = "SELECT companyName, jobTitle, salary, location 
                  FROM jobAd 
                  ORDER BY companyName ASC, jobTitle ASC";
    }
    
    $stmt = $connection->query($query);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $output .= "<h1>All Jobs</h1>";
    

    $output .= '<form method="post" action="jobListPage.php">
        <input type="hidden" name="companyJob" value="all">
        <label for="sortOption">Sort Jobs By:</label>
        <select name="sortOption" id="sortOption">
            <option value="alphabetical"' . ($sortOption === "alphabetical" ? " selected" : "") . '>Alphabetical (Company & Position)</option>
            <option value="salary"' . ($sortOption === "salary" ? " selected" : "") . '>Salary (Lowest to Highest)</option>
        </select>
        <input type="submit" value="Sort">
      </form><br>';
    
    if ($jobs) {
        $output .= "<table border='1' cellpadding='5'>";
        $output .= "<tr><th>Company</th><th>Job Title</th><th>Salary</th><th>Location</th></tr>";
        foreach ($jobs as $job) {
            $output .= "<tr>
                          <td>" . htmlspecialchars($job['companyName']) . "</td>
                          <td>" . htmlspecialchars($job['jobTitle']) . "</td>
                          <td>" . htmlspecialchars($job['salary']) . "</td>
                          <td>" . htmlspecialchars($job['location']) . "</td>
                        </tr>";
        }
        $output .= "</table>";
    } else {
        $output .= "<p>No jobs available.</p>";
    }
} elseif (!empty($companyJob)) {
    
    $query = "SELECT jobTitle, salary, location 
              FROM jobAd 
              WHERE companyName = ? 
              ORDER BY jobTitle ASC";
    $stmt = $connection->prepare($query);
    $stmt->execute([$companyJob]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $output .= "<h1>Jobs at " . htmlspecialchars($companyJob) . "</h1>";
    if ($jobs) {
        $output .= "<table border='1' cellpadding='5'>";
        $output .= "<tr><th>Job Title</th><th>Salary</th><th>Location</th></tr>";
        foreach ($jobs as $job) {
            $output .= "<tr>
                          <td>" . htmlspecialchars($job['jobTitle']) . "</td>
                          <td>" . htmlspecialchars($job['salary']) . "</td>
                          <td>" . htmlspecialchars($job['location']) . "</td>
                        </tr>";
        }
        $output .= "</table>";
    } else {
        $output .= "<p>No jobs listed for " . htmlspecialchars($companyJob) . ".</p>";
    }
} else {
    $output .= "<p>Please select a company.</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Jobs Listing</title>
</head>
<body>
  <?php echo $output; ?>
  <br>
</body>
</html>
