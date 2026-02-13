<?php
session_start();
//error_reporting(0);
include('doctor/includes/dbconnection.php');
    if(isset($_POST['submit']))
  {
 $name=$_POST['name'];
  $mobnum=$_POST['phone'];
 $email=$_POST['email'];
 $appdate=$_POST['date'];
 $aaptime=$_POST['time'];
 $symptoms=$_POST['symptoms'];
 $specialization=$_POST['specialization'];
  $doctorlist=$_POST['doctorlist'];
 $message=$_POST['message'];
 $aptnumber=mt_rand(100000000, 999999999);
 $cdate=date('Y-m-d');

if($appdate<=$cdate){
       echo '<script>alert("Appointment date must be greater than todays date")</script>';
} else {
    try {
        $sql="insert into tblappointment(AppointmentNumber,Name,MobileNumber,Email,AppointmentDate,AppointmentTime,Specialization,Doctor,Message,Symptoms)values(:aptnumber,:name,:mobnum,:email,:appdate,:aaptime,:specialization,:doctorlist,:message,:symptoms)";
        $query=$dbh->prepare($sql);
        $query->bindParam(':aptnumber',$aptnumber,PDO::PARAM_STR);
        $query->bindParam(':name',$name,PDO::PARAM_STR);
        $query->bindParam(':mobnum',$mobnum,PDO::PARAM_STR);
        $query->bindParam(':email',$email,PDO::PARAM_STR);
        $query->bindParam(':appdate',$appdate,PDO::PARAM_STR);
        $query->bindParam(':aaptime',$aaptime,PDO::PARAM_STR);
        $query->bindParam(':specialization',$specialization,PDO::PARAM_STR);
        $query->bindParam(':doctorlist',$doctorlist,PDO::PARAM_STR);
        $query->bindParam(':message',$message,PDO::PARAM_STR);
        $query->bindParam(':symptoms',$symptoms,PDO::PARAM_STR);
        $query->execute();
        
        $LastInsertId=$dbh->lastInsertId();
        if ($LastInsertId>0) {
            echo '<script>alert("Your Appointment Request Has Been Sent. We Will Contact You Soon")</script>';
        } else {
            echo '<script>alert("Something Went Wrong. Please try again")</script>';
        }
    } catch (PDOException $e) {
        // Check if the error is from our waitlist trigger
        if ($e->errorInfo[1] == 1644 && $e->getMessage() == 'WAITLISTED') {
            echo '<script>
                if(confirm("All slots are currently full. You have been added to the waitlist. Would you like to check your waitlist status?")) {
                    window.location.href = "check-waitlist.php?aptnumber=' . $aptnumber . '";
                }
            </script>';
        } else {
            echo '<script>alert("An error occurred. Please try again.")</script>';
        }
    }
}
}
?>
<!doctype html>
<html lang="en">
    <head>
        <title>OADCC</title>

        <!-- CSS FILES -->        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

        <link href="css/bootstrap.min.css" rel="stylesheet">

        <link href="css/bootstrap-icons.css" rel="stylesheet">

        <link href="css/owl.carousel.min.css" rel="stylesheet">

        <link href="css/owl.theme.default.min.css" rel="stylesheet">

        <link href="css/templatemo-medic-care.css" rel="stylesheet">
        <script>
function getdoctors(val) {
$.ajax({
    type: "POST",
    url: "get_doctors.php",
    data:'sp_id='+val,
    success: function(data){
        $("#doctorlist").html(data);
    }
});
}

function getRecommendation() {
    var symptom = document.getElementById('symptoms').value;
    var recommendationBox = document.getElementById('specialization-recommendation');
    var recommendedSpecialist = document.getElementById('recommended-specialist');
    
    if(symptom) {
        $.ajax({
            type: "POST",
            url: "get_recommendation.php",
            data: { symptom: symptom },
            success: function(response) {
                var data = JSON.parse(response);
                if(data.id) {
                    document.getElementById('specialization').value = data.id;
                    getdoctors(data.id);
                    recommendedSpecialist.textContent = data.specialization;
                    recommendationBox.style.display = 'block';
                }
            }
        });
    } else {
        recommendationBox.style.display = 'none';
    }
}
</script>
    </head>
    
    <body id="top">
    
        <main>

            <?php include_once('includes/header.php');?>

            <section class="hero" id="hero">
                <div class="container">
                    <div class="row">

                        <div class="col-12">
                            <div id="myCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="images/slider/portrait-successful-mid-adult-doctor-with-crossed-arms.jpg" class="img-fluid" alt="">
                                    </div>

                                    <div class="carousel-item">
                                        <img src="images/slider/young-asian-female-dentist-white-coat-posing-clinic-equipment.jpg" class="img-fluid" alt="">
                                    </div>

                                    <div class="carousel-item">
                                        <img src="images/slider/doctor-s-hand-holding-stethoscope-closeup.jpg" class="img-fluid" alt="">
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </section>

            <section class="section-padding" id="about">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-6 col-md-6 col-12">
                            <?php
