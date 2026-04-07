# Project Guidelines

## Code Style
PHP 8.2 with MySQLi, vanilla HTML/CSS. Use prepared statements for database writes, basic escaping for reads. Session-based authentication using `$_SESSION["student_name"]`. Reference [database.php](database.php) and [auth.php](auth.php) for connection and auth patterns.

## Architecture
Simple 3-tier PHP web application for student event tracking:
- **Database layer**: [database.php](database.php) - MySQLi singleton connection
- **Auth layer**: [auth.php](auth.php) - Session guard, [login.php](login.php)/[registration.php](registration.php) - User flows
- **Presentation layer**: [dashboard.php](dashboard.php) - Main menu, [event-tracker.php](event-tracker.php) - Event listing with CRUD, [event-tracker-form.php](event-tracker-form.php) - Add/edit forms

Database schema in [cocu_db.sql](cocu_db.sql): `students`, `events`, `participations` (currently unused).

## Build and Test
No build system or automated tests. Manual setup required:
1. Install XAMPP (Apache + MySQL + PHP 8.2)
2. Place project in `/xampp/htdocs/UCCD3243-assignment`
3. Create MySQL database `cocu_db`
4. Import [cocu_db.sql](cocu_db.sql)
5. Visit `http://localhost/UCCD3243-assignment/login.php`

Test by registering or using existing "Tester Lee" account.

## Conventions
- **Form handling**: `stripslashes()` + `mysqli_real_escape_string()` + `htmlspecialchars()` for output
- **Flash messages**: `$_SESSION['flash']` with `type`/`msg` structure
- **Input validation**: Type cast GET/POST params like `(int)$_GET['id']` before use
- **Password hashing**: MD5 (insecure - upgrade to bcrypt)
- **Search/filtering**: Loads entire result set (no pagination)

**Potential pitfalls**:
- Hardcoded database credentials in [database.php](database.php)
- No comprehensive input validation (risk of injection)
- Missing [forgot_password.php](forgot_password.php) implementation
- All sorting/search operations load full datasets</content>
<parameter name="filePath">c:\xampp\htdocs\UCCD3243-assignment\.github\copilot-instructions.md