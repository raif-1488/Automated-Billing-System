# Automated Billing System 💳📧

A real-time web-based billing and invoicing system built for **Get Catalyzed (Digital Marketing Agency)**.  
This project automates invoice generation, payment tracking, and reminder notifications with **Razorpay payment gateway integration**.

---

## 🚀 Features
- Responsive web-based invoicing system.
- Supports **full and partial payments** via Razorpay API.
- **Secure transaction handling** with real-time updates using AJAX.
- **SMTP email integration** for:
  - Payment success notifications.
  - Automated payment reminders.
- Role-based **User and Admin dashboards**.
- Dynamic **invoice generation and management**.
- Receipt generation after successful payment.
- Built with **modular and reusable components**.

---

## 🛠 Tech Stack
- **Backend:** PHP, MySQL  
- **Frontend:** HTML, CSS, jQuery, AJAX  
- **Payment Gateway:** Razorpay API  
- **Mailing Service:** PHPMailer (SMTP)  
- **Server Environment:** XAMPP (Apache + MySQL)  

---

## 📌 Core Features
- Order generation & management  
- Razorpay payment gateway integration (installments & full payments)  
- Automated payment reminders  
- Success email notifications to customers  
- Receipt generation after successful payment  

---

## 💳 Razorpay Payment Gateway Integration

1. **Sign up and Login**  
   - Create an account at [Razorpay](https://razorpay.com/).  
   - Go to **Account & Settings → API Keys → Generate Test Key**.  
   - Copy and save your **Key ID** and **Key Secret**.  

2. **Download Razorpay PHP SDK**  
   - Get it from [Razorpay PHP Docs](https://razorpay.com/docs/payments/server-integration/php/).  
   - Download and extract `razorpay-php.zip`.  
   - Place the extracted folder inside your project as `src/razorpay/`.  

3. **Setup Payment Button (`payment.php`)**  
   - Create `payment.php` with a **payment button**.  
   - Use AJAX to send requests to `pay-online.php`.  

4. **Setup Payment Processing (`pay-online.php`)**  
   - Copy **Basic Usage** + **Integration Steps (Sample Code)** from Razorpay PHP docs.  
   - Adjust variables and DB insertions according to your project.  

5. **Add Razorpay Checkout Script**  
   ```html
   <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
   ```
   - Add inside `<head>` of `payment.php`.  
   - Implement the `startPayment()` function from docs.  

6. **Handle Payment Success & Failure**  
   - **On Success:** generate receipt + send email confirmation.  
   - **On Failure:** display error and retry option.  

---

## ⚙️ Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone https://github.com/raif-1488/Automated-Billing-System.git
   cd automate-billing
   ```

2. **Import Database**
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`).  
   - Create a database `automate`.  
   - Import `automate_dump.sql`.  

3. **Configure Environment**
   - Update DB credentials in `includes/db.php`.  
   - Add Razorpay API keys in integration files.  
   - Configure SMTP in `includes/sendMail.php`.  

4. **Start Server**
   - Start **Apache** and **MySQL** in XAMPP.  
   - Open `http://localhost/automate-billing/` in your browser.  

---

## 📧 Email Notifications
- Uses **PHPMailer (SMTP)** to send:  
  - Payment confirmation emails.  
  - Automated reminders for pending payments.  

---

## 📸 Screenshots  

### 1. Admin Dashboard  
![Admin Dashboard]("images/Screenshot 2025-09-14 173541.png")  

### 2. Consumer Login Page  
![Consumer Login]("images/Screenshot 2025-09-14 175934.png")  

### 3. Consumer Dashboard  
![Consumer Dashboard]("images/Screenshot 2025-09-14 181347.png")  

### 4. Consumer Payment  
![Consumer Payment]("images/Screenshot 2025-09-14 181728.png")  
![Consumer Payment]("images/Screenshot 2025-09-14 182221.png")  

---

## 👤 Author
**Raif** (GitHub: [raif-1488](https://github.com/raif-1488))  
**Email:** chhavinawria@gmail.com  
