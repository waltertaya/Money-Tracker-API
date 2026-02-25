# Money Tracker API
A RESTful backend API built with Laravel (PHP) for managing multiple user wallets and tracking financial transcations (income and expenses).

This project was developed as part of a backend technical assessment.

## Objective
Build a backend-only Money tracker API that:

## Tech stack

## Project Structure
```text
.
├───app
│   ├───Http
│   │   └───Controllers
│   │       └───Api
│   ├───Models
│   └───Providers
├───bootstrap
│   └───cache
├───config
├───database
│   ├───factories
│   ├───migrations
│   └───seeders
├───public
│   └───build
│       └───assets
├───resources
│   ├───css
│   ├───js
│   └───views
├───routes
├───storage
│   ├───app
│   │   ├───private
│   │   └───public
│   ├───framework
│   │   ├───cache
│   │   │   └───data
│   │   ├───sessions
│   │   ├───testing
│   │   └───views
│   └───logs
└───tests
    ├───Feature
    └───Unit
```

## Setup and Contribution Guide

### Prerequisites
- PHP 8.2+
- Composer
- Node.js and npm
- MySQL

### 1) Clone the repository
```bash
git clone https://github.com/waltertaya/Money-Tracker-API.git
cd Money-Tracker-API
```

### 2) Install dependencies and initialize the project
```bash
composer setup
```

This command will:
- Install PHP dependencies
- Create `.env` from `.env.example` (if missing)
- Generate the application key
- Run database migrations
- Install frontend dependencies
- Build frontend assets

### 3) Configure environment
Update `.env` database values to match your local MySQL setup:
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=money_tracker`
- `DB_USERNAME=your_username`
- `DB_PASSWORD=your_password`

If you change database settings after setup, run:
```bash
php artisan migrate
```

### 4) Run the app in development
```bash
npm install
npm run dev

php artisan serve
# composer dev
```

After the server is running, open the API documentation at:
- http://127.0.0.1:8000/docs

All API routes are documented in the docs page.

### 5) Run tests
```bash
php artisan test tests/Feature/UserTest.php tests/Feature/WalletTest.php tests/Feature/TransactionTest.php 
# composer test
```

### Contribution workflow
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature-name`)
3. Commit with clear messages
4. Run tests before pushing (`composer test`)
5. Open a pull request with a short description of your changes


## Database ERD
<img width="831" height="1122" alt="money_tracker erd" src="https://github.com/user-attachments/assets/3d078071-c07c-4e0b-bc5c-9fd41e6c3878" />


## Author
- [walter](https://www.github.com/waltertaya)
