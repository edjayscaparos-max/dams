<?php
// This file should be run periodically via cron job to check for available slots
include('doctor/includes/dbconnection.php');

function notifyPatient($email, $name, $doctorName, $date, $time) {
    $to = $email;
    $subject = "Appointment Slot Available";
    $message = "Dear $name,\n\n";
    $message .= "A slot has become available with Dr. $doctorName on $date at $time.\n";
    $message .= "Please contact the clinic or visit our website to confirm your appointment.\n\n";
    $message .= "This slot will be held for you for the next 24 hours.\n\n";
    $message .= "Best regards,\nYour Digital Health Partner";
    
    $headers = 'From: noreply@yourhealthpartner.com' . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

try {
    // Get all waitlisted patients
    $sql = "SELECT w.*, d.FullName as DoctorName 
            FROM tblwaitlist w 
            JOIN tbldoctor d ON w.Doctor = d.ID 
            WHERE w.Status = 'Waiting' 
            ORDER BY w.CreationDate ASC";
    $query = $dbh->prepare($sql);
    $query->execute();
    $waitlist = $query->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($waitlist as $patient) {
        // Check if slots are available for the requested date and time
        $dayOfWeek = date('w', strtotime($patient['RequestedDate']));
        
        $sql = "SELECT COUNT(*) as slot_count, da.MaxAppointments 
                FROM tblappointment a 
                JOIN tbldoctoravailability da ON a.Doctor = da.DoctorID 
                WHERE a.Doctor = :doctor 
                AND a.AppointmentDate = :date 
                AND a.AppointmentTime = :time 
                AND a.Status = 'Approved' 
                AND da.DayOfWeek = :dayOfWeek";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':doctor', $patient['Doctor'], PDO::PARAM_INT);
        $query->bindParam(':date', $patient['RequestedDate'], PDO::PARAM_STR);
        $query->bindParam(':time', $patient['PreferredTimeSlot'], PDO::PARAM_STR);
        $query->bindParam(':dayOfWeek', $dayOfWeek, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        // If slots are available, notify patient
        if ($result && $result['slot_count'] < $result['MaxAppointments']) {
            if(notifyPatient(
                $patient['Email'], 
                $patient['Name'], 
                $patient['DoctorName'],
                $patient['RequestedDate'],
                $patient['PreferredTimeSlot']
            )) {
                // Update waitlist status
                $sql = "UPDATE tblwaitlist 
                        SET Status = 'Notified', 
                            NotificationDate = CURRENT_TIMESTAMP 
                        WHERE ID = :id";
                $query = $dbh->prepare($sql);
                $query->bindParam(':id', $patient['ID'], PDO::PARAM_INT);
                $query->execute();
                
                echo "Notified patient: " . $patient['Name'] . "\n";
            }
        }
    }
    
    // Update expired notifications (older than 24 hours) back to waiting
    $sql = "UPDATE tblwaitlist 
            SET Status = 'Waiting' 
            WHERE Status = 'Notified' 
            AND TIMESTAMPDIFF(HOUR, NotificationDate, CURRENT_TIMESTAMP) > 24";
    $query = $dbh->prepare($sql);
    $query->execute();
    
    echo "Waitlist check completed successfully\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>