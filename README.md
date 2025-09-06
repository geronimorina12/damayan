# Capstone Project
---

## 🛠️ Tech Stack
- **[Laravel](https://laravel.com/)** – Backend framework for building robust APIs and handling business logic  
- **[Vue.js](https://vuejs.org/)** – Reactive frontend JavaScript framework for a dynamic UI  
- **[Inertia.js](https://inertiajs.com/)** – Bridges Laravel and Vue for smooth SPA-like navigation  
- **[Bootstrap](https://getbootstrap.com/)** – Responsive UI components and styling  
- **[ECharts](https://echarts.apache.org/)** – Interactive charts and visualizations  
- **[Pusher](https://pusher.com/)** – Real-time notifications and updates  


---

## 🚀 Installation

### Prerequisites
- PHP >= 8.1  
- Composer  
- Node.js & npm  
- MySQL or SQLite database  

### Steps
```bash
# Clone the repository
git clone https://github.com/geronimorina12/damayan.git

# Navigate into the project folder
cd online-lost-and-found

# Install backend dependencies
composer install

# Install frontend dependencies
npm install && npm run dev

# Set up environment file
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Start development server
php artisan serve
