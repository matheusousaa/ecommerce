# 🛒 E-Commerce Platform (Laravel + Vue.js + Inertia.js)

This project is a full-featured e-commerce management system built with **Laravel**, **Vue.js**, and **Inertia.js**. It allows users to manage products, categories, and orders with authentication and role-based access, serving as a portfolio piece to showcase backend and frontend integration.

---

## 🚀 Live Demo

> ⚠️ Deployment in progress — system currently available only locally.  
> A live demo may be added soon.

---

## ✨ Features

- 🔐 **Authentication** (Login, Register, Profile)
- 📦 **Product CRUD** (Create, Read, Update, Delete)
- 🗂️ **Category Management**
- 📑 **Order Management**
- 📸 **Image Upload**
- 📊 **Dashboard Overview**
- 🛠️ Built with **Laravel 10**, **Vue 3**, **Inertia.js**, **Tailwind CSS**
- 🌐 PostgreSQL support (MySQL-compatible)

---

## 🖥️ Screenshots

> Add screenshots/gifs here of the product listing, forms, and dashboard.

---

## 🧰 Tech Stack

**Backend:**
- Laravel 10
- PostgreSQL (or MySQL-compatible)
- Eloquent ORM

**Frontend:**
- Vue 3
- Inertia.js
- Tailwind CSS
- Vite

**Other:**
- Authentication via Laravel Breeze (Inertia stack)
- File upload via Laravel Storage
- Session & database cache
- `.env` based environment configuration

---

## ⚙️ Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js + npm
- PostgreSQL (or MySQL)
- Git

### Installation

```bash
git clone https://github.com/matheusousaa/ecommerce.git
cd ecommerce
composer install
npm install
cp .env.example .env
php artisan key:generate
