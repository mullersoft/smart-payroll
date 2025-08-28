# Smart Payroll System

A full-stack payroll management system built with **Vue 3 (Vite)** for the frontend and **Laravel 9 (Sanctum + MySQL)** for the backend.

The system supports **multi-role authentication**, payroll preparation & approval, allowances, overtime, Chapa payment integration, and export to PDF/Excel.

---

## 🚀 Features

- **Authentication**

  - Signup, login, logout
  - Forget & reset password
  - Google login
  - Role-based authorization (Preparer, Approver, Admin)

- **User & Employee Management**

  - Manage system users
  - Manage employees (positions, employment types, allowances)

- **Payroll**

  - Prepare payrolls individually or in bulk
  - Overtime calculations based on Ethiopian labor law
  - Payroll approval workflow (Preparer → Approver)
  - Export payroll to **PDF & Excel**

- **Payments**
  - Process payrolls internally or via **Chapa Payment API**
  - Automatic updates to employee, employer, and revenue accounts
  - Transactions log

---

## 🛠 Tech Stack

- **Frontend:** Vue 3 + Vite + TailwindCSS
- **Backend:** Laravel 9 + Sanctum
- **Database:** MySQL
- **Auth:** Sanctum + Google OAuth
- **Payments:** Chapa
- **Exports:** Laravel Excel + DOMPDF

---

## 📦 Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR-USERNAME/smart-payroll.git
cd smart-payroll
```
