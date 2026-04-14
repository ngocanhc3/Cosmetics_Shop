# Cosme House - Cửa Hàng Mỹ Phẩm Online

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.0-38B2AC.svg)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1.svg)](https://mysql.com)

Cosme House là một nền tảng thương mại điện tử chuyên về mỹ phẩm được xây dựng bằng Laravel Framework, cung cấp trải nghiệm mua sắm trực tuyến hiện đại với các tính năng tiên tiến.

## ✨ Tính Năng Chính

### 🛍️ Cửa Hàng Online
- **Danh mục sản phẩm đa dạng**: Chăm sóc da, trang điểm, chăm sóc tóc, cơ thể, nước hoa
- **Tìm kiếm và lọc sản phẩm** theo danh mục, thương hiệu, giá cả
- **Chi tiết sản phẩm** với gallery ảnh, mô tả, đánh giá
- **So sánh giá** và khuyến mãi

### 🛒 Giỏ Hàng & Thanh Toán
- **Giỏ hàng thông minh** với cập nhật real-time
- **Mã giảm giá** và voucher vận chuyển
- **Tích điểm thưởng** cho mỗi lần mua hàng
- **Thanh toán đa dạng**: VietQR, các phương thức khác
- **Theo dõi đơn hàng** chi tiết

### 🤖 Trí Tuệ Nhân Tạo
- **Chatbot AI** hỗ trợ tư vấn sản phẩm
- **Gợi ý sản phẩm** dựa trên sở thích
- **Phân tích làn da** và đề xuất sản phẩm phù hợp

### 🎮 Tương Tác Người Dùng
- **Vòng quay may mắn** với cơ hội trúng thưởng
- **Mystery Box** - hộp quà bí mật
- **Đánh giá sản phẩm** với hình ảnh
- **Danh sách yêu thích** cá nhân hóa

### 👨‍💼 Quản Lý Admin
- **Dashboard thống kê** chi tiết
- **Quản lý sản phẩm, danh mục, thương hiệu**
- **Xử lý đơn hàng** và quản lý khách hàng
- **Quản lý khuyến mãi** và voucher
- **Phân quyền người dùng** chi tiết

## 🛠️ Công Nghệ Sử Dụng

### Backend
- **Laravel 12** - Framework PHP hiện đại
- **PHP 8.2+** - Hỗ trợ các tính năng mới nhất
- **MySQL 8.0+** - Cơ sở dữ liệu mạnh mẽ
- **Spatie Laravel Permission** - Quản lý quyền chi tiết

### Frontend
- **TailwindCSS 4.0** - Framework CSS utility-first
- **Alpine.js** - Framework JavaScript reactive
- **Vite** - Build tool nhanh chóng
- **Axios** - HTTP client cho API calls

### Tính Năng Nâng Cao
- **Laravel Socialite** - Đăng nhập mạng xã hội
- **File Storage** - Quản lý ảnh sản phẩm
- **Email Notifications** - Gửi mail xác nhận
- **Caching** - Tối ưu hiệu suất

## 📋 Yêu Cầu Hệ Thống

- **PHP**: 8.2 hoặc cao hơn
- **Composer**: 2.0+
- **Node.js**: 18.0+
- **npm/yarn**: Package manager
- **MySQL**: 8.0+
- **Web Server**: Apache/Nginx với mod_rewrite

## 🚀 Cài Đặt & Chạy Dự Án

### 1. Clone Repository
```bash
git clone https://github.com/your-username/cosme-house.git
cd cosme-house
```

### 2. Cài Đặt Dependencies
```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Node.js dependencies
npm install
```

### 3. Cấu Hình Environment
```bash
# Sao chép file cấu hình
cp .env.example .env

# Tạo application key
php artisan key:generate
```

### 4. Cấu Hình Database
Chỉnh sửa file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cosme_house
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Chạy Migrations & Seeders
```bash
# Tạo database tables
php artisan migrate

# Chạy seeders để tạo dữ liệu mẫu
php artisan db:seed
```

### 6. Build Assets
```bash
# Build CSS/JS cho production
npm run build

# Hoặc chạy development server
npm run dev
```

### 7. Khởi Động Server
```bash
php artisan serve
```

Truy cập: `http://localhost:8000`

## 📁 Cấu Trúc Dự Án

```
cosmetics-shop/
├── app/                          # Application logic
│   ├── Console/Commands/         # Artisan commands
│   ├── Http/Controllers/         # HTTP controllers
│   │   ├── Admin/               # Admin controllers
│   │   ├── Account/             # User account controllers
│   │   └── Shop/                # Shop controllers
│   ├── Mail/                    # Email templates
│   ├── Models/                  # Eloquent models
│   ├── Policies/                # Authorization policies
│   ├── Services/                # Business logic services
│   └── Observers/               # Model observers
├── database/                     # Database files
│   ├── factories/               # Model factories
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
├── public/                       # Public assets
│   ├── css/                     # Compiled CSS
│   ├── js/                      # Compiled JS
│   ├── images/                  # Static images
│   └── storage/                 # File uploads
├── resources/                    # Frontend resources
│   ├── css/                     # Source CSS
│   ├── js/                      # Source JS
│   └── views/                   # Blade templates
├── routes/                       # Route definitions
│   ├── web.php                  # Web routes
│   ├── api.php                  # API routes
│   └── console.php              # Console routes
├── storage/                      # File storage
├── tests/                        # Test files
├── vendor/                       # Composer dependencies
├── .env.example                  # Environment template
├── artisan                       # Laravel CLI
├── composer.json                 # PHP dependencies
├── package.json                  # Node dependencies
├── vite.config.js               # Vite configuration
└── README.md                     # Documentation
```

## 🔧 Lệnh Artisan Thường Dùng

```bash
# Cache management
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Database operations
php artisan migrate
php artisan migrate:rollback
php artisan db:seed

# Queue management
php artisan queue:work
php artisan queue:failed

# Storage link
php artisan storage:link
```

## 🎨 Giao Diện & UX

- **Responsive Design**: Tương thích mọi thiết bị
- **Dark/Light Mode**: Chuyển đổi giao diện
- **Smooth Animations**: Hiệu ứng mượt mà
- **Accessibility**: Thiết kế thân thiện
- **Performance**: Tối ưu tốc độ tải

## 🔐 Bảo Mật

- **CSRF Protection**: Bảo vệ cross-site request
- **XSS Prevention**: Sanitize input data
- **SQL Injection**: Parameter binding
- **Rate Limiting**: Giới hạn request
- **Secure Passwords**: Hashing & validation

## 📊 Giám Sát & Logs

- **Laravel Telescope**: Debug & monitoring
- **Error Logging**: Chi tiết lỗi hệ thống
- **Performance Monitoring**: Theo dõi hiệu suất
- **User Activity**: Ghi log hoạt động

## 🤝 Đóng Góp

1. Fork dự án
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📝 License

Dự án này sử dụng giấy phép MIT. Xem file `LICENSE` để biết thêm chi tiết.

## 👥 Tác Giả

- **Developer**: [Nhom5]
- **Email**: nhom5@gmail.com
- **Website**: https://cosmehouse.vn

## 🙏 Lời Cảm Ơn

Cảm ơn bạn đã quan tâm đến dự án Cosme House! Nếu có câu hỏi hoặc cần hỗ trợ, hãy tạo issue trên GitHub hoặc liên hệ trực tiếp.

---

**⭐ Nếu bạn thích dự án, hãy cho chúng tôi một ngôi sao trên GitHub!**<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
#   c o s m e t i c s _ s h o p 
 
 #   C o s m e t i c s _ S h o p  
 