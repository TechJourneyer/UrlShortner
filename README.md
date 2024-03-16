# URL Shortener Project

## Introduction
This project is a URL shortener application built using Laravel and Tailwind CSS. It allows users to shorten URLs, manage them, and upgrade their plans for more URL quotas.

## Features
- User registration and authentication.
- URL shortening with unique short URLs.
- Maximum 10 URLs per user.
- Listing page to manage URLs (delete, edit, deactivate).
- Simple plan upgrade form.

## Technologies Used
- Laravel (v5-v8) for backend.
- jQuery and Tailwind CSS for frontend.
- MySQL for database.

## Setup Instructions
1. Clone the repository:
    ```bash
    git clone <repository-url>
    ```

2. Install dependencies using Composer:
    ```bash
    composer install
    ```

3. Copy the .env.example file and update the database configuration:
    ```bash
    cp .env.example .env
    ```

4. Generate application key:
    ```bash
    php artisan key:generate
    ```

5. Migrate the database:
    ```bash
    php artisan migrate
    ```
6. Run Seeder:
    ```bash
    php artisan db:seed --class=PlanSeeder
    ```
7. Serve the application:
    ```bash
    php artisan serve
    ```

8. Visit `http://localhost:8000` in your browser to use the application.


