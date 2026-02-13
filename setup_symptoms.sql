-- Add symptoms column to appointment table
ALTER TABLE tblappointment ADD COLUMN IF NOT EXISTS Symptoms varchar(250) DEFAULT NULL;

-- Create symptoms table if it doesn't exist
CREATE TABLE IF NOT EXISTS tblsymptoms (
    ID int(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Symptom varchar(250) NOT NULL,
    RecommendedSpecialization int(5) NOT NULL,
    FOREIGN KEY (RecommendedSpecialization) REFERENCES tblspecialization(ID)
);

-- Insert common symptoms with their recommended specializations
INSERT INTO tblsymptoms (Symptom, RecommendedSpecialization) VALUES 
('Joint pain or stiffness', 1),
('Back pain', 1),
('Persistent fever or fatigue', 2),
('Digestive issues', 2),
('Pregnancy related concerns', 3),
('Menstrual issues', 3),
('Skin rashes or allergies', 4),
('Acne problems', 4),
('Child health issues', 5),
('Childhood vaccinations', 5),
('Breathing difficulties', 10),
('Persistent cough', 10),
('Eye problems', 8),
('Vision changes', 8),
('Ear pain or hearing issues', 13),
('Sinus problems', 13),
('General health checkup', 9),
('Chronic disease management', 9);