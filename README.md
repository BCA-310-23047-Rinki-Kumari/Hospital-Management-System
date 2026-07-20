Hospital Management System Project

 Project Title
Hospital Management System (HMS)
 Prepared By
Rinki Kumari
 Department
BCA-AKU
Academic Year
2023-2026

---

## 1. Introduction

The Hospital Management System is a web-based application designed to simplify hospital operations by managing patients, doctors, appointments, prescriptions, feedback, reports, and invoices. The system provides separate access for administrators, doctors, and patients so that each user can perform role-specific tasks efficiently.

This project was developed using:
- HTML for page structure
- CSS for styling
- Bootstrap for responsive UI design
- PHP for server-side logic
- MySQL for storing and managing data
- XAMPP as the local development environment

---

## 2. Objectives

The main objectives of this project are:
- To digitize hospital operations and reduce manual work
- To provide a user-friendly interface for hospital staff and patients
- To manage patient and doctor information efficiently
- To allow patients to book appointments online
- To help doctors view appointments and prescribe medicines
- To generate reports and manage invoices for hospital records

---

## 3. Problem Statement

Traditional hospital management systems often rely on manual records and paper-based processes. This can lead to:
- Loss of important patient information
- Delays in appointment scheduling
- Difficulties in maintaining doctor and patient records
- Poor communication between hospital staff and patients

The proposed system solves these issues by providing a centralized digital platform for hospital management.

---

## 4. Scope of the Project

The system covers the following modules:
- Admin module
- Doctor module
- Patient module
- Appointment booking
- Prescription management
- Feedback submission
- Reports generation
- Invoice management

---

## 5. Features of the System

### 5.1 Admin Features
- Admin login
- View dashboard statistics
- Manage doctors
- Manage patients
- View appointments
- View feedback
- Generate reports
- Create and view invoices
- Logout

### 5.2 Doctor Features
- Doctor login
- View doctor dashboard
- View assigned appointments
- View patient reports
- Prescribe medicines
- View doctor profile

### 5.3 Patient Features
- Patient registration and login
- View patient dashboard
- Book appointments
- View upcoming appointments
- View prescriptions
- View medical reports
- Submit feedback
- View invoices

---

## 6. Technology Stack

| Component | Technology |
|----------|------------|
| Frontend | HTML, CSS, Bootstrap |
| Backend | PHP |
| Database | MySQL |
| Server | XAMPP Apache + MySQL |
| Development Tool | Visual Studio Code |

---

## 7. System Requirements

### Software Requirements
- XAMPP or WAMP server
- Web browser (Chrome, Edge, Firefox)
- MySQL database support
- PHP 7 or above

### Hardware Requirements
- Minimum 2 GB RAM
- 100 MB free disk space
- Internet connection for Bootstrap CDN (optional if offline assets are used)

---

## 8. Project Structure

The main project files are stored in the folder:
- [xampp/htdocs/final year project](xampp/htdocs/final%20year%20project)

### Main Files
- index.html - Home page of the system
- admin_login.php - Admin login page
- admin_dashboard.php - Admin dashboard
- doctor_login.php - Doctor login page
- doctor_dashboard.php - Doctor dashboard
- patient_login.php - Patient login page
- patient_dashboard.php - Patient dashboard
- appointment.php - Appointment booking page
- appointment_success.php - Appointment confirmation page
- manage_doctors.php - Doctor management page
- manage_patients.php - Patient management page
- db.php - Database connection file
- hms.sql - Main database schema and sample data

### Invoice Module
- invoice/invoice_form.php
- invoice/invoice_list.php
- invoice/view_invoice.php
- invoice/create_invoice.php
- invoice/delete_invoice.php

---

## 9. Database Design

The project uses a MySQL database named hms.

### Main Tables

#### admin
Stores admin login credentials.

#### doctor
Stores doctor details such as:
- name
- email
- password
- specialization

#### patient
Stores patient details such as:
- name
- email
- password
- age

#### appointment
Stores appointment information including:
- patient name
- doctor name
- appointment date
- appointment time

