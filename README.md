# C&B Hub - Student Event Management System

A lightweight, fast, and efficient event management platform exclusively designed for university students at **Vietnam National University (VNU)**. C&B Hub enables students to create, manage, and discover campus events with an intuitive interface and robust backend.

**Live Demo:** [student-event-management-plum.vercel.app](https://student-event-management-plum.vercel.app)

---

## 📋 Table of Contents

- [About](#-about)
- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [Project Structure](#-project-structure)
- [Prerequisites](#-prerequisites)
- [Installation & Setup](#-installation--setup)
- [Usage](#-usage)
- [Configuration](#-configuration)
- [Contributing](#-contributing)
- [Team](#-team)
- [Contact](#-contact)
- [License](#-license)

---

## 📌 About

**C&B Hub** is a student-centric event management platform developed specifically for the Vietnam National University community. Whether you're organizing a club meeting, academic conference, cultural event, or social gathering, C&B Hub provides an easy-to-use platform to connect with fellow students and manage events seamlessly.

**Target Audience:** University students at VNU

---

## ✨ Features

### Core Functionality
- **Event Creation & Management** - Create and manage university events with ease
- **Event Discovery** - Browse and search for upcoming campus events
- **Event Registration** - Students can register for events they're interested in
- **Event Details** - View comprehensive information about events including date, time, location, and description
- **User Profiles** - Manage user accounts and preferences
- **VNU Community** - Exclusive platform for VNU students only

### Performance & Architecture
- **Server-Side Rendering** - Fast initial page loads with server-side MVC architecture
- **Vanilla JavaScript** - Zero framework overhead for lightweight performance
- **Instant Load Times** - No build tools, transpiling, or heavy library dependencies needed
- **Native Browser Execution** - Code runs directly in the browser without compilation

---

## 🛠️ Technology Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP (83.9%) |
| **Frontend** | Vanilla JavaScript (2.9%), HTML, CSS (13.2%) |
| **Architecture** | Central Front Router with Server-Side Rendering (SSR) |
| **Deployment** | Vercel |

### Why This Stack?

The project uses vanilla technologies (HTML, CSS, JavaScript) with a traditional PHP backend specifically to:
- Eliminate framework overhead and unnecessary complexity
- Provide instant feedback without build processes or transpilation
- Minimize browser download and parsing time for faster user experience
- Reduce development environment complexity (no Node.js or package managers needed)
- Keep the codebase lightweight, maintainable, and fast

---

## 📁 Project Structure

```
student-event-management/
├── infrastructure/                # Application infrastructure
│   ├── app/                       # Application logic and controllers
│   ├── models/                    # Database models and data access
│   ├── config/                    # Configuration files
│   ├── public/                    # Static assets (CSS, JS, images)
│   └── .htaccess                  # Apache routing configuration
├── docs.txt                       # Project documentation
├── design.drawio                  # System design diagrams
└── README.md                      # This file
```

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 7.4
- **Web Server** (Apache with mod_rewrite enabled)
- **MySQL** or compatible database
- **Git** (for version control)

### Optional
- **Composer** (if using PHP dependency management)
- **A modern web browser** (Chrome, Firefox, Safari, Edge)

---

## 🚀 Installation & Setup

### Step 1: Clone the Repository

```bash
git clone https://github.com/whateveriswhatever/student-event-management.git
cd student-event-management
```

### Step 2: Server Configuration

The project uses Apache with mod_rewrite for URL routing. Ensure `.htaccess` is enabled on your server:

1. Place the project in your web root (e.g., `/var/www/html` or `C:\xampp\htdocs`)
2. Verify Apache's `mod_rewrite` module is enabled:
   ```bash
   sudo a2enmod rewrite      # Linux
   # For Windows/XAMPP, uncomment in httpd.conf
   ```

### Step 3: Database Setup

1. Create a database for the application:
   ```sql
   CREATE DATABASE c_and_b_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Configure database credentials in `infrastructure/config/` files

3. Import database schema (if provided):
   ```bash
   mysql -u root -p c_and_b_hub < database.sql
   ```

### Step 4: Directory Permissions

Set proper permissions for writable directories:

```bash
chmod -R 755 infrastructure/
chmod -R 777 infrastructure/public/uploads/    # If uploads are enabled
```

### Step 5: Access the Application

1. Start your web server (PHP built-in server or Apache)
2. Navigate to `http://localhost` or your configured domain
3. The application should load automatically via the front router

---

## 💻 Usage

### For End Users

1. **Browse Events**
   - Visit the homepage to see featured events
   - Use the search/filter functionality to find specific events
   - Filter by category, date, or location

2. **Create an Event**
   - Log in with your VNU student account
   - Click "Create Event" and fill in event details
   - Set date, time, location, and description
   - Upload event banner/cover image
   - Publish your event for other VNU students to discover

3. **Register for Events**
   - Browse available events
   - Click on an event to view full details
   - Register or RSVP if you're interested
   - Track your registered events in your dashboard

4. **Manage Your Events**
   - Access your dashboard to view events you've created
   - Edit or delete your events as needed
   - View attendee lists and manage registrations

### For Developers

1. **Understanding the Architecture**
   - Entry point: Central Front Router (handles all requests)
   - Routes requests to appropriate controllers in `infrastructure/app/`
   - Models in `infrastructure/models/` handle data operations
   - Static assets served from `infrastructure/public/`

2. **Adding New Features**
   - Create controller classes in `infrastructure/app/`
   - Define database models in `infrastructure/models/`
   - Add static assets (CSS, JS) to `infrastructure/public/`

3. **Database Operations**
   - Query examples and connection details in `infrastructure/config/`
   - Follow the existing model patterns for consistency

---

## ⚙️ Configuration

### Key Configuration Files

**`infrastructure/config/`** - Contains database connections and application settings

### Environment Variables

Create a `.env` file in the root directory (optional):

```
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=your_password
DB_NAME=c_and_b_hub
APP_URL=http://localhost
VNU_DOMAIN=vnu.edu.vn
```

### Apache Configuration (.htaccess)

The `.htaccess` file handles:
- URL rewriting for clean routes
- Pretty URLs (e.g., `/events/123` instead of `/index.php?id=123`)
- Static file handling (CSS, JS, images)
- Security configurations
- VNU domain verification

---

## 📝 Development Guidelines

### Code Style
- Follow PSR-12 for PHP code
- Use semantic HTML5
- Use vanilla JavaScript (no frameworks)
- Keep CSS modular and maintainable

### Adding New Pages
1. Create a controller in `infrastructure/app/`
2. Define routes in the front router
3. Add view templates as PHP files
4. Style with CSS in `infrastructure/public/css/`
5. Add interactivity with vanilla JS in `infrastructure/public/js/`

### Commit Messages
Please follow conventional commit format:
- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation update
- `style:` - Code style change
- `refactor:` - Code refactoring
- `test:` - Test additions/changes

---

## 🤝 Contributing

We welcome all contributions from everyone! Whether you're a VNU student or not, your contributions help make C&B Hub better for the entire VNU community.

### How to Contribute

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'feat: Add amazing feature'`)
4. **Push** to your branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request with a clear description of your changes

### Contribution Areas
- **Bug fixes** - Found a bug? Let us know!
- **Feature requests** - Have an idea? Submit a pull request!
- **Documentation** - Help improve our docs
- **UI/UX improvements** - Make the interface more user-friendly
- **Performance optimization** - Help us keep C&B Hub fast
- **Testing** - Add tests for new features
- **Localization** - Help translate for Vietnamese language support

### Code of Conduct
- Be respectful and inclusive
- Provide constructive feedback
- Follow the project's code style guidelines
- Test your changes before submitting

---

## 👥 Team

| Name | Student ID | Role |
|------|-----------|------|
| **Duy La** | 22071044 | Lead Developer |
| **Tuan Nguyen** | 22071083 | Backend Developer |
| **Khanh Phan** | 22070980 | Frontend Developer |

---

## 📧 Contact

For questions, suggestions, or support:

- **Primary Contact:** [22071044@vnu.edu.vn](mailto:22071044@vnu.edu.vn)
- **GitHub Issues:** [Report issues here](https://github.com/whateveriswhatever/student-event-management/issues)
- **GitHub Discussions:** [Join our discussions](https://github.com/whateveriswhatever/student-event-management/discussions)

Feel free to reach out with questions, bug reports, or feature requests!

---

## 📄 License

This project is currently unlicensed. Please add a LICENSE file if you plan to distribute it.

---

## 🆘 Support & Troubleshooting

### Common Issues

**Issue: 404 errors on all pages**
- Ensure `.htaccess` is enabled and `mod_rewrite` is active
- Check file permissions in the root directory
- Verify the base URL configuration

**Issue: Database connection errors**
- Verify database credentials in configuration files
- Ensure MySQL server is running
- Check database name and user permissions

**Issue: CSS/JS not loading**
- Verify files exist in `infrastructure/public/`
- Check browser console for path errors
- Clear browser cache and reload

**Issue: VNU email verification not working**
- Ensure your email ends with `@vnu.edu.vn`
- Check your email spam folder for verification link
- Contact the development team if issues persist

---

## 🙏 Acknowledgments

Special thanks to:
- Vietnam National University for the opportunity to develop this platform
- All contributors who have helped improve C&B Hub
- The VNU student community for valuable feedback

---

**Built with ❤️ by the C&B Hub Team for the VNU Community**

*Making student events more accessible and connected* 🎉

