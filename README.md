<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Hướng dẫn chạy project

### Cách 1: Chạy với Docker (Khuyến nghị)

#### Yêu cầu hệ thống

- Docker và Docker Compose
- Git

#### Cài đặt và chạy project

1. **Clone repository và di chuyển vào thư mục project:**
   ```bash
   cd /path/to/project
   ```

2. **Đảm bảo file `.env.example` tồn tại:**
   
   File `.env` sẽ được tự động tạo từ `.env.example` khi container khởi động lần đầu.
   
   Nếu chưa có, tạo từ template:
   ```bash
   cp .env-example .env.example
   ```

3. **Build và khởi động các container:**
   ```bash
   docker-compose build
   docker-compose up -d
   ```

4. **Chạy migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

5. **Tạo dữ liệu mẫu (tùy chọn):**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

6. **Truy cập ứng dụng:**
   - Web: http://localhost:8000
   - MySQL: localhost:3306
     - Database: `laravel`
     - Username: `laravel`
     - Password: `laravel`
     - Root Password: `root`

#### Các lệnh Docker thường dùng

- **Xem logs:**
  ```bash
  docker-compose logs -f app
  ```

- **Chạy Artisan commands:**
  ```bash
  docker-compose exec app php artisan [command]
  ```

- **Truy cập vào container:**
  ```bash
  docker-compose exec app bash
  ```

- **Dừng containers:**
  ```bash
  docker-compose down
  ```

- **Dừng và xóa volumes (xóa database):**
  ```bash
  docker-compose down -v
  ```

- **Rebuild containers:**
  ```bash
  docker-compose build --no-cache
  docker-compose up -d
  ```

#### Lưu ý

- File permissions được tự động fix để bạn có thể edit files từ host mà không cần sudo
- File `.env` sẽ được tự động tạo và APP_KEY sẽ được generate khi container khởi động lần đầu
- Nếu gặp vấn đề về permissions, chạy:
  ```bash
  docker-compose exec app chown -R 1000:1000 /var/www/html
  ```

### Cách 2: Chạy không dùng Docker

#### Yêu cầu hệ thống

- PHP >= 8.1 với các extensions: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML
- Composer
- MySQL >= 8.0 hoặc MariaDB >= 10.3
- Node.js và NPM (cho frontend assets)

#### Cài đặt và chạy project

1. **Di chuyển vào thư mục src:**
   ```bash
   cd src
   ```

2. **Cài đặt dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Tạo file `.env`:**
   ```bash
   cp .env.example .env
   ```

4. **Cấu hình file `.env`:**
   
   Mở file `.env` và cập nhật thông tin database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Tạo database:**
   ```bash
   mysql -u root -p
   CREATE DATABASE laravel;
   EXIT;
   ```

6. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

7. **Chạy migrations:**
   ```bash
   php artisan migrate
   ```

8. **Tạo dữ liệu mẫu (tùy chọn):**
   ```bash
   php artisan db:seed
   ```

9. **Build frontend assets (nếu có):**
   ```bash
   npm run build
   # hoặc cho development
   npm run dev
   ```

10. **Chạy development server:**
    ```bash
    php artisan serve
    ```

11. **Truy cập ứng dụng:**
    - Web: http://localhost:8000

#### Các lệnh thường dùng

- **Chạy migrations:**
  ```bash
  php artisan migrate
  ```

- **Rollback migrations:**
  ```bash
  php artisan migrate:rollback
  ```

- **Xóa cache:**
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  ```

- **Chạy tests:**
  ```bash
  php artisan test
  ```

### Cấu trúc project

- `app/` - Laravel application code
- `config/` - Configuration files
- `database/` - Migrations, factories, seeders
- `routes/` - Route definitions
- `public/` - Public web root
- `docker/` - Docker configuration files
  - `docker/php/Dockerfile` - PHP-FPM container
  - `docker/php/entrypoint.sh` - Entrypoint script
  - `docker/nginx/default.conf` - Nginx configuration
- `docker-compose.yml` - Docker Compose configuration

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).