$sql="SELECT * from tblpage where PageType='aboutus'";
$query = $dbh -> prepare($sql);
query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>
                            <h2 class="mb-lg-3 mb-3"><?php  echo htmlentities($row->PageTitle);?></h2>

                            <p><?php  echo ($row->PageDescription);?>.</p>

                           <?php $cnt=$cnt+1;}} ?>
                        </div>

                        <div class="col-lg-4 col-md-5 col-12 mx-auto">
                            <div class="featured-circle bg-white shadow-lg d-flex justify-content-center align-items-center">
                                <p class="featured-text"><span class="featured-number">12</span> Years<br> of Experiences walay labot OJT</p>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <section class="gallery">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-6 col-6 ps-0">
                            <img src="images/gallery/medium-shot-man-getting-vaccine.jpg" class="img-fluid galleryImage" alt="get a vaccine" title="get a vaccine for yourself">
                        </div>

                        <div class="col-lg-6 col-6 pe-0">
                            <img src="images/gallery/female-doctor-with-presenting-hand-gesture.jpg" class="img-fluid galleryImage" alt="wear a mask" title="wear a mask to protect yourself">
                        </div>

                    </div>
                </div>
            </section>

            

            

            <section class="section-padding" id="booking">
                <div class="container">
                    <div class="row">
                    
                        <div class="col-lg-8 col-12 mx-auto">
                            <div class="booking-form">
                                
                                <h2 class="text-center mb-lg-3 mb-2">Book an appointment</h2>
                            
                                <form role="form" method="post">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <h5>Step 1: Tell us about your symptoms</h5>
                                            <div class="symptom-selector">
                                                <select class="form-control form-control-lg" id="symptoms" name="symptoms" onchange="getRecommendation()" required>
                                                    <option value="">Select your primary symptom</option>
                                                    <?php
                                                    $sql = "SELECT s.*, sp.Specialization 
                                                           FROM tblsymptoms s 
                                                           JOIN tblspecialization sp ON s.RecommendedSpecialization = sp.ID 
                                                           ORDER BY sp.Specialization, s.Symptom";
                                                    $stmt = $dbh->query($sql);
                                                    $currentSpec = '';
                                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        if($currentSpec != $row['Specialization']) {
                                                            if($currentSpec != '') echo '</optgroup>';
                                                            echo '<optgroup label="'.$row['Specialization'].'">';
                                                            $currentSpec = $row['Specialization'];
                                                        }
                                                        echo '<option value="'.htmlspecialchars($row['Symptom']).'">'.htmlspecialchars($row['Symptom']).'</option>';
                                                    }
                                                    if($currentSpec != '') echo '</optgroup>';
                                                    ?>
                                                </select>
                                                <div id="specialization-recommendation" class="alert alert-info mt-3" style="display:none;">
                                                    <i class="bi bi-info-circle-fill"></i> 
                                                    Based on your symptoms, we recommend consulting with a <strong><span id="recommended-specialist"></span></strong>.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mb-4">
                                            <h5>Step 2: Select your doctor</h5>
                                            <div class="row">
                                                <div class="col-lg-6 col-12 mb-3">
                                                    <select onChange="getdoctors(this.value);" name="specialization" id="specialization" class="form-control" required>
                                                        <option value="">Select specialization</option>
                                                        <?php
                                                        $sql="SELECT * FROM tblspecialization";
                                                        $stmt=$dbh->query($sql);
                                                        $stmt->setFetchMode(PDO::FETCH_ASSOC);
                                                        while($row =$stmt->fetch()) { 
                                                        ?>
                                                        <option value="<?php echo $row['ID'];?>"><?php echo $row['Specialization'];?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <select name="doctorlist" id="doctorlist" class="form-control">
                                                        <option value="">Select Doctor</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mb-4">
                                            <h5>Step 3: Your Information</h5>
                                            <div class="row">
                                                <div class="col-lg-6 col-12 mb-3">
                                                    <input type="text" name="name" id="name" class="form-control" placeholder="Full name" required='true'>
                                                </div>
                                                <div class="col-lg-6 col-12 mb-3">
                                                    <input type="email" name="email" id="email" pattern="[^ @]*@[^ @]*" class="form-control" placeholder="Email address" required='true'>
                                                </div>
                                                <div class="col-lg-6 col-12 mb-3">
                                                    <input type="telephone" name="phone" id="phone" class="form-control" placeholder="Enter Phone Number" maxlength="10">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mb-4">
                                            <h5>Step 4: Choose Appointment Time</h5>
                                            <div class="row">
                                                <div class="col-lg-6 col-12 mb-3">
                                                    <input type="date" name="date" id="date" value="" class="form-control">
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <input type="time" name="time" id="time" value="" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mb-4">
                                            <h5>Additional Information</h5>
                                            <textarea class="form-control" rows="3" id="message" name="message" placeholder="Additional Message"></textarea>
                                        </div>

                                        <div class="col-lg-3 col-md-4 col-6 mx-auto">
                                            <button type="submit" class="form-control" name="submit" id="submit-button">Book Now</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </main>
        <?php include_once('includes/footer.php');?>
        <!-- JAVASCRIPT FILES -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/owl.carousel.min.js"></script>
        <script src="js/scrollspy.min.js"></script>
        <script src="js/custom.js"></script>
    </body>
</html>