# Asset Responsibility and Borrowing Management System

A multi-organization web application for managing asset tracking and borrowing requests. Built with Native PHP (No Framework) and Tailwind CSS.

**Live Demo**: [https://raffdev.my.id](https://raffdev.my.id)

## Preview Admin
<img width="1918" height="921" alt="image" src="https://github.com/user-attachments/assets/8727fef8-3177-46c6-bb5f-3967acc95e95" />
<img width="1912" height="913" alt="image" src="https://github.com/user-attachments/assets/53dcbf9e-e292-4f1e-b217-b0c875de90b9" />
<img width="1918" height="917" alt="image" src="https://github.com/user-attachments/assets/bf9ec6b3-7c57-44a4-a8ec-36998fc8352d" />
<img width="1909" height="914" alt="image" src="https://github.com/user-attachments/assets/fc150c60-3c03-40c5-96fc-78d9fedbb273" />
<img width="1911" height="917" alt="image" src="https://github.com/user-attachments/assets/abf6c122-ba59-420f-bc4a-ec934ba635dd" />
<img width="1917" height="915" alt="image" src="https://github.com/user-attachments/assets/3a679057-025f-45d1-bdeb-648bd5867a31" />

## Preview User
<img width="1914" height="916" alt="image" src="https://github.com/user-attachments/assets/7fd19115-f134-4cae-8575-8b0c1d48ceac" />
<img width="1910" height="915" alt="image" src="https://github.com/user-attachments/assets/939ba5d0-1e0e-45fb-b4ad-941a97087638" />



## Fitur
- **Multi-Organization SaaS Architecture**: Multiple organizations with complete data isolation
- **Role-Based Access**:
  - **Super Admin**: mengelola organisasi yang terdaftar
  - **Organization Administrator**: Mengelola aset, pengguna, and pengajuan peminjaman
  - **Member**: Meminjam aset dan melihat histori peminjaman
- **Alur Kerja Peminjaman**: Pengajuan → Penyetujuan → Stok aset dikurangi otomatis → Pengembalian → Penyetujuan jika barang dikembalikkan dalam kondisi baik
- **Secure Authentication**: JWT stored in HttpOnly cookies
- **Responsive UI**: Built with Tailwind CSS and custom theming

## Tech Stack
- **Backend**: Native PHP 7.4+ (No Framework)
- **Database**: MySQL/MariaDB
- **Frontend**: Tailwind CSS 3.4 (Local Build)
- **Authentication**: JWT (JSON Web Tokens)
- **Routing**: Apache mod_rewrite

## Folder Structure
```
asset_management/
├── app/                  # Backend Logic
│   ├── config/           # Database Connection & Environment
│   ├── core/             # Middleware & JWT Service
│   ├── controllers/      # Business Logic
│   ├── models/           # Database Interactions
│   └── helpers/          # Response Formatters
├── public/               # Frontend (Web Root)
│   ├── assets/           # CSS, JS, Images
│   │   ├── css/          # Compiled Tailwind CSS
│   │   └── js/           # Application Scripts
│   ├── pages/            # View Templates
│   └── index.php         # API Router
├── src/                  # Tailwind Source
│   └── input.css         # Tailwind Entry Point
├── database/             # SQL Schema
├── tailwind.config.js    # Tailwind Configuration
└── package.json          # NPM Dependencies
```

## Local Development Setup

### 1. Clone & Install Dependencies
```bash
git clone <repository-url>
cd asset_management
npm install
```

### 2. Build CSS
```bash
npm run build    # Production build
# or
npm run watch    # Development (auto-rebuild)
```

### 3. Database Setup
- Import `database/saas_asset_manager.sql` into MySQL/MariaDB
- Create `.env` file in `app/config/`:
```php
<?php
putenv('DB_HOST=localhost');
putenv('DB_NAME=saas_asset_manager');
putenv('DB_USER=root');
putenv('DB_PASS=');
putenv('JWT_SECRET=your_secret_key');
putenv('DISPLAY_ERRORS=1');
```

### 4. Server Configuration
- **Apache**: Point document root to project folder, enable `mod_rewrite`
- **Access**: `http://localhost/asset_management/public/pages/login.php`

### 5. Default Accounts
- **Super Admin**: `super@saas.com` / `123456`
- **New Organizations**: Register via "Register Organization" on login page

## Production Deployment (cPanel)

See detailed guide: [`deployment_guide.md`](deployment_guide.md)

**Quick Steps**:
1. Run `npm run build` locally
2. Upload all files **except** `node_modules/`
3. Create database & import SQL
4. Configure `app/config/env.php` with cPanel credentials
5. Upload `.htaccess` files for routing
6. Access your domain

## Project Structure Notes

- **API Routes**: `/public/api/{controller}/{action}`
- **Authentication**: Cookie-based JWT (HttpOnly, 24h expiry)
- **Role-Based Theming**: CSS variables for Admin (Teal) vs Super Admin (Purple)
- **CSS Build**: Production CSS is in `public/assets/css/styles.css`

## Development Commands

```bash
npm run build   # Build production CSS
npm run watch   # Watch mode for development
```

## License
Copyright © 2025 Raffly - 23552011278 CNS A

---
