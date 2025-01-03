# Tailwind CSS Integration Guide

## Initial Setup

### 1. Install Required Node Packages
```bash
# Install Node.js dependencies
npm install -D tailwindcss postcss autoprefixer

# Initialize Tailwind CSS
npx tailwindcss init -p
```

### 2. Configure Tailwind CSS
Update `tailwind.config.js`:
```js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

### 3. Add Tailwind to CSS
Update `resources/css/app.css`:
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### 4. Update Laravel Layout
Update `resources/views/layouts/app.blade.php`:
```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">
    <div id="app">
        <!-- Your content here -->
    </div>
</body>
</html>
```

### 5. Run Development Server
```bash
# Start Vite development server
npm run dev

# In another terminal, start Laravel server
php artisan serve
```

## Common Tailwind Components

### 1. Navigation Bar
```html
<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <span class="text-xl font-bold text-gray-800">Change Request System</span>
            </div>
            
            <!-- Navigation Links -->
            <div class="flex">
                <a href="/dashboard" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                    Dashboard
                </a>
                <a href="/requests" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                    Requests
                </a>
            </div>
        </div>
    </div>
</nav>
```

### 2. Form Components
```html
<!-- Input Field -->
<div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">
        Title
    </label>
    <input type="text" 
           name="title" 
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
           required>
</div>

<!-- Select Field -->
<div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">
        Table
    </label>
    <select name="table" 
            class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:shadow-outline">
        <option value="countries">Countries</option>
        <option value="states">States</option>
        <option value="cities">Cities</option>
    </select>
</div>

<!-- Button -->
<button type="submit" 
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
    Submit Request
</button>
```

### 3. Table Layout
```html
<div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Title
                </th>
                <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($requests as $request)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ $request->title }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($request->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                    'bg-red-100 text-red-800') }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <a href="/requests/{{ $request->id }}" 
                       class="text-indigo-600 hover:text-indigo-900">
                        View
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

### 4. Card Layout
```html
<div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
    <div class="px-6 py-4">
        <div class="font-bold text-xl mb-2">Request Title</div>
        <p class="text-gray-700 text-base">
            Request description goes here...
        </p>
    </div>
    <div class="px-6 pt-4 pb-2">
        <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">
            #pending
        </span>
    </div>
</div>
```

### 5. Modal Component
```html
<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <!-- Modal content here -->
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">
                        Confirm
                    </button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
```

## Responsive Design Tips

1. Use Tailwind's responsive prefixes:
```html
<div class="w-full md:w-1/2 lg:w-1/3">
    <!-- Content adapts at different breakpoints -->
</div>
```

2. Mobile-first design:
```html
<div class="flex flex-col md:flex-row">
    <!-- Stacks on mobile, side-by-side on desktop -->
</div>
```

3. Hide/Show elements:
```html
<div class="hidden md:block">
    <!-- Only visible on medium screens and up -->
</div>
```

## Best Practices

1. Extract common patterns into components:
```php
// resources/views/components/button.blade.php
@props(['type' => 'button'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded']) }}>
    {{ $slot }}
</button>

// Usage
<x-button type="submit">
    Save Changes
</x-button>
```

2. Use configuration for common values:
```js
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: '#1a73e8',
        secondary: '#5f6368',
      }
    }
  }
}
```

3. Group related utilities with @apply:
```css
@layer components {
  .btn-primary {
    @apply bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700;
  }
}
```
