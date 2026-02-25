# NepaCare Admin System Documentation

## Overview
The admin system controls both caregiver and elder registrations, with a centralized dashboard for managing users and viewing statistics.

---

## 📁 Files Created in `/admin` Folder

### 1. **login.php** - Admin Authentication
**Location:** `admin/login.php`

**Features:**
- Secure admin login page
- Hardcoded credentials: 
  - Email: `admin@nepacare.com`
  - Password: `admin123`
- Session-based authentication
- Redirects to dashboard on successful login
- Responsive design with blue color scheme

**How it Works:**
```php
// Validates credentials and creates session
if($email === 'admin@nepacare.com' && $password === 'admin123'){
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_name'] = 'Administrator';
    // Redirects to index.php
}
```

---

### 2. **index.php** - Admin Dashboard
**Location:** `admin/index.php`

**Features:**
- Shows system statistics:
  - Total users count
  - Total caregivers
  - Total elders
  - Linked caregiver-elder pairs
- Quick navigation buttons
- Admin sidebar navigation
- Dashboard summary cards

**Database Queries:**
```php
// Real-time counts from database
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"));
$total_caregivers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='caregiver'"));
$total_elders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='elder'"));
$linked_pairs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT linked_elder_id) as count FROM users WHERE linked_elder_id IS NOT NULL"));
```

---

### 3. **manage_registrations.php** - Registration Management
**Location:** `admin/manage_registrations.php`

**Features:**
- View all registered users in a table
- Display: ID, Name, Email, Phone, Role (badge), Registration Date, Status, Actions
- Delete users (with confirmation)
- Deactivate users (with confirmation)
- Role badges: Blue for caregivers, Purple for elders
- Search-friendly table layout

**Actions Available:**
- **Deactivate:** Mark user as inactive (can be reactivated later)
- **Delete:** Permanently remove user from system

**User Roles:**
- Color-coded badges:
  - Caregiver: Blue badge (#e3f2fd)
  - Elder: Purple badge (#f3e5f5)

---

### 4. **manage_users.php** - User Directory
**Location:** `admin/manage_users.php`

**Features:**
- View all users with detailed information
- Shows linked relationships:
  - If caregiver → displays linked elder name
  - If elder → displays linked caregiver name
  - Shows "Not Linked" status if no connection
- Users are sorted by role and ID
- Green linked badge, Red unlinked badge

**Query Logic:**
```php
SELECT u.*, 
       (SELECT name FROM users WHERE id=u.linked_elder_id) as linked_elder_name,
       (SELECT name FROM users WHERE linked_elder_id=u.id AND role='caregiver') as linked_caregiver_name
FROM users u 
ORDER BY u.role, u.id DESC
```

---

### 5. **logout.php** - Session Termination
**Location:** `admin/logout.php`

**Features:**
- Destroys admin session
- Redirects to login page
- Simple and secure

---

## 🔐 Security Features

1. **Session Validation:** Every page checks `if(!isset($_SESSION['admin_id']))` before allowing access
2. **Confirmation Dialogs:** Delete/deactivate actions require confirmation
3. **SQL Protection:** Using mysqli (prepare statements recommended for future upgrades)
4. **Role-based Access:** Only admins can access admin panel

---

## 🎨 Design Features

### Admin Sidebar
- Dark blue background (#1e3a8a)
- Consistent navigation across all pages
- Active page highlighting
- Smooth hover transitions

### Color Scheme
- Primary: #1e3a8a (Dark Blue)
- Accent: #3478e5 (Bright Blue)
- Success: #d4edda (Light Green)
- Danger: #dc3545 (Red)
- Warning: #ffc107 (Yellow)

### Responsive Design
- Flexbox-based layout
- Mobile-friendly sidebar
- Data tables with hover effects
- Badge system for quick identification

---

## 📊 Key Statistics Tracked

1. **Total Users:** All registered caregivers and elders
2. **Caregiver Count:** Only users with role='caregiver'
3. **Elder Count:** Only users with role='elder'
4. **Linked Pairs:** Count of successful caregiver-elder connections

---

## 🔗 How It Controls Registration

### For Caregivers:
- Can view all caregivers registered
- Can see which elder they're linked to
- Admin can deactivate or delete caregiver accounts

### For Elders:
- Can view all elders registered
- Can see which caregiver is linked to them
- Admin can deactivate or delete elder accounts

### Registration Flow:
```
User Signs Up (signup.php)
    ↓
Data Stored in Database
    ↓
Admin Reviews in manage_registrations.php
    ↓
Admin Can Approve (by keeping) or Delete/Deactivate
```

---

## 🔧 Changes Made to Existing Files

### No Changes Required
The existing signup.php, login.php, and registration flow remain unchanged. Users can still register normally, and the admin panel monitors them afterward.

### Database Requirements
No database schema changes needed. The system uses existing `users` table with:
- `id` - User ID
- `name` - User name
- `email` - User email
- `phone` - User phone
- `role` - 'caregiver' or 'elder'
- `linked_elder_id` - For caregivers linked to elders
- `created_at` - Registration timestamp

---

## 🚀 How to Access Admin Panel

1. **Access URL:** `http://localhost/Nepacare/admin/login.php`
2. **Credentials:**
   - Email: `admin@nepacare.com`
   - Password: `admin123`
3. **Dashboard:** Shows overview statistics
4. **Manage Registrations:** View, deactivate, or delete users
5. **Manage Users:** View all users and their linked relationships

---

## 📝 User Status Management

### Deactivate User
- User cannot login
- Account remains in database
- Can be reactivated by direct database update
- Useful for temporary suspension

### Delete User
- Permanently removes user from system
- Cannot be recovered
- All associated data is deleted
- Use with caution

---

## 📈 Future Enhancements (Optional)

1. Add approval workflow (pending → approved → active)
2. Export user data to CSV
3. Search and filter functionality
4. User activity logs
5. Admin password change option
6. Multiple admin accounts with role-based permissions
7. Email notifications on new registrations
8. User profile editing by admin

---

## ✅ Summary

**Admin System Location:** `/admin/` folder

**Core Files:**
1. `login.php` - Authentication
2. `index.php` - Dashboard with statistics
3. `manage_registrations.php` - User management (delete/deactivate)
4. `manage_users.php` - User directory with linking info
5. `logout.php` - Session termination

**Key Features:**
✓ Control both caregiver and elder registrations
✓ View real-time statistics
✓ Delete or deactivate users
✓ See linked relationships
✓ Secure session-based access
✓ Professional UI matching existing design
