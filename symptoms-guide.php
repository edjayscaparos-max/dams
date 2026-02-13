<?php
include('includes/dbconnection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Common Symptoms Guide - DAMS</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/templatemo-medic-care.css" rel="stylesheet">
</head>
<body>
    <?php include_once('includes/header.php');?>

    <section class="section-padding" id="booking">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <h2 class="mb-4">Common Symptoms Guide</h2>
                    <div class="row">
                        <?php
                        $sql = "SELECT DISTINCT s.Specialization, GROUP_CONCAT(t.Symptom) as Symptoms 
                                FROM tblspecialization s 
                                JOIN tblsymptoms t ON s.ID = t.RecommendedSpecialization 
                                GROUP BY s.ID, s.Specialization";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $specializations = $query->fetchAll(PDO::FETCH_ASSOC);

                        foreach($specializations as $spec) {
                            $symptoms = explode(',', $spec['Symptoms']);
                            ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary"><?php echo htmlspecialchars($spec['Specialization']); ?></h5>
                                        <ul class="list-unstyled">
                                            <?php foreach($symptoms as $symptom) { ?>
                                                <li class="mb-2">
                                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                    <?php echo htmlspecialchars(trim($symptom)); ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <a href="index.php#booking" class="btn btn-primary btn-sm mt-3">Book Appointment</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="alert alert-info mt-4">
                        <p><strong>Note:</strong> This guide is for reference only. For accurate medical advice, please consult with a healthcare professional.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once('includes/footer.php');?>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>