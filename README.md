# Translation Management Service (Laravel Code Test)

This project is a **Translation Management Service** built with Laravel to demonstrate clean, scalable, and secure coding practices.  
The system allows you to manage translations with support for locales, tags, and exporting translations for frontend usage.

---

## 🚀 Setup Instructions

### 1. Clone the repository
```bash
git clone https://github.com/abdulnasir25/translation-management-service.git
cd translation-service
```

### 2. Install Dependencies:
```bash
composer install
```

### 3. Environment Setup:
```bash
cp .env.example .env
php artisan key:generate
```

Set your database credentials:
```bash
CACHE_STORE=file # or database

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=translation_service
DB_USERNAME=root
DB_PASSWORD=secret
```

### 4. Run migrations
```bash
php artisan migrate
```

### 5. Populate Test Data:
```bash
php artisan app:translations-populate 100000
```

### 6. Start the development server
```bash
 php artisan serve
```

**Performance Benchmarks Expected:**
- CRUD operations: < 50ms
- Search operations: < 150ms  
- Export endpoint: < 500ms even with 100k+ records
- Database queries optimized with proper indexing

**Security Features:**
- Input validation and sanitization
- SQL injection protection via Eloquent ORM
- Mass assignment protection
- XSS protection through proper JSON responses

**Scalability Features:**
- Database indexing strategy for large datasets
- Chunked bulk operations
- Efficient caching system
- Normalized database schema
- Query optimization techniques

## 📌 API Endpoints

### 1. List translations
```bash
GET /api/v1/translations
```

### 2. Create translation
```bash
POST /api/v1/translations
Content-Type: application/json

{
  "key": "welcome.message",
  "locale": "en",
  "content": "Welcome to our application!",
  "tags": ["web", "desktop", "mobile"]
}
```
- Use locale (e.g., en , fr , es )

### 3. Get single translation
```bash
GET /api/v1/translations/{key}/{locale}
```

### 4. Update translation
```bash
PUT /api/v1/translations/{id}
```

### 5. Delete translation
```bash
DELETE /api/v1/translations/{id}
```

### 6. Export translations by locale
```bash
GET /api/v1/translations/export/{locale}?tags=web,desktop,mobile
```


## 📌 Super Simple Testing (No Authentication):

### 1. Create a Translation:
```bash
curl -X POST "http://example-domain.com/api/v1/translations" \
  -H "Content-Type: application/json" \
  -d '{
      "key": "welcome.message",
      "locale": "en", 
      "content": "Welcome to our application!",
      "tags": ["web", "desktop", "mobile"]
  }'
```

### 2. Search Translations:
```bash
curl -X GET "http://example-domain.com/api/v1/translations?key=welcome&locale=en"
```

### 3. Export for Frontend:
```bash
curl -X GET "http://example-domain.com/api/v1/translations/export/en?tags=web,desktop,mobile"
```

### 4. Get Single Translation:
```bash
curl -X GET "http://example-domain.com/api/v1/translations/welcome/en"
```

### 5. Update Translation:
```bash
curl -X PUT "http://example-domain.com/api/v1/translations/1" \
  -H "Content-Type: application/json" \
  -d '{
      "content": "Updated welcome message!",
      "tags": ["web", "desktop", "mobile"]
  }'
```

### 6. Delete Translation:
```bash
curl -X DELETE "http://example-domain.com/api/v1/translations/1"
```

## 🛠 Design Choices

- **SOLID Principles:** Code structured into controllers, services, and repositories for single responsibility and separation of concerns.
- **Repository Pattern:** Used to abstract data access and make it easier to swap or extend storage layers.
- **Service Layer:** Business logic centralized in TranslationService, keeping controllers lightweight.
- **Contracts (Interfaces):** Defined TranslationRepositoryInterface for loose coupling and easier testing.
- **Caching:** Translations are cached for performance on export endpoints.
- **Transactions:** Ensured atomicity during create/update/delete operations with DB::transaction().
- **Tags Support:** Translations can be categorized by tags, making it easy to filter/export subsets.
- **Pagination & Filters:** Search API supports filtering by key, content, locale, and tags with pagination.
- **Extensibility:** Designed so more features (bulk import, versioning, soft deletes) can be added easily.

## ✅ Example cURL Request
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/translations" \
  -H "Content-Type: application/json" \
  -d '{
      "key": "nasir.welcome.message",
      "locale": "en",
      "content": "Welcome to our application, Nasir!",
      "tags": ["web", "mobile"]
  }'
```

**Response:**
```bash
{
  "status": "success",
  "data": {
    "id": 1,
    "key": "nasir.welcome.message",
    "locale": "en",
    "content": "Welcome to our application, Nasir!"
  }
}
```

### 📖 Tech Stack

- PHP 8.2+
- Laravel 12+
- MySQL (or any database supported by Laravel)
- Composer

## 👨‍💻 Author

Developed by **Abdul Nasir Shah** -
**Full Stack PHP Developer (Laravel, OpenCart, PrestaShop)**