#### prescription
Stores prescriptions issued to patients.

#### feedback
Stores patient feedback for appointments.

### Database Connection
The database connection is configured in [xampp/htdocs/final year project/db.php](xampp/htdocs/final%20year%20project/db.php).

---

## 10. Installation and Setup

### Step 1: Install XAMPP
Install XAMPP and start Apache and MySQL.

### Step 2: Place Project Folder
Copy the project folder into:
- [xampp/htdocs/final year project](xampp/htdocs/final%20year%20project)

### Step 3: Create the Database
Open phpMyAdmin and create a database named:
- hms

### Step 4: Import SQL File
Import the file:
- [xampp/htdocs/final year project/hms.sql](xampp/htdocs/final%20year%20project/hms.sql)

### Step 5: Run the Application
Open the browser and visit:
- http://localhost/final%20year%20project/

### Step 6: Login Credentials
Default login details included in the database:
- Admin username: admin
- Admin password: admin

You may change the password later for better security.

---

## 11. User Guide

### 11.1 Admin User
1. Open the admin login page.
2. Enter the admin username and password.
3. Access the dashboard.
4. Manage doctors, patients, appointments, feedback, and reports.

### 11.2 Doctor User
1. Login through the doctor login page.
2. View doctor dashboard.
3. Check appointments.
4. Prescribe medicine for patients.

### 11.3 Patient User
1. Register or log in as a patient.
2. Access the patient dashboard.
3. Book appointments.
4. View prescriptions and invoices.
5. Submit feedback.

---

## 12. Workflow of the System

1. Patient opens the website and registers/logs in.
2. Patient books an appointment with a selected doctor.
3. Appointment information is stored in the database.
4. Admin or doctor can view the appointment details.
5. Doctor can prescribe medicine to the patient.
6. Patient can view prescriptions and invoices.
7. Patient can submit feedback about the treatment experience.

---

## 13. Security Considerations

The current system includes basic authentication and session checks. However, for real-world deployment, the following improvements are recommended:
- Use prepared statements instead of direct SQL queries
- Hash passwords securely and store them safely
- Implement role-based access control more strictly
- Validate and sanitize all user inputs
- Use HTTPS in deployment

---

## 14. Advantages of the System

- Easy to use for hospital staff and patients
- Reduces paperwork and manual record keeping
- Improves appointment management
- Provides centralized data storage
- Enhances communication between patients and doctors

---

## 15. Limitations of the Project

- The project is a basic academic implementation
- Some features are limited compared to commercial hospital systems
- Real-time notifications and payment integration are not fully implemented
- Advanced analytics and multi-hospital support are not included

---

## 16. Future Enhancements

The following features can be added in future versions:
- Online payment integration
- SMS or email notifications
- Doctor availability scheduling
- Advanced report generation with charts
- Mobile-friendly version
- Cloud hosting and remote access
- More robust security features

---

## 17. Conclusion

The Hospital Management System is a practical and effective web-based solution for managing hospital operations. It simplifies appointment scheduling, record management, prescriptions, feedback, and invoicing. The project demonstrates the integration of frontend, backend, and database technologies to build a functional healthcare management application.

This system can be further expanded into a full-scale hospital management solution with more advanced features and stronger security.

---

## 18. Reference Files

- [xampp/htdocs/final year project/index.html](xampp/htdocs/final%20year%20project/index.html)
- [xampp/htdocs/final year project/db.php](xampp/htdocs/final%20year%20project/db.php)
- [xampp/htdocs/final year project/hms.sql](xampp/htdocs/final%20year%20project/hms.sql)
- [xampp/htdocs/final year project/admin_dashboard.php](xampp/htdocs/final%20year%20project/admin_dashboard.php)
- [xampp/htdocs/final year project/doctor_dashboard.php](xampp/htdocs/final%20year%20project/doctor_dashboard.php)
- [xampp/htdocs/final year project/patient_dashboard.php](xampp/htdocs/final%20year%20project/patient_dashboard.php)
- [xampp/htdocs/final year project/appointment.php](xampp/htdocs/final%20year%20project/appointment.php)
