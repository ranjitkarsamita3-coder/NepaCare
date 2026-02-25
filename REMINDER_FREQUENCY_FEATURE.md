# NepaCare - Reminder Frequency Feature

## Overview
This update adds support for recurring reminders with frequency options: **Once, Daily, Weekly, and Monthly**.

## Changes Made

### 1. Database Schema Update
- Added `reminder_frequency` column to the `reminders` table
- Column type: VARCHAR(20)
- Default value: 'once'
- Accepted values: 'once', 'daily', 'weekly', 'monthly'

### 2. Files Modified

#### Elder Reminders (`reminders.php`)
- Added frequency dropdown field to the reminder form
- Updated ADD REMINDER logic to capture and store frequency
- Updated EDIT REMINDER logic to update frequency
- Added "Frequency" column to reminders table display
- Display frequency for each reminder (Once, Daily, Weekly, Monthly)

#### Caregiver Reminders (`caregiver_reminders.php`)
- Added frequency dropdown field to the reminder form
- Updated ADD REMINDER logic to capture and store frequency
- Updated UPDATE REMINDER logic to capture and store frequency
- Added "Frequency" column to reminders table display
- Display frequency for each reminder (Once, Daily, Weekly, Monthly)

### 3. New Files Created

#### `migrations.php`
- Automated migration script to add the `reminder_frequency` column
- Can be run from: `http://localhost/Nepacare/migrations.php`
- Only accessible from localhost or by authenticated admin users
- Shows status of each migration

#### `setup.sql`
- SQL script for manual database setup
- Contains the ALTER TABLE command to add the frequency column
- Includes documentation about frequency values

## How to Apply Changes

### Option 1: Automated Migration (Recommended)
1. Navigate to `http://localhost/Nepacare/migrations.php`
2. The migration will automatically add the `reminder_frequency` column if needed
3. You'll see success/error messages for each migration

### Option 2: Manual SQL
1. Open your database administration tool (phpMyAdmin, MySQL Workbench, etc.)
2. Run the SQL command from `setup.sql`:
   ```sql
   ALTER TABLE reminders ADD COLUMN reminder_frequency VARCHAR(20) DEFAULT 'once';
   ```

### Option 3: Direct Database Query
```sql
ALTER TABLE reminders ADD COLUMN reminder_frequency VARCHAR(20) DEFAULT 'once';
```

## Features

### For Elders
- When adding/editing a reminder, select frequency: Once, Daily, Weekly, or Monthly
- View the frequency of each reminder in the reminders list
- All existing reminders default to "Once" (one-time reminder)

### For Caregivers
- When adding/editing a reminder for their linked elder, select frequency
- View the frequency of each reminder in the caregivers reminders list
- Same frequency options as elder reminders

## Reminder Frequency Options

| Frequency | Description |
|-----------|-------------|
| Once | One-time reminder on the specified date and time |
| Daily | Repeats every day at the specified time |
| Weekly | Repeats every week on the same day and time |
| Monthly | Repeats every month on the same date and time |

## Database Backward Compatibility

- Existing reminders will automatically be assigned 'once' as their frequency
- No data loss occurs during the migration
- The feature is fully backward compatible

## Notes

- The frequency field is now required when creating new reminders
- The reminder notification system may need additional implementation to handle recurring reminders (e.g., creating new reminder instances or checking recurrence on retrieval)
- Current implementation stores frequency but doesn't automatically generate recurring instances yet
- Consider implementing a cron job or scheduled task to handle recurring reminders in future versions
