# Project Structure Overview

## Directory Tree

```
prabasini/
│
├── 📂 config/
│   ├── database.php                 # Database configuration (EDIT THIS)
│   └── database.php.template        # Template untuk berbagai environment
│
├── 📂 public/
│   ├── 🎯 index.php                # Dashboard/Landing page
│   ├── 🎯 pap.php                  # Main PAP management page
│   ├── 🎯 health-check.php         # System diagnostics
│   ├── 📂 css/                     # Custom CSS files
│   ├── 📂 js/                      # Custom JavaScript files
│   └── 📂 uploads/                 # Temporary uploaded files
│       └── .gitkeep
│
├── 📂 src/
│   ├── ExcelImporter.php           # Excel/CSV parser
│   ├── ExcelHelpers.php            # Parsing helper classes
│   ├── PapRepository.php           # Database CRUD operations
│   ├── Helper.php                  # Utility functions
│   └── api_pap.php                 # API endpoints
│
├── 📂 logs/
│   └── activity.log                # Activity log (auto-created)
│
├── 📋 Documentation
│   ├── README.md                   # Complete documentation
│   ├── QUICKSTART.md              # Quick start guide
│   ├── INSTALL.md                 # Detailed setup instructions
│   ├── API.md                     # API reference
│   ├── CHANGELOG.md               # Version history
│   └── PROJECT_STRUCTURE.md       # This file
│
├── 📝 Configuration
│   ├── .gitignore                 # Git ignore rules
│   ├── setup.sql                  # Database setup script
│   ├── composer.json              # Project metadata
│   └── SAMPLE_PAP_DATA.csv       # Sample data
│
└── 🔗 Git
    └── .git/                      # Git repository
```

---

## File Descriptions

### Core Application Files

#### `public/index.php` (140 lines)
- Landing page / Dashboard
- Shows available modules
- Beautiful responsive design
- Links to main features

#### `public/pap.php` (380 lines)
- **Main application file**
- Features:
  - File upload dengan drag & drop
  - Data table dengan pagination ready
  - Action buttons (WA.me, Copy, Delete)
  - Real-time updates
  - Bootstrap + Tailwind styling
  - Vanilla JavaScript (no jQuery)

#### `public/health-check.php` (150 lines)
- System diagnostics page
- Checks:
  - PHP version
  - Database connection
  - File permissions
  - Required PHP extensions
  - Database table status

### Backend Classes

#### `src/PapRepository.php` (180 lines)
- Database operations class
- Methods:
  - `createTableIfNotExists()` - Auto create table
  - `insert()` - Add new record
  - `getAll()` - Get all records
  - `getById()` - Get single record
  - `update()` - Update record
  - `updateStatus()` - Update status field
  - `delete()` - Delete record
  - `importFromArray()` - Bulk import

#### `src/ExcelImporter.php` (120 lines)
- File parsing class
- Supports:
  - .xlsx files (built-in ZIP extraction)
  - .xls files (fallback to CSV)
  - .csv files (auto delimiter detection)
- Methods:
  - `parseExcel()` - Auto detect format
  - `parseXlsx()` - Parse modern Excel
  - `parseXls()` - Parse legacy Excel
  - `parseCsv()` - Parse CSV

#### `src/ExcelHelpers.php` (140 lines)
- Advanced parsing utilities
- Classes:
  - `CsvSimpleReader` - CSV parsing with auto-delimiter
  - `AdvancedExcelReader` - Better XLSX support

#### `src/Helper.php` (220 lines)
- Utility functions
- Features:
  - WhatsApp number formatting
  - DateTime formatting
  - Input sanitization
  - File validation
  - Activity logging
  - JSON response helper
  - Status badge generation

#### `src/api_pap.php` (180 lines)
- API endpoints handler
- Actions:
  - `upload` - Handle file upload
  - `get_list` - Return all data
  - `delete` - Delete record
  - `update_status` - Update status

### Configuration Files

#### `config/database.php` (30 lines)
- **EDIT THIS FILE FIRST**
- PDO connection setup
- Supports multiple environments:
  - Localhost (XAMPP/WAMP/MAMP)
  - Production server
  - Shared hosting (cPanel)

