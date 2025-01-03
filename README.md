# Database Change Request System

## Overview
This system allows users to request changes to the Countries, States, and Cities database in a controlled and organized way. Think of it like a "suggestion box" where users can propose updates to the data.

## Why Laravel Instead of Core PHP?
As a Core PHP developer, you might wonder why we're using Laravel. Here's why:

### 1. Problems with Core PHP Approach:
```php
// In Core PHP, you'd need to write security manually:
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Manual database connection in every file
$conn = mysqli_connect('localhost', 'username', 'password', 'database');

// Manual SQL injection prevention
$userId = mysqli_real_escape_string($conn, $_POST['user_id']);
$query = "SELECT * FROM users WHERE id = '$userId'";
```

### 2. How Laravel Makes It Better:
```php
// Laravel Authentication - One line!
Route::middleware(['auth'])->group(function () {
    // Your protected routes here
});

// Database queries - Safer and easier
$user = User::find($userId);  // No SQL injection possible!
```

## Project Structure Explained

### 1. Main Folders You'll Work With:
```plaintext
your-project/
│
├── app/                 👉 Your PHP code goes here
│   ├── Http/           
│   │   ├── Controllers/  👉 Like your old .php files that handle requests
│   │   └── Middleware/   👉 Security checks
│   │
│   └── Models/         👉 Database table definitions
│
├── resources/          👉 Your HTML templates
│   └── views/          
│
└── routes/             👉 Like your old index.php that had all URLs
    └── web.php
```

### 2. File Comparisons (Core PHP vs Laravel)

#### Core PHP Way:
```php
// login.php
<?php
session_start();
$conn = mysqli_connect('localhost', 'user', 'pass', 'db');

if ($_POST) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']); // Not secure!
    
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
    }
}
?>
<html>
    <form method="POST">
        <!-- Form HTML here -->
    </form>
</html>
```

#### Laravel Way:
```php
// LoginController.php
class LoginController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }
}

// login.blade.php (HTML template)
@extends('layouts.app')
@section('content')
    <form method="POST" action="{{ route('login') }}">
        <!-- Form HTML here -->
    </form>
@endsection
```

## Step-by-Step Setup Guide

### 1. Install Required Software
```bash
# Install PHP 8.1 or higher
# Install Composer (PHP's package manager)
# Install MySQL or PostgreSQL
```

### 2. Create New Project
```bash
# Create project
composer create-project laravel/laravel change-request-system

# Enter project folder
cd change-request-system

# Install additional packages
composer require laravel/ui
php artisan ui bootstrap --auth
```

### 3. Configure Database
Edit `.env` file:
```plaintext
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Initial Setup
```bash
# Create database tables
php artisan migrate

# Start development server
php artisan serve
```

## Common Tasks Guide

### 1. Creating a New Page
In Core PHP, you'd create a new .php file. In Laravel:

1. Create a Route (routes/web.php):
```php
Route::get('/change-request', [ChangeRequestController::class, 'create']);
```

2. Create a Controller (app/Http/Controllers/ChangeRequestController.php):
```php
class ChangeRequestController extends Controller
{
    public function create()
    {
        return view('change-requests.create');
    }
}
```

3. Create a View (resources/views/change-requests/create.blade.php):
```php
@extends('layouts.app')
@section('content')
    <!-- Your HTML here -->
@endsection
```

### 2. Working with Database
Instead of writing SQL queries:

```php
// Core PHP way
$query = "SELECT * FROM cities WHERE state_id = '$stateId'";
$result = mysqli_query($conn, $query);

// Laravel way
$cities = City::where('state_id', $stateId)->get();
```

### 3. Form Processing
Instead of checking $_POST:

```php
// Core PHP way
if ($_POST) {
    $title = $_POST['title'];
    // Insert into database
}

// Laravel way
public function store(Request $request)
{
    $changeRequest = new ChangeRequest;
    $changeRequest->title = $request->title;
    $changeRequest->save();
}
```

## Common Issues & Solutions

### 1. Page Not Found
- Check routes/web.php
- Use `php artisan route:list` to see all routes

### 2. Database Connection Failed
- Check .env file settings
- Ensure MySQL is running

### 3. Class Not Found
Run: `composer dump-autoload`

## Development Workflow

1. Create new feature branch
2. Make changes
3. Test changes
4. Create pull request
5. Wait for review

## Testing Your Changes

```bash
# Start local server
php artisan serve

# Run tests
php artisan test

# Clear cache if needed
php artisan cache:clear
```

## Need Help?

1. Check Laravel documentation: https://laravel.com/docs
2. Common issues: https://stackoverflow.com/questions/tagged/laravel
3. Local files to check:
   - routes/web.php for URLs
   - app/Http/Controllers for logic
   - resources/views for HTML

## Security Best Practices

1. Always validate user input
2. Use Laravel's built-in CSRF protection
3. Never trust user data
4. Use prepared statements (Laravel does this automatically)

## Performance Tips

1. Use pagination for large data sets:
```php
$cities = City::paginate(100);
```

2. Cache frequently accessed data:
```php
$countries = Cache::remember('countries', 3600, function () {
    return Country::all();
});
```

3. Load only what you need:
```php
$cities = City::select(['id', 'name'])->get();
```

## Questions?
Feel free to ask for help if you get stuck! The best way to learn Laravel is by doing, and it's okay to make mistakes along the way.
