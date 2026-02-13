<?php
session_start();
error_reporting(0);
include('doctor/includes/dbconnection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Check Waitlist Status</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/templatemo-medic-care.css" rel="stylesheet">
</head>
<body>
    <?php include_once('includes/header.php');?>
    
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Check Waitlist Status</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if(isset($_GET['aptnumber'])) {
                            $aptnumber = $_GET['aptnumber'];
                            $sql = "SELECT w.*, d.FullName as DoctorName, sp.Specialization 
                                   FROM tblwaitlist w 
                                   JOIN tbldoctor d ON w.Doctor = d.ID 
                                   JOIN tblspecialization sp ON w.Specialization = sp.ID 
                                   WHERE w.AppointmentNumber = :aptnumber";
                            $query = $dbh->prepare($sql);
                            $query->bindParam(':aptnumber', $aptnumber, PDO::PARAM_STR);
                            $query->execute();
                            $result = $query->fetch(PDO::FETCH_ASSOC);
                            
                            if($result) {
                                ?>
                                <div class="alert alert-info">
                                    <h5>Waitlist Details</h5>
                                    <hr>
                                    <p><strong>Appointment Number:</strong> <?php echo htmlentities($result['AppointmentNumber']); ?></p>
                                    <p><strong>Status:</strong> 
                                        <?php 
                                        $status = $result['Status'];
                                        $statusClass = '';
                                        switch($status) {
                                            case 'Waiting':
                                                $statusClass = 'badge bg-warning';
                                                break;
                                            case 'Notified':
                                                $statusClass = 'badge bg-info';
                                                break;
                                            case 'Appointed':
                                                $statusClass = 'badge bg-success';
                                                break;
                                            case 'Expired':
                                                $statusClass = 'badge bg-danger';
                                                break;
                                        }
                                        echo "<span class='".$statusClass."'>".$status."</span>";
                                        ?>
                                    </p>
                                    <p><strong>Doctor:</strong> Dr. <?php echo htmlentities($result['DoctorName']); ?></p>
                                    <p><strong>Specialization:</strong> <?php echo htmlentities($result['Specialization']); ?></p>
                                    <p><strong>Requested Date:</strong> <?php echo htmlentities($result['RequestedDate']); ?></p>
                                    <p><strong>Preferred Time:</strong> <?php echo htmlentities($result['PreferredTimeSlot']); ?></p>
                                    <?php if($result['NotificationDate']) { ?>
                                        <p><strong>Last Notification:</strong> <?php echo htmlentities($result['NotificationDate']); ?></p>
                                    <?php } ?>
                                    
                                    <?php if($status == 'Notified') { ?>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            A slot has become available! Please contact the clinic to confirm your appointment.
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php
                            } else {
                                echo '<div class="alert alert-danger">No waitlist record found with this appointment number.</div>';
                            }
                        }
                        ?>
                        
                        <form method="get" class="mt-3">
                            <div class="form-group">
                                <label for="aptnumber">Enter Appointment Number:</label>
                                <input type="text" class="form-control" name="aptnumber" id="aptnumber" required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Check Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once('includes/footer.php');?>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>