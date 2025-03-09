# Laravel JWT Authentication with Login Notifications

This project is a Laravel-based API that implements JWT (JSON Web Token) authentication. It includes user registration, login, and email notifications for login attempts. The notification includes details such as IP address, location, browser, and timestamp.

---

## Features

- **User Registration**: Register a new user with name, email, and password.
- **User Login**: Authenticate users and generate a JWT token.
- **Login Notifications**: Send email notifications to users upon successful login, containing login details (IP, location, browser, etc.).
- **Background Notifications**: Notifications are sent in the background using Laravel's queue system.
- **Geolocation**: Fetch the user's location based on their IP address.

---

## Prerequisites

- PHP >= 8.0
- Composer
- MySQL or any other supported database
- SMTP credentials (e.g., Mailtrap) for sending emails

---

## Installation

1. **Clone the repository**:
	```bash
	git clone https://github.com/shahrozdaniel/jwt_login_notify.git
	cd jwt_login_notify
	```

2. **Install dependencies**:
	```bash
	composer install
	```

3. **Set up the environment file**:
	Copy `.env.example` to `.env`:
	```bash
	cp .env.example .env
	```

4. **Update the `.env` file with your database and email credentials**:
	```env
	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
	DB_DATABASE=jwt_login_notify
	DB_USERNAME=root
	DB_PASSWORD=

	MAIL_MAILER=smtp
	MAIL_HOST=smtp.gmail.com
	MAIL_PORT=587
	MAIL_USERNAME=your-email@gmail.com
	MAIL_PASSWORD=your-email-password
	MAIL_ENCRYPTION=tls
	MAIL_FROM_ADDRESS=your-email@gmail.com
	MAIL_FROM_NAME="${APP_NAME}"
	```

5. **Generate application key**:
	```bash
	php artisan key:generate
	```

6. **Set up JWT**:
	Publish the JWT configuration:
	```bash
	php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
	```
	Generate the JWT secret key:
	```bash
	php artisan jwt:secret
	```

7. **Run migrations**:
	```bash
	php artisan migrate
	```

8. **Run the development server**:
	```bash
	php artisan serve
	```

---

## Usage

### API Endpoints

#### Register a User:
- **URL**: `/api/register`
- **Method**: `POST`
- **Body**:
	```json
	{
	 "name": "John Doe",
	 "email": "johndoe@example.com",
	 "password": "password123"
	}
	```

#### Login:
- **URL**: `/api/login`
- **Method**: `POST`
- **Body**:
	```json
	{
	 "email": "johndoe@example.com",
	 "password": "password123"
	}
	```
- **Response**:
	```json
	{
	 "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
	 "token_type": "bearer",
	 "expires_in": 3600
	}
	```

#### Testing Token:
- **URL**: `/api/index`
- **Method**: `GET`
- **Headers**:
	```
	Authorization: Bearer <your_jwt_token>
	```
- **Response**:
	```json
	{
		"message": "Token is valid!",
		"user": {
			"id": 1,
			"name": "John Doe",
			"email": "johndoe@example.com"
		}
	}
	```

---

## Testing

### Testing with Postman
1. Use the `/api/register` endpoint to create a new user.
2. Use the `/api/login` endpoint to authenticate and get a JWT token.
3. Use the token to access protected routes.

### Testing Emails
- Use a tool like Mailtrap or your Gmail account to test email notifications.
- Ensure your `.env` file has the correct SMTP credentials.

---

## Troubleshooting

### Issue: Localhost IP (127.0.0.1) Cannot Be Geolocated
When testing locally, the IP address 127.0.0.1 cannot be geolocated. To resolve this:

#### Hardcode a Public IP for Testing:
Replace `$request->ip()` your own Public IP address or a  public IP address (e.g., 8.8.8.8):
```php
$ip = '8.8.8.8'; // Google's public DNS IP
$location = Location::get($ip);
```

---

## Background Notifications
Notifications are sent in the background using Laravel's queue system. Ensure your queue worker is running:
```bash
php artisan queue:work
```

---

## Geolocation
The project uses the `stevebauman/location` package to fetch the user's location based on their IP address. If the package fails to resolve the location, it defaults to "Unknown".
