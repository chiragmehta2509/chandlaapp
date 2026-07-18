# Chandla Management System

## Overview
Complete Chandla (Gift/Contribution) management system for tracking gifts, covers, and chandlas for marriage events with multiple payment methods and comprehensive reporting.

## Features

### Categories
- **Chandla**: Traditional monetary gift
- **Cover**: Cover/Shawl gifts
- **Gift**: Other types of gifts

### Payment Methods
- **Hard Form**: Physical/cash payment
- **GPay**: Google Pay digital payment
- **Cash**: Cash payment
- **Other**: Other payment methods

## Database Structure

### Chandlas Table
- `id` - Primary key
- `event_id` - Foreign key to events
- `user_id` - Foreign key to users (event owner)
- `giver_name` - Name of the person giving
- `giver_phone` - Phone number
- `giver_email` - Email address
- `giver_address` - Address
- `category` - Enum: chandla, cover, gift
- `payment_method` - Enum: hard_form, gpay, cash, other
- `amount` - Decimal amount
- `description` - Description of the gift
- `received_date` - Date when received
- `receipt_number` - Receipt number (optional)
- `notes` - Additional notes
- `is_verified` - Verification status
- `verified_at` - Verification timestamp

## Client Portal Features

### Routes
- `GET /client/chandlas` - List all chandlas
- `GET /client/chandlas/create` - Create new chandla
- `POST /client/chandlas` - Store chandla
- `GET /client/chandlas/{id}` - View chandla details
- `GET /client/chandlas/{id}/edit` - Edit chandla
- `PUT /client/chandlas/{id}` - Update chandla
- `DELETE /client/chandlas/{id}` - Delete chandla

### Features
- Filter by event, category, payment method, date range
- View statistics (total amount, by category, by payment method)
- Add chandla records with all details
- Edit and delete records
- View chandlas in event detail page

## Admin Portal Features

### Routes
- `GET /admin/chandlas` - List all chandlas (all users)
- `GET /admin/chandlas/{id}` - View chandla details
- `DELETE /admin/chandlas/{id}` - Delete chandla
- `POST /admin/chandlas/{id}/verify` - Verify chandla
- `GET /admin/reports/chandla` - Generate report
- `GET /admin/reports/chandla/export` - Export CSV report

### Features
- View all chandlas across all users
- Filter by event, category, payment method, date range
- Verify chandla records
- Generate comprehensive reports
- Export reports to CSV

## Report Generation

### Filter Options
- **By Event**: Filter for specific event
- **By Date**: Filter by specific date
- **By Date Range**: Filter by start and end date
- **By Category**: Filter by chandla, cover, or gift
- **By Payment Method**: Filter by payment type

### Report Statistics
- Total records count
- Total amount
- Breakdown by category (count and amount)
- Breakdown by payment method (count and amount)
- Breakdown by event (count and amount)

### Export Features
- Export to CSV format
- Includes all filter criteria
- Contains all relevant fields

## Setup Instructions

### 1. Run Migration
```bash
php artisan migrate
```

This will create the `chandlas` table.

### 2. Access Features

**Client Portal:**
- Navigate to `/client/chandlas` to manage your chandlas
- Add chandlas for your events
- View statistics and filter records

**Admin Portal:**
- Navigate to `/admin/chandlas` to view all chandlas
- Navigate to `/admin/reports/chandla` to generate reports
- Filter and export reports as needed

## Usage Examples

### For a Specific Event
1. Go to `/client/events/{id}` 
2. Click "Add Chandla" button
3. Fill in the form with giver details, category, payment method, and amount
4. Save the record

### Generate Report for Specific Event
1. Go to `/admin/reports/chandla`
2. Select the event from dropdown
3. Click "Generate"
4. View statistics and detailed report
5. Click "Export CSV" to download

### Generate Report for Specific Date
1. Go to `/admin/reports/chandla`
2. Enter the date in "Specific Date" field
3. Click "Generate"
4. View all chandlas received on that date

### Generate Report for Date Range
1. Go to `/admin/reports/chandla`
2. Enter start date and end date
3. Optionally filter by category or payment method
4. Click "Generate"
5. View comprehensive report with statistics

## Integration with Events

- Each chandla is linked to an event
- Event detail page shows all chandlas for that event
- Quick "Add Chandla" button on event page
- Total amount displayed on event page

## Statistics Dashboard

### Client Portal
- Total amount across all chandlas
- Breakdown by category (Chandla, Cover, Gift)
- Filterable statistics

### Admin Portal
- System-wide statistics
- Total records and amounts
- Category-wise breakdown
- Payment method breakdown

## File Structure

### Models
- `app/Models/Chandla.php` - Main model

### Controllers
- `app/Http/Controllers/Client/ChandlaController.php` - Client management
- `app/Http/Controllers/Admin/ChandlaController.php` - Admin management
- `app/Http/Controllers/Admin/ReportController.php` - Report generation

### Views
- `resources/views/client/chandlas/` - Client views (index, create, edit, show)
- `resources/views/admin/chandlas/` - Admin views (index, show)
- `resources/views/admin/reports/chandla.blade.php` - Report view

### Migrations
- `database/migrations/2026_02_11_052214_create_chandlas_table.php`

## Notes
- All amounts are stored in decimal format (supports paise)
- Verification system for admin to verify records
- Receipt numbers can be tracked
- Full audit trail with created/updated timestamps
- Users can only see their own chandlas (client portal)
- Admin can see all chandlas across the system
