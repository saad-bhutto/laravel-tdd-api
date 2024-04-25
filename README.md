### Requirements

- PHP 8.x
- Composer
- MySQL or any compatible database
- Optional: Laravel Valet for local development

## Usage

To use this project, follow these steps:

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/saad-bhutto/laravel-tdd-api
   ```

2. **Install Dependencies:**

   Navigate into the project directory and run:

   ```bash
   composer install
   ```

3. **Set Up Environment Variables:**

   Copy the `.env.example` file to `.env`:

   ```bash
   cp .env.example .env
   ```

   Update the `.env` file with your database credentials and other configuration settings.

4. **Run Migrations:**

   Run the database migrations to set up your database schema:

   ```bash
   php artisan migrate
   ```

5. **Run Unit Tests:**

   Run the unit tests to ensure everything is working correctly:

   ```bash
   php artisan test
   ```

6. **Start the Development Server:**

   Start the Laravel development server:

   ```bash
   php artisan serve
   ```

7. **Additional Notes:**

   - Make sure you have PHP, Composer, and a compatible database (e.g., MySQL, PostgreSQL) installed on your system.

---

 ## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please open a pull request.

## License

This project is licensed under the [MIT License](LICENSE).