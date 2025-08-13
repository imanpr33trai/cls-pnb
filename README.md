# Punjab Classifieds Website

A modern, secure, and feature-rich classified ads website built with PHP, MySQL, and Vanilla JavaScript. This project features a dynamic admin panel, secure user authentication (local, Google, GitHub), and a clean, responsive user interface.

## Features

-   **Secure User Authentication**: Local (email/password) registration with email verification, plus OAuth2 integration with Google and GitHub.
-   **Dynamic Ad Management**: Users can post, edit, and manage their ads.
-   **Advanced Admin Panel**: A single-page application (SPA-like) experience for managing users, ads, categories, and site settings.
-   **Front Controller Pattern**: Clean, SEO-friendly URLs (e.g., `/category/ad-title`).
-   **Database-Driven Sessions**: Robust and scalable session management.
-   **AJAX-Powered UI**: Live search, dynamic forms, and modal actions for a smooth user experience.

## Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

-   [PHP](https://www.php.net/downloads.php) (>= 8.0)
-   [MySQL](https://www.mysql.com/downloads/) or [MariaDB](https://mariadb.org/download/)
-   [Composer](https://getcomposer.org/download/)
-   [Node.js](https://nodejs.org/en/download/) (for frontend dependencies)

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/cls-pnb.git
    cd cls-pnb
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Install frontend dependencies:**
    ```bash
    npm install
    ```

## Configuration

The project uses a `.env` file for environment-specific configuration.

1.  **Create the environment file:**
    Copy the example file to a new `.env` file in the root directory.
    ```bash
    cp .env.example .env
    ```

2.  **Configure your environment variables:**
    Open the `.env` file and fill in the required values for your local setup.

    -   `BASE_URL`: The full base URL of your project (e.g., `http://localhost/cls-pnb/`).
    -   `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`: Your database connection details.
    -   `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`: Your Google OAuth credentials.
    -   `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET`: Your GitHub OAuth credentials.
    -   `MAIL_*`: Your SMTP server details for sending emails.

## Database Setup

1.  **Create a database:**
    Create a new MySQL database with the name you specified in your `.env` file (`DB_NAME`).

2.  **Import the database schema:**
    Import the `schema.sql` file to set up the necessary tables.
    ```bash
    mysql -u YOUR_USERNAME -p YOUR_DATABASE_NAME < schema.sql
    ```
    If there are updates, also import the `update-schema.sql` file:
    ```bash
    mysql -u YOUR_USERNAME -p YOUR_DATABASE_NAME < update-schema.sql
    ```

## Running the Application

You can run the application using a local server like XAMPP, WAMP, or PHP's built-in server.

**Using PHP's built-in server:**

```bash
php -S localhost:8000
```

Now you can access the application by navigating to `http://localhost:8000` in your web browser.

-   **Admin Panel**: `http://localhost:8000/admin`
-   **Admin Login**: Use the credentials you create or find in your database.
