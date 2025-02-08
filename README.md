# CSC Update Tool

![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)

The CSC Update Tool is a Laravel-based application designed to manage and track database change requests. It provides a structured workflow for submitting, reviewing, and implementing changes to database records.

## Features

- **User Authentication**
  - Role-based access control (User, Moderator, Admin)
  - Email verification
  - Password reset functionality

- **Change Request Management**
  - Create, view, and track change requests
  - Commenting system for requests
  - File attachments for supporting documents
  - Search and filter functionality

- **Data Management**
  - Paginated data viewing
  - Data validation and backup
  - Audit logging and change history tracking

- **Performance Optimization**
  - Fast page load times (< 2 seconds)
  - Lazy loading for large datasets
  - Caching for frequently accessed data

## Technology Stack

- **Backend**: Laravel 11
- **Frontend**: Tailwind CSS, Alpine.js
- **Database**: MySQL
- **Caching**: Redis
- **Queue**: Database
- **Authentication**: Magic Link, Github

## Installation

1. Clone the repository:
```bash
git clone https://github.com/dr5hn/csc-update-tool.git
cd csc-update-tool
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment variables:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run migrations:
```bash
php artisan migrate
```

6. Configure environment variables:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csc-update-tool
DB_USERNAME=root
DB_PASSWORD=
```

7. Run the development server:
```bash
npm run dev
php artisan serve
```

## Configuration

Key configuration files:
- `config/change-request.php` - Change request settings
- `config/services.php` - Third-party service credentials
- `tailwind.config.js` - Tailwind CSS configuration

Environment variables:
- `APP_NAME` - Application name
- `MAIL_*` - Email configuration
- `GITHUB_CLIENT_ID` - GitHub OAuth credentials
- `REDIS_*` - Redis connection settings

## API Endpoints

### Authentication
```plaintext
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
POST /api/auth/reset-password
```

### Change Requests
```plaintext
GET /api/change-requests
POST /api/change-requests
GET /api/change-requests/{id}
PUT /api/change-requests/{id}
DELETE /api/change-requests/{id}
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a pull request

## License

This project is open-source and available under the [MIT License](LICENSE).

## Security Vulnerabilities

If you discover a security vulnerability, please raise an issue. All security vulnerabilities will be promptly addressed.

## Credits

- [Laravel](https://laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Mailtrap](https://mailtrap.io) for email testing
