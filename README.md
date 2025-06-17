# Laravel EMI Processing System

This project implements an EMI (Equated Monthly Installment) processing system using the Laravel framework. It demonstrates usage of repository-service design patterns, raw SQL operations, dynamic table generation, and admin interface authentication.

## Features

- Admin login with seeded user credentials
- Loan details management with seed data
- EMI schedule table creation using raw SQL
- Dynamic EMI allocation based on loan duration
- Accurate EMI rounding and last-installment adjustment
- Repository-Service pattern for clean architecture

---

## 📁 Project Structure

- `app/Repositories/LoanRepository.php` - Handles DB interactions for `loan_details`
- `app/Services/EmiService.php` - Business logic for EMI processing
- `app/Http/Controllers/EmiController.php` - Controller handling EMI routes
- `resources/views/emi/index.blade.php` - Displays loan and EMI data
- `routes/web.php` - Route definitions
- `database/migrations/` - Schema for `users`, `loan_details`
- `database/seeders/` - Seed data for `users` and `loan_details`

---

## 🚀 Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/yourname/laravel-emi-processing.git
cd laravel-emi-processing
composer install
npm install
npm run dev
php artisan key:generate
php artisan migrate --seed
php artisan serve

Username: developer

Password: Test@Password123#
