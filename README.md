# Asset Responsibility and Borrowing Management System

A multi-organization web application for managing asset tracking and borrowing requests. Built with Native PHP (No Framework) and Bootstrap 5.

## Features
- **Multi-Organization SaaS Architecture**: Multiple organizations can exist in one system with complete data isolation.
- **Role-Based Access**:
  - **Platform Administrator**: Manages organizations.
  - **Organization Administrator**: Manages assets, users, and borrowing approvals.
  - **Member**: Requests assets and views history.
- **Borrowing Workflow**: Request -> Approval -> Auto-Stock Reduction -> Return -> Log.
- **Secure Authentication**: JWT stored in HttpOnly cookies.

## Folder Structure
```
ukm-asset-access-manager/
├── app/                  # Backend Logic
│   ├── config/           # Database Connection
│   ├── core/             # Middleware & JWT Service
│   ├── controllers/      # Business Logic
│   ├── models/           # Database Interactions
│   └── helpers/          # Response Formatters
├── public/               # Frontend (Web Root)
│   ├── assets/           # CSS, JS, Vendor files
│   ├── pages/            # View Templates (Login, Dashboard, etc.)
│   └── index.php         # API Router
├── database/             # SQL Schema
└── README.md
```

## Setup Instructions

1.  **Database Setup**:
    - Import `database/saas_asset_manager.sql` into MySQL/MariaDB.
    - Check `app/config/database.php` for credentials (default: root/empty).

2.  **Server Setup**:
    - Point your web server (Apache) to the root folder.
    - Ensure `mod_rewrite` is enabled for `.htaccess` to work.

3.  **Default Accounts**:
    - **Platform Owner**: `super@saas.com` / `123456`
    - **New Organizations**: Register via the "Register Organization" link on the login page.

## License
Copyright by NIM_Name_Class
