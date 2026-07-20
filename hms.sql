-- hms.sql

-- Admin Table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    password VARCHAR(100)
);

INSERT INTO admin(username,password)
VALUES ('admin','$2y$10$7V.ZBACQzVCKK.U8kaos8unZyZbLz56hPwjyJX3c2c4dAw8L5nRzq');

-- Doctor Table
CREATE TABLE doctor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100),
    specialization VARCHAR(100)
);

INSERT INTO doctor(name,email,password,specialization)
VALUES
('Rahul Kumar','doctor@gmail.com','$2y$10$DQbXr29sKru/j79xqoGex.7kDIdJu9r/mtZ38INTOZYeUODTCGfei','Cardiologist');

-- Patient Table
CREATE TABLE patient (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100),
    age INT
);

INSERT INTO patient(name,email,password,age)
VALUES
('Rinki','patient@gmail.com','$2y$10$OghVuoyL2NVCPREJ1mG.besaSOxRhS4r1yQvNJMtVGNgrPtz4KBEi',22);

-- Appointment Table
CREATE TABLE appointment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(100),
    doctor_name VARCHAR(100),
    appointment_date DATE,
    appointment_time TIME
);

-- Prescription Table
CREATE TABLE prescription (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    patient_name VARCHAR(100),
    doctor_id INT,
    doctor_name VARCHAR(100),
    medicine_name VARCHAR(200),
    dosage VARCHAR(100),
    frequency VARCHAR(100),
    duration VARCHAR(100),
    instructions TEXT,
    issued_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patient(id),
    FOREIGN KEY (doctor_id) REFERENCES doctor(id)
);

-- Feedback Table
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT,
    patient_id INT,
    patient_name VARCHAR(100),
    doctor_name VARCHAR(100),
    rating INT,
    comments TEXT,
    submitted_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointment(id),
    FOREIGN KEY (patient_id) REFERENCES patient(id)
);
