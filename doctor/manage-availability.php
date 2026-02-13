<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['damsid']==0)) {
    header('location:logout.php');
} else {
    if(isset($_POST['submit'])) {
        $did = $_SESSION['damsid'];
        $dayOfWeek = $_POST['dayOfWeek'];
        $startTime = $_POST['startTime'];
        $endTime = $_POST['endTime'];
        $maxAppointments = $_POST['maxAppointments'];

        $sql = "INSERT INTO tbldoctoravailability(DoctorID, DayOfWeek, StartTime, EndTime, MaxAppointments) 
                VALUES(:did, :dayOfWeek, :startTime, :endTime, :maxAppointments)
                ON DUPLICATE KEY UPDATE 
                StartTime = VALUES(StartTime),
                EndTime = VALUES(EndTime),
                MaxAppointments = VALUES(MaxAppointments)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':did', $did, PDO::PARAM_STR);
        $query->bindParam(':dayOfWeek', $dayOfWeek, PDO::PARAM_STR);
        $query->bindParam(':startTime', $startTime, PDO::PARAM_STR);
        $query->bindParam(':endTime', $endTime, PDO::PARAM_STR);
        $query->bindParam(':maxAppointments', $maxAppointments, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            echo '<script>alert("Availability updated successfully.")</script>';
        } else {
            echo '<script>alert("Something Went Wrong. Please try again")</script>';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>DAMS - Manage Availability</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <?php include_once('includes/header.php');
          include_once('includes/sidebar.php');?>
    <div class="main-content">
        <div class="wrap-content container" id="container">
            <div class="container-fluid container-fullw bg-white">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row margin-top-30">
                            <div class="col-lg-8 col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">Manage Availability</h5>
                                    </div>
                                    <div class="panel-body">
                                        <form role="form" method="post" >
                                            <div class="form-group">
                                                <label for="dayOfWeek">Day of Week</label>
                                                <select name="dayOfWeek" class="form-control" required>
                                                    <option value="0">Sunday</option>
                                                    <option value="1">Monday</option>
                                                    <option value="2">Tuesday</option>
                                                    <option value="3">Wednesday</option>
                                                    <option value="4">Thursday</option>
                                                    <option value="5">Friday</option>
                                                    <option value="6">Saturday</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="startTime">Start Time</label>
                                                <input type="time" name="startTime" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="endTime">End Time</label>
                                                <input type="time" name="endTime" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="maxAppointments">Maximum Appointments Per Slot</label>
                                                <input type="number" name="maxAppointments" class="form-control" min="1" max="50" value="10" required>
                                            </div>
                                            <button type="submit" name="submit" class="btn btn-primary">Update Availability</button>
                                        </form>

                                        <div class="mt-4">
                                            <h4>Current Availability Schedule</h4>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Day</th>
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
                                                        <th>Max Appointments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $did = $_SESSION['damsid'];
                                                    $sql = "SELECT * FROM tbldoctoravailability WHERE DoctorID=:did ORDER BY DayOfWeek";
                                                    $query = $dbh->prepare($sql);
                                                    $query->bindParam(':did', $did, PDO::PARAM_STR);
                                                    $query->execute();
                                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                                    foreach($results as $row) {
                                                        echo "<tr>";
                                                        echo "<td>".$days[$row->DayOfWeek]."</td>";
                                                        echo "<td>".date('h:i A', strtotime($row->StartTime))."</td>";
                                                        echo "<td>".date('h:i A', strtotime($row->EndTime))."</td>";
                                                        echo "<td>".$row->MaxAppointments."</td>";
                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>