# Book Management API

This project is a Book Management API built with Laravel 10. It provides endpoints for managing books, categories, and book loans in a library system.

## Features

- User authentication (registration, login, logout)
- Email verification
- Password reset functionality
- CRUD operations for books and categories
- Book loan management
- Protected routes using Laravel Sanctum
- Pagination for list endpoints
- Rate limiting to prevent API abuse
- Caching for improved performance

## Tech Stack

- PHP 8.x
- Laravel 10.x
- MySQL (or your preferred database)
- Laravel Sanctum for API authentication
- Redis (for caching)



# API Documentation for Book Management System

## Base URL
All API requests should be made to: `http://localhost:8000/api/v1`

## Authentication
Most endpoints require authentication. Include the authentication token in the header of your requests:
```
Authorization: Bearer your_token_here
```

## Endpoints

### 1. Register
- **URL:** `/register`
- **Method:** POST
- **Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "securepassword",
    "password_confirmation": "securepassword"
  }
  ```
- **Description:** Register a new user.

### 2. Reset Password
- **URL:** `/forgot-password`
- **Method:** POST
- **Body:**
  ```json
  {
    "email": "john.doe@example.com"
  }
  ```
- **Description:** Request a password reset link.

### 3. Login
- **URL:** `/login`
- **Method:** POST
- **Body:**
  ```json
  {
    "email": "john.doe@example.com",
    "password": "securepassword"
  }
  ```
- **Description:** Authenticate a user and receive a token.

### 4. Create Category
- **URL:** `/categories`
- **Method:** POST
- **Authentication:** Required
- **Body:**
  ```json
  {
    "name": "Science Fiction"
  }
  ```
- **Description:** Create a new book category.

### 5. Get All Categories
- **URL:** `/categories`
- **Method:** GET
- **Authentication:** Required
- **Description:** Retrieve a list of all categories.

### 6. Get Category by ID
- **URL:** `/categories/{id}`
- **Method:** GET
- **Authentication:** Required
- **Description:** Retrieve details of a specific category.

### 7. Update Category
- **URL:** `/categories/{id}`
- **Method:** PUT
- **Authentication:** Required
- **Body:**
  ```json
  {
    "name": "Updated Category Name"
  }
  ```
- **Description:** Update an existing category.

### 8. Delete Category
- **URL:** `/categories/{id}`
- **Method:** DELETE
- **Authentication:** Required
- **Description:** Delete a category. Returns a success message.

### 9. Create a Book
- **URL:** `/books`
- **Method:** POST
- **Authentication:** Required
- **Body:**
  ```json
  {
    "title": "The Great Novel",
    "description": "An exciting new book",
    "author": "Famous Author",
    "isbn": "1234567890",
    "published_year": 2023,
    "category_id": 1,
    "total_copies": 5,
    "available_copies": 5
  }
  ```
- **Description:** Add a new book to the system.

### 10. Get a Book
- **URL:** `/books/{id}`
- **Method:** GET
- **Authentication:** Required
- **Description:** Retrieve details of a specific book.

### 11. Get All Books
- **URL:** `/books`
- **Method:** GET
- **Authentication:** Required
- **Description:** Retrieve a list of all books.

### 12. Update Book
- **URL:** `/books/{id}`
- **Method:** PUT
- **Authentication:** Required
- **Body:**
  ```json
  {
    "title": "Updated Book Title",
    "description": "Updated description",
    "author": "Updated Author",
    "isbn": "0987654321",
    "published_year": 2024,
    "category_id": 2,
    "total_copies": 10,
    "available_copies": 8
  }
  ```
- **Description:** Update an existing book's details.

### 13. Delete Book
- **URL:** `/books/{id}`
- **Method:** DELETE
- **Authentication:** Required
- **Description:** Remove a book from the system.

### 14. Borrow Book
- **URL:** `/book-loans`
- **Method:** POST
- **Authentication:** Required
- **Body:**
  ```json
  {
    "book_id": 1,
    "due_date": "2024-12-31"
  }
  ```
- **Description:** Create a new book loan.

### 15. Get Book Loan Details
- **URL:** `/book-loans/{id}`
- **Method:** GET
- **Authentication:** Required
- **Description:** Retrieve details of a specific book loan.

### 16. Get All Book Loans
- **URL:** `/book-loans`
- **Method:** GET
- **Authentication:** Required
- **Description:** Retrieve a list of all book loans.

### 17. Logout
- **URL:** `/logout`
- **Method:** POST
- **Authentication:** Required
- **Description:** End the current user session and invalidate the token.

## Notes
- Replace `{id}` in URLs with the actual ID of the resource.
- Ensure to include the authentication token in the header for protected endpoints.
- All requests should use JSON for request bodies and will receive JSON responses.
## Rate Limiting

This API implements rate limiting to prevent abuse. The current limits are:

- 60 requests per minute for authenticated users
- 30 requests per minute for unauthenticated users

When the limit is exceeded, the API will return a `429 Too Many Requests` response.

## Caching

The API uses caching to improve performance. Cached data includes:

- Book lists (cached for 1 hour)
- Individual book details (cached for 1 day)
- Category lists (cached for 1 day)

Cache is automatically invalidated when related data is updated or deleted.

## Setup and Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Serve the application: `php artisan serve`

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
