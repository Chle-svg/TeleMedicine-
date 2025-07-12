# MedConnect - User Manual (WAMP Version)

## 1. Introduction
MedConnect is an online medical consultation system where users can book appointments, make payments, and receive prescriptions online. Doctors manage appointments, and admins oversee system operations.

## 2. System Requirements
- PHP >= 7.4  
- MySQL  
- WAMP Server  
- Web Browser (Chrome, Firefox, etc.)

## 3. Installation Guide
1. Install WAMP Server from [https://www.wampserver.com](https://www.wampserver.com)  
2. Copy the MedConnect project folder into `C:/wamp64/www`  
3. Open phpMyAdmin via `http://localhost/phpmyadmin`  
4. Create a database named `med_db`  
5. Import the `med_db.sql` file  
6. Update `includes/db.php` with the correct database credentials  
7. Run the system at `http://localhost/onlinemedication2`

## 4. User Roles and Features
- **Admin Panel:** Approve doctors, post medications, view users/doctors/appointments  
- **Doctor Panel:** Manage appointments, accept/reject requests, write prescriptions  
- **User Panel:** Book appointments, view status, pay via Chapa, receive prescriptions

## 5. Using the System
- Register/Login as a user  
- Apply as a doctor (upload CV)  
- Book appointments with doctors  
- View appointment status  
- Pay consultation fee using Chapa  
- View/download prescriptions

## 6. Admin Instructions
- Login via localhost  
- Approve/reject doctor applications  
- Post medications  
- View users and appointments

## 7. Database Info
- Database name: `med_db`  
- Key tables:  
  - `users`  
  - `doctors`  
  - `appointments`  
  - `prescriptions`  
  - `medications`  
  - `notifications`

## 8. Common Issues

| Issue              | Solution                                  |
|--------------------|-------------------------------------------|
| Can't login        | Check credentials and session             |
| Database error     | Ensure SQL file is imported properly      |
| Payment not working | Use valid Chapa test key and ensure internet connectivity |

## 9. Default Admin Login
- URL: `http://localhost/medconnect/admin_login.php`  
- Email: `mom@gmail.com`  
- Password: `Mom12345`  

*Note: Change the password after first login for security.*

## 10. Developer Contact
- Developer: Chlotaw Gedamu  
- Email: chlotawgedamu@gmail.com  
- Phone: 0933804482
