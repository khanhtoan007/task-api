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

1. **Cài đặt dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Tạo file `.env`:**
   ```bash
   cp .env.example .env
   ```

3. **Cấu hình file `.env`:**
   
   Mở file `.env` và cập nhật thông tin database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Tạo database:**
   ```bash
   mysql -u root -p
   CREATE DATABASE laravel;
   EXIT;
   ```

5. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

6. **Chạy migrations:**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets (nếu có):**
   ```bash
   npm run build
   # hoặc cho development
   npm run dev
   ```

8. **Chạy development server:**
    ```bash
    php artisan serve
    ```

9. **Truy cập ứng dụng:**
    - Web: http://localhost:8000

#### Các lệnh thường dùng

- **Chạy tests:**
  ```bash
  composer test
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

## Packages

### For Prod

#### # [darkaonline/l5-swagger](https://github.com/DarkaOnLine/L5-Swagger)

L5 Swagger - OpenApi or Swagger Specification for Laravel project made easy.

### For dev:

#### # [Sail](https://laravel.com/docs/10.x/sail)

Laravel Sail is a light-weight command-line interface for interacting with Laravel's default Docker development
environment.

#### # [Pest](https://pestphp.com)

Pest is a testing framework with a focus on simplicity,
meticulously designed to bring back the joy of testing in PHP.

#### # [Laravel Pint](https://laravel.com/docs/10.x/pint)

Laravel Pint is an opinionated PHP code style fixer for minimalists.

#### # [Rector](https://github.com/rectorphp/rector)

Rector instantly upgrades and refactors the PHP code of your application.

