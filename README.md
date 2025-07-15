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

> ![dashboard](https://github.com/user-attachments/assets/d016b593-6da3-4f7e-970b-1a5b74190aab)
> ![products](https://github.com/user-attachments/assets/d847b923-fb44-442a-890f-0bd03ed5fbcb)
> ![add product](https://github.com/user-attachments/assets/2f777e7c-5c26-4ce5-93e4-985e23c9a83b)
> ![categories](https://github.com/user-attachments/assets/09852639-6ed8-45d0-b028-534bfd7043c9)
> ![orders](https://github.com/user-attachments/assets/e7842a29-89bc-4393-9ff3-dd1b756407c0)
> ![order details](https://github.com/user-attachments/assets/77e741a2-764d-4c1e-b333-72a4621b58a8)

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
