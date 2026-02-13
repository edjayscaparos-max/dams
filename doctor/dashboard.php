<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['damsid']) == 0) {
    header('location:logout.php');
    exit();
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - DAMS</title>
    
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-light: #e3f2fd;
            --secondary-light: #f8f9fa;
        }
        
        body {
            background-color: var(--secondary-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
            transition: transform 0.2s;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
        }

        .card-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.5rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 600;
            margin: 0;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }

        .action-link {
            color: inherit;
            text-decoration: none;
        }

        .action-link:hover {
            color: var(--bs-primary);
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--bs-primary) 0%, #0056b3 100%);
            border-radius: 12px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .stats-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include_once('includes/header.php');?>
    <?php include_once('includes/sidebar.php');?>

    <main class="app-main">
        <div class="container-fluid py-4">
            <div class="welcome-section">
                <?php
                $docid = $_SESSION['damsid'];
                $sql = "SELECT FullName from tbldoctor where ID=:docid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':docid', $docid, PDO::PARAM_STR);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_OBJ);
                ?>
                <h1 class="h3 mb-0">Welcome back, Dr. <?php echo htmlentities($result->FullName); ?></h1>
                <p class="mb-0">Here's your practice overview for today</p>
            </div>

            <div class="row g-4">
                <!-- New Appointments -->
                <div class="col-md-6 col-lg-3">
                    <div class="dashboard-card p-4">
                        <?php 
                        $sql = "SELECT ID from tblappointment where Status is null && Doctor=:docid";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':docid', $docid, PDO::PARAM_STR);
                        $query->execute();
                        $newAppts = $query->rowCount();
                        ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="card-icon bg-warning-subtle text-warning me-3">
                                <i class="bi bi-journal-plus"></i>
                            </div>
                            <div>
                                <p class="stats-number"><?php echo $newAppts; ?></p>
                                <p class="stats-label">New Appointments</p>
                            </div>
                        </div>
                        <a href="new-appointment.php" class="action-link">View appointments <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Approved Appointments -->
                <div class="col-md-6 col-lg-3">
                    <div class="dashboard-card p-4">
                        <?php 
                        $sql = "SELECT ID from tblappointment where Status='Approved' && Doctor=:docid";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':docid', $docid, PDO::PARAM_STR);
                        $query->execute();
                        $approvedAppts = $query->rowCount();
                        ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="card-icon bg-success-subtle text-success me-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <p class="stats-number"><?php echo $approvedAppts; ?></p>
                                <p class="stats-label">Approved Today</p>
                            </div>
                        </div>
                        <a href="approved-appointment.php" class="action-link">See approved <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Cancelled Appointments -->
                <div class="col-md-6 col-lg-3">
                    <div class="dashboard-card p-4">
                        <?php 
                        $sql = "SELECT ID from tblappointment where Status='Cancelled' && Doctor=:docid";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':docid', $docid, PDO::PARAM_STR);
                        $query->execute();
                        $cancelledAppts = $query->rowCount();
                        ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="card-icon bg-danger-subtle text-danger me-3">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div>
                                <p class="stats-number"><?php echo $cancelledAppts; ?></p>
                                <p class="stats-label">Cancelled</p>
                            </div>
                        </div>
                        <a href="cancelled-appointment.php" class="action-link">View cancelled <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Total Appointments -->
                <div class="col-md-6 col-lg-3">
                    <div class="dashboard-card p-4">
                        <?php 
                        $sql = "SELECT ID from tblappointment where Doctor=:docid";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':docid', $docid, PDO::PARAM_STR);
                        $query->execute();
                        $totalAppts = $query->rowCount();
                        ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="card-icon bg-primary-subtle text-primary me-3">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <p class="stats-number"><?php echo $totalAppts; ?></p>
                                <p class="stats-label">Total Appointments</p>
                            </div>
                        </div>
                        <a href="all-appointment.php" class="action-link">View all <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php } ?>