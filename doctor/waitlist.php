<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['damsid']==0)) {
    header('location:logout.php');
} else {
    // Handle notifications
    if(isset($_POST['notify'])) {
        $waitlistId = $_POST['waitlist_id'];
        $sql = "UPDATE tblwaitlist SET Status='Notified', NotificationDate=CURRENT_TIMESTAMP WHERE ID=:wid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':wid', $waitlistId, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            echo '<script>alert("Patient has been notified.")</script>';
        }
    }

    // Handle appointment creation from waitlist
    if(isset($_POST['create_appointment'])) {
        $waitlistId = $_POST['waitlist_id'];
        
        // Get waitlist entry
        $sql = "SELECT * FROM tblwaitlist WHERE ID=:wid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':wid', $waitlistId, PDO::PARAM_STR);
        $query->execute();
        $waitlist = $query->fetch(PDO::FETCH_ASSOC);

        // Create new appointment
        $sql = "INSERT INTO tblappointment(AppointmentNumber, Name, MobileNumber, Email, 
                AppointmentDate, AppointmentTime, Specialization, Doctor, Message, 
                Symptoms, Status) 
                VALUES(:aptnumber, :name, :mobnum, :email, :appdate, :aaptime, 
                :specialization, :doctor, :message, :symptoms, 'Approved')";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':aptnumber', $waitlist['AppointmentNumber'], PDO::PARAM_STR);
        $query->bindParam(':name', $waitlist['Name'], PDO::PARAM_STR);
        $query->bindParam(':mobnum', $waitlist['MobileNumber'], PDO::PARAM_STR);
        $query->bindParam(':email', $waitlist['Email'], PDO::PARAM_STR);
        $query->bindParam(':appdate', $waitlist['RequestedDate'], PDO::PARAM_STR);
        $query->bindParam(':aaptime', $waitlist['PreferredTimeSlot'], PDO::PARAM_STR);
        $query->bindParam(':specialization', $waitlist['Specialization'], PDO::PARAM_STR);
        $query->bindParam(':doctor', $waitlist['Doctor'], PDO::PARAM_STR);
        $query->bindParam(':message', $waitlist['Message'], PDO::PARAM_STR);
        $query->bindParam(':symptoms', $waitlist['Symptoms'], PDO::PARAM_STR);
        
        $query->execute();
        if ($query->rowCount() > 0) {
            // Update waitlist status
            $sql = "UPDATE tblwaitlist SET Status='Appointed' WHERE ID=:wid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':wid', $waitlistId, PDO::PARAM_STR);
            $query->execute();
            echo '<script>alert("Appointment created successfully from waitlist.")</script>';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>DAMS - Waitlist Management</title>
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
                        <h5 class="over-title margin-bottom-15">Manage <span class="text-bold">Waitlist</span></h5>
                        <table class="table table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Requested Date</th>
                                    <th>Preferred Time</th>
                                    <th>Wait Duration</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $did = $_SESSION['damsid'];
                                $sql = "SELECT *, 
                                       TIMESTAMPDIFF(DAY, CreationDate, CURRENT_TIMESTAMP) as wait_days 
                                       FROM tblwaitlist 
                                       WHERE Doctor=:did AND Status IN ('Waiting', 'Notified') 
                                       ORDER BY CreationDate ASC";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':did', $did, PDO::PARAM_STR);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                $cnt = 1;
                                foreach($results as $row) {
                                ?>
                                <tr>
                                    <td><?php echo htmlentities($cnt);?></td>
                                    <td><?php echo htmlentities($row->Name);?></td>
                                    <td><?php echo htmlentities($row->MobileNumber);?></td>
                                    <td><?php echo htmlentities($row->Email);?></td>
                                    <td><?php echo htmlentities($row->RequestedDate);?></td>
                                    <td><?php echo htmlentities($row->PreferredTimeSlot);?></td>
                                    <td><?php echo htmlentities($row->wait_days);?> days</td>
                                    <td>
                                        <?php 
                                        if($row->Status == 'Waiting') {
                                            echo '<span class="badge badge-warning">Waiting</span>';
                                        } else {
                                            echo '<span class="badge badge-info">Notified</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="waitlist_id" value="<?php echo $row->ID; ?>">
                                            <?php if($row->Status == 'Waiting'): ?>
                                            <button type="submit" name="notify" class="btn btn-info btn-sm">
                                                Notify
                                            </button>
                                            <?php endif; ?>
                                            <button type="submit" name="create_appointment" class="btn btn-success btn-sm">
                                                Create Appointment
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php 
                                $cnt++;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>