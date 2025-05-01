# 📋 Laravel Inertia React Todo App

A full-stack Todo application built with **Laravel**, **Inertia.js**, **React**, and **MySQL**. This project
demonstrates authentication, nested CRUD operations, and collaboration features, making it a solid portfolio piece.

## 🚀 Features

- 🧑‍💻 User Registration and Authentication
- ✅ Full CRUD for Todos
- 📝 Nested CRUD for Tasks (each task belongs to a Todo)
- 🤝 Invite collaborators to shared Todos
- ❌ Remove collaborators from shared Todos
- ⚙️ Built with Laravel (back-end) and React (front-end) using Inertia.js
- 💾 MySQL for data persistence

## 🛠️ Tech Stack

- **Backend:** Laravel 10+
- **Frontend:** React + Inertia.js
- **Database:** MySQL
- **Styling:** Tailwind CSS (or your choice of styling solution)
- **Auth:** Laravel Breeze (Inertia stack) or custom implementation

## ⚙️ Installation

## 👥 Collaboration Flow

- Create a Todo

- Invite users by email to collaborate on a specific Todo

- Collaborators can view, edit, and add tasks

- The owner can remove collaborators at any time

```bash
# Clone the repository
git clone https://github.com/your-username/todo-app.git
cd todo-app

# Install backend dependencies
composer install

# Install frontend dependencies
npm install && npm run dev

# Set up your environment
cp .env.example .env
php artisan key:generate

# Set up your database
php artisan migrate

# Run the server
php artisan serve
```