#### `config/database.php.template` (60 lines)
- Template untuk berbagai setup
- Contains comments untuk setiap environment
- Copy ke database.php dan edit

### Database

#### `setup.sql` (30 lines)
- Database creation script
- Table creation untuk PAP
- Indexes untuk performance
- Sample data (commented)

### Documentation

| File | Purpose |
|------|---------|
| **README.md** | Complete system documentation |
| **QUICKSTART.md** | 30-second setup guide |
| **INSTALL.md** | Detailed installation steps |
| **API.md** | API endpoints reference |
| **CHANGELOG.md** | Version history & roadmap |
| **PROJECT_STRUCTURE.md** | This file |

---

## Technology Stack

### Backend
- **Language**: PHP 7.4+
- **Database**: MySQL 5.7 / MariaDB 10.2+
- **Architecture**: OOP with Repository pattern

### Frontend
- **HTML5**: Semantic markup
- **CSS**: Bootstrap 5 + Tailwind CSS
- **JavaScript**: Vanilla (no jQuery)
- **Icons**: Font Awesome 6.4

### Tools & Libraries
- **Git**: Version control
- **Composer**: PHP package manager (optional)
- **ZipArchive**: XLSX parsing
- **DOM/SimpleXML**: XML parsing

---

## Development Workflow

### Adding New Feature

1. **Create database layer** (src/Repository.php)
   ```php
   public function newFeature() {
       // Implementation
   }
   ```

2. **Create API endpoint** (src/api_*.php)
   ```php
   case 'new_action':
       handleNewAction($repo);
       break;
   ```

3. **Update frontend** (public/page.php)
   ```js
   fetch('/src/api_pap.php?action=new_action')
   ```

4. **Update documentation** (README.md, API.md)

### Code Standards

- Use clear, descriptive names
- Add DocBlocks untuk classes & methods
- Handle exceptions properly
- Sanitize user input
- Use prepared statements untuk SQL
- Keep functions focused & small

---

## File Relationships

```
User Browser
    ↓
public/pap.php (UI)
    ↓
JavaScript fetch() calls
    ↓
src/api_pap.php (API Handler)
    ↓
src/PapRepository.php (Database)
    ↓
config/database.php (PDO Connection)
    ↓
MySQL Database
```

---

## Important Files to Edit

### 1. **config/database.php** (CRITICAL)
   Edit dengan konfigurasi MySQL Anda

### 2. **setup.sql** (FIRST TIME ONLY)
   Import ke database untuk membuat struktur

### 3. **public/uploads/.gitkeep**
   Pastikan folder ini writable (755/777)

---

## Size Estimate

| Component | Size |
|-----------|------|
| Backend PHP | ~1200 lines |
| Frontend HTML/JS | ~450 lines |
| Configuration | ~150 lines |
| Documentation | ~2000 lines |
| **Total** | **~3800 lines** |

---

## Performance Considerations

- ✓ Indexed database queries
- ✓ Lazy loading untuk large datasets
- ✓ CSS/JS dari CDN
- ✓ Minimal database calls
- ✓ Efficient file parsing
- ✓ No unnecessary redirects
- ⚠ Future: Add caching layer
- ⚠ Future: Add pagination

---

## Security Notes

- ✓ Input sanitization
- ✓ Prepared SQL statements
- ✓ HTML escaping untuk output
- ✓ File type validation
- ✓ File size limits
- ⚠ Future: Add authentication
- ⚠ Future: Add CSRF protection
- ⚠ Future: Add rate limiting

---

## Scalability

Current implementation suitable untuk:
- ✓ Small to medium projects
- ✓ Up to 100K records
- ✓ Single user access

Untuk scale up:
- Add authentication layer
- Implement caching (Redis/Memcached)
- Add job queue untuk large imports
- Implement API versioning
- Add monitoring & logging

---

**Last Updated**: 2024-01-15
**Version**: 1.0.0
**Status**: ✅ Production Ready
