# Attendance Management System

## Table of Contents

1. [Introduction](#introduction)
2. [Features](#features)
3. [Technologies Used](#technologies-used)
4. [Installation](#installation)
5. [Usage](#usage)
6. [Configuration](#configuration)
7. [Database Setup](#database-setup)

---

## Introduction

The **Attendance Management System** is a web-based application designed to automate and streamline the process of managing student attendance. This system allows administrators to manage student data, record attendance, and generate reports efficiently.

---

## Features

- Student and class management
- CSV bulk import of student data
- Attendance marking and tracking
- Automated email notifications using PHPMailer
- User-friendly interface for administrators and teachers
- Reporting and analysis of attendance records

---

## Technologies Used

- **Backend:** PHP, MySQL
- **Frontend:** HTML, CSS, JavaScript, Bootstrap
- **Libraries:** PHPMailer for sending email notifications

---

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/attendance-management-system.git
   cd attendance-management-system
   ```

2. **Install dependencies:**
   - Use `composer` to install PHPMailer:
     ```bash
     composer require phpmailer/phpmailer
     ```

3. **Configure database and application settings:**  
   Update the database credentials in `config.php` as described in the Configuration section.

---

## Usage

1. **Login to the system:**
   - Access the application in your browser using `http://localhost/attendance-management`.

2. **Admin functionalities:**
   - Add students, classes, and class arms.
   - Import students via a CSV file.
   - Manage daily attendance records.
   
3. **Generate Reports:**
   - View attendance reports and analyze the data.

---

## Configuration

- **Database Configuration:**
  Update the following variables in `config.php` to match your MySQL database:

  ```php
  $host = 'localhost';
  $username = 'your-username';  // Replace with your MySQL username
  $password = 'your-password';  // Replace with your MySQL password
  $dbname = 'attendance_management';
  ```

- **PHPMailer Configuration:**
  Update the email sending credentials for PHPMailer:

  ```php
  $mail->Host = 'smtp.gmail.com';
  $mail->Username = 'your-email@gmail.com';  // Replace with your email
  $mail->Password = 'your-email-password';  // Replace with your email app password
  ```

---

## Database Setup

1. **Create the database:**
   ```sql
   CREATE DATABASE attendance_management;
   ```

2. **Import the database schema:**
   Run the following command to import the `database.sql` file:

   ```bash
   mysql -u your-username -p attendance_management < database.sql
   ```

---


