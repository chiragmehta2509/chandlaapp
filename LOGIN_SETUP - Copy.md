# Admin & Client Login Setup Guide

## Database Tables

Both **Admin** and **Client** use the same `users` table. No separate tables are needed.

### Users Table Structure

The `users` table includes:
- `id` - Primary key
- `name` - User's full name
- `email` - Email address (unique)
- `phone` - Phone number (unique, nullable)
- `password` - Hashed password
- `is_admin` - Boolean flag to identify admin users (default: false)
- `is_active` - Boolean flag for account status
- `subscription_status` - User subscription level
- And other fields...

## Setup Instructions

### Step 1: Run Migration

Add the `is_admin` field to the users table:

```bash
php artisan migrate
```

This will add the `is_admin` boolean column to your existing `users` table.

### Step 2: Create Admin User

Run the seeder to create a default admin user:

```bash
php artisan db:seed --class=AdminUserSeeder
```

Or run all seeders:

```bash
php artisan db:seed
```

**Default Admin Credentials:**
- Email: `admin@chandlabook.com`
- Password: `admin123`

⚠️ **IMPORTANT:** Change the admin password immediately after first login!

### Step 3: Access Admin Panel

1. Navigate to: `http://localhost:8000/admin/login`
2. Login with admin credentials
3. You'll have access to the admin dashboard

### Step 4: Access Client Portal

1. Navigate to: `http://localhost:8000/client/register`
2. Create a new account (regular users are NOT admins)
3. Or login at: `http://localhost:8000/client/login`

## How It Works

### Admin Authentication

- **Route:** `/admin/login`
- **Middleware:** `admin.auth`
- **Check:** User must have `is_admin = true` in the database
- **Access:** Full system management (users, events, contacts, payments)

### Client Authentication

- **Route:** `/client/login` or `/client/register`
- **Middleware:** `auth:web`
- **Check:** Any active user can access
- **Access:** Only their own data (events, contacts)

## Creating Additional Admin Users

### Option 1: Via Database

```sql
UPDATE users SET is_admin = 1 WHERE email = 'user@example.com';
```

### Option 2: Via Tinker

```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'user@example.com')->first();
$user->is_admin = true;
$user->save();
```

### Option 3: Via Admin Panel

Once logged in as admin:
1. Go to `/admin/users`
2. Click on a user
3. Edit the user
4. Set `is_admin` to true (you'll need to add this field to the edit form)

## Security Notes

1. **Admin Access:** Only users with `is_admin = true` can access `/admin/*` routes
2. **Client Access:** Regular users can only see their own data
3. **Password:** Always use strong passwords in production
4. **Session:** Uses Laravel's session-based authentication (secure by default)

## Troubleshooting

### Can't login as admin?

1. Check if user exists: `SELECT * FROM users WHERE email = 'admin@chandlabook.com';`
2. Check if `is_admin` is set: `SELECT is_admin FROM users WHERE email = 'admin@chandlabook.com';`
3. Verify password: Try resetting it via tinker
4. Check middleware: Ensure `admin.auth` middleware is working

### Client can't access their data?

1. Verify user is logged in: Check session
2. Check `is_active` status: Should be `true`
3. Verify data ownership: Users can only see their own events/contacts

## Migration File

The migration file is located at:
`database/migrations/2026_02_11_050340_add_is_admin_to_users_table.php`

This adds:
- `is_admin` boolean column (default: false)
- Index for better query performance
