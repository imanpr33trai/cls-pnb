import os
import mysql.connector
from dotenv import load_dotenv
import sys

# Load environment variables from .env file
load_dotenv()

def delete_user(email):
    """Deletes a user from the database by email."""
    try:
        # Database connection details from .env
        db_host = os.getenv("DB_HOST")
        db_user = os.getenv("DB_USER")
        db_password = os.getenv("DB_PASSWORD")
        db_name = os.getenv("DB_NAME")

        if not all([db_host, db_user, db_password, db_name]):
            print("Error: Database credentials not fully set in .env file.")
            sys.exit(1)

        conn = mysql.connector.connect(
            host=db_host,
            user=db_user,
            password=db_password,
            database=db_name
        )
        cursor = conn.cursor()

        # Check if user exists
        cursor.execute("SELECT id FROM users WHERE email = %s", (email,))
        user_exists = cursor.fetchone()

        if not user_exists:
            print(f"User with email '{email}' not found in the database.")
            return

        # Confirmation
        confirm = input(f"Are you sure you want to delete user '{email}'? (yes/no): ").lower()
        if confirm != 'yes':
            print("User deletion cancelled.")
            return

        # Delete the user
        cursor.execute("DELETE FROM users WHERE email = %s", (email,))
        conn.commit()

        if cursor.rowcount > 0:
            print(f"User '{email}' deleted successfully.")
        else:
            print(f"Failed to delete user '{email}'.")

    except mysql.connector.Error as err:
        print(f"Database error: {err}")
    except Exception as e:
        print(f"An unexpected error occurred: {e}")
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python delete_user_cli.py <user_email>")
        sys.exit(1)

    user_email = sys.argv[1]
    delete_user(user_email)
