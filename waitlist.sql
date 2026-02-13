-- Create table for doctor availability
CREATE TABLE IF NOT EXISTS tbldoctoravailability (
    ID int(5) NOT NULL AUTO_INCREMENT,
    DoctorID int(5) NOT NULL,
    DayOfWeek int(1) NOT NULL,  -- 0=Sunday, 1=Monday, etc.
    StartTime time NOT NULL,
    EndTime time NOT NULL,
    MaxAppointments int(3) NOT NULL DEFAULT 10,
    PRIMARY KEY (ID),
    FOREIGN KEY (DoctorID) REFERENCES tbldoctor(ID),
    CONSTRAINT unique_doctor_schedule UNIQUE (DoctorID, DayOfWeek)
);

-- Create table for waitlist
CREATE TABLE IF NOT EXISTS tblwaitlist (
    ID int(10) NOT NULL AUTO_INCREMENT,
    AppointmentNumber int(10) NOT NULL,
    Name varchar(250) NOT NULL,
    MobileNumber bigint(20) NOT NULL,
    Email varchar(250) NOT NULL,
    RequestedDate date NOT NULL,
    PreferredTimeSlot time,
    Specialization varchar(250) NOT NULL,
    Doctor int(10) NOT NULL,
    Message mediumtext,
    Symptoms varchar(250),
    Status enum('Waiting', 'Notified', 'Appointed', 'Expired') DEFAULT 'Waiting',
    CreationDate timestamp DEFAULT current_timestamp(),
    NotificationDate timestamp NULL DEFAULT NULL,
    PRIMARY KEY (ID),
    FOREIGN KEY (Doctor) REFERENCES tbldoctor(ID)
);

-- Add slot management columns to tblappointment
ALTER TABLE tblappointment 
ADD COLUMN IF NOT EXISTS TimeSlotID int(5) AFTER AppointmentTime;

-- Add trigger to check availability before insert
DELIMITER //
CREATE TRIGGER before_appointment_insert 
BEFORE INSERT ON tblappointment
FOR EACH ROW
BEGIN
    DECLARE slot_count INT;
    DECLARE max_appointments INT;
    DECLARE day_of_week INT;
    
    SET day_of_week = WEEKDAY(NEW.AppointmentDate);
    
    -- Get current appointment count for the time slot
    SELECT COUNT(*) INTO slot_count
    FROM tblappointment
    WHERE Doctor = NEW.Doctor 
    AND AppointmentDate = NEW.AppointmentDate
    AND AppointmentTime = NEW.AppointmentTime
    AND Status = 'Approved';
    
    -- Get max appointments allowed
    SELECT MaxAppointments INTO max_appointments
    FROM tbldoctoravailability
    WHERE DoctorID = NEW.Doctor
    AND DayOfWeek = day_of_week
    AND NEW.AppointmentTime BETWEEN StartTime AND EndTime;
    
    -- If slot is full, move to waitlist
    IF slot_count >= max_appointments THEN
        INSERT INTO tblwaitlist (
            AppointmentNumber, Name, MobileNumber, Email,
            RequestedDate, PreferredTimeSlot, Specialization,
            Doctor, Message, Symptoms
        )
        VALUES (
            NEW.AppointmentNumber, NEW.Name, NEW.MobileNumber, NEW.Email,
            NEW.AppointmentDate, NEW.AppointmentTime, NEW.Specialization,
            NEW.Doctor, NEW.Message, NEW.Symptoms
        );
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'WAITLISTED';
    END IF;
END //
DELIMITER ;