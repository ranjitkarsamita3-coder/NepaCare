# Admin Panel Quick Start Guide

## 📌 Admin System Created Successfully!

### Location
```
c:\xampp\htdocs\Nepacare\admin\
├── login.php                    ← Start here
├── index.php                    ← Dashboard
├── manage_registrations.php     ← Manage users
├── manage_users.php             ← View all users & links
└── logout.php                   ← Logout
```

---

## 🔐 Login Credentials

**URL:** `http://localhost/Nepacare/admin/login.php`

**Email:** `admin@nepacare.com`  
**Password:** `admin123`

---

## 📊 What Admin Can Do

### 1. **Dashboard (index.php)**
- View total number of users
- See caregiver count
- See elder count
- Monitor linked pairs

### 2. **Manage Registrations (manage_registrations.php)**
- View all registered users in table format
- See user details: Name, Email, Phone, Role
- **Deactivate User:** Suspend account temporarily
- **Delete User:** Permanently remove from system

### 3. **Manage Users (manage_users.php)**
- View complete user directory
- See which caregivers are linked to which elders
- Identify unlinked users
- Track all relationships

---

## 🎯 How It Controls Registrations

### Current Flow:
```
Caregiver/Elder Signs Up (signup.php)
           ↓
User accounts stored in database
           ↓
Admin reviews in Manage Registrations
           ↓
Admin approves (keeps) or rejects (deletes/deactivates)
```

### User Roles Supported:
- **Caregiver** - Can be linked to one elder
- **Elder** - Can be linked to one caregiver

---

## 🎨 Features Explained

### Role Badges (in Manage Registrations)
- 🔵 **Blue Badge:** Caregiver
- 🟣 **Purple Badge:** Elder

### Link Status (in Manage Users)
- 🟢 **Green Badge:** Linked (caregiver-elder pair established)
- 🔴 **Red Badge:** Not Linked (no connection yet)

### Action Buttons
- **Deactivate:** Makes user unable to login (reversible)
- **Delete:** Removes user permanently (irreversible)

---

## 📋 Database Tables Used

The admin system uses the existing `users` table:

```
users table:
├── id (Primary Key)
├── name
├── email
├── phone
├── role (caregiver / elder)
├── password
├── linked_elder_id (for caregivers)
├── otp
├── otp_expiry
├── created_at
└── ... other fields
```

---

## 🔧 No Changes Required To Existing Code

✅ **signup.php** - Still works normally  
✅ **login.php** - Users can still login  
✅ **All Elder Pages** - No changes needed  
✅ **All Caregiver Pages** - No changes needed  
✅ **Reminders** - No changes needed  
✅ **Profiles** - No changes needed  

The admin system works **independently** and only **monitors** user registrations!

---

## 🚀 Next Steps

1. **Access Admin:** Go to `http://localhost/Nepacare/admin/login.php`
2. **Login:** Use credentials above
3. **View Dashboard:** Check statistics
4. **Manage Users:** Review registrations
5. **Logout:** Click logout button

---

## 💡 Example Scenarios

### Scenario 1: Review New Registration
1. Go to Manage Registrations
2. See new user in table
3. Review their details
4. Click "Delete" to reject or do nothing to approve

### Scenario 2: Deactivate Problematic User
1. Go to Manage Registrations
2. Find user
3. Click "Deactivate"
4. User cannot login anymore

### Scenario 3: View All Linked Pairs
1. Go to Manage Users
2. Look for green "Linked" badges
3. See which caregiver is linked to which elder
4. Monitor relationships

---

## 🔒 Security Notes

- Only access admin panel from secure networks
- Change default password after first login
- Log out after finishing admin tasks
- Don't share admin credentials

---

## 📞 Support

All admin features are self-contained in the `/admin/` folder and don't affect existing user functionality.

**Files Modified:** NONE (new admin system is separate)  
**Database Changes:** NONE (uses existing tables)  
**User Experience:** UNCHANGED (admin system is independent)

---

**Admin System Created Successfully! ✅**
