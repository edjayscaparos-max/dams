<?php
include('doctor/includes/dbconnection.php');

if(isset($_POST['symptom'])) {
    $symptom = $_POST['symptom'];
    
    $sql = "SELECT s.RecommendedSpecialization, sp.Specialization 
            FROM tblsymptoms s 
            JOIN tblspecialization sp ON s.RecommendedSpecialization = sp.ID 
            WHERE s.Symptom = :symptom";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':symptom', $symptom, PDO::PARAM_STR);
    $query->execute();
    
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if($result) {
        echo json_encode([
            'id' => $result['RecommendedSpecialization'],
            'specialization' => $result['Specialization']
        ]);
    } else {
        echo json_encode(['error' => 'No recommendation found']);
    }
}
?>