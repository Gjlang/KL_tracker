# 🌇 KL Guide Tracker — Outdoor & Media Management System

> _"Smart, sleek, and scalable — one dashboard to track it all."_

---

## 🧭 Overview

**KL Guide Tracker** is a full-stack Laravel system designed to manage **Outdoor Advertising**, **Media Social Campaigns**, and **KL The Guide** operations under the BGOC umbrella.  
Built with **Laravel 10**, **Blade**, **Tailwind**, and **Livewire**, it unifies all job data — from *Master Files* to *Coordinators* and *Monthly Trackings* — in one elegant dashboard.

This project is a love letter to structure, design, and function — where **clarity meets control**.

---

## 🚀 Key Features

### 🧱 Master File Module
- Centralized repository for all job orders and clients  
- Search, filter, and export to Excel/PDF  
- Linked records across outdoor, media, and KLTG divisions  

### 🎯 Outdoor Tracking
- Manage **Billboards, Tempboards, Buntings, Signages**, and more  
- Supports **install/dismantle dates**, locations, and site mapping  
- Integrated timeline tracking and editable monthly statuses  

### 📅 Calendar View
- **FullCalendar v5.11.3** integration for job timelines  
- Color-coded events with tooltips and clickable details  
- Filter by client, location, category, and status  

### 💼 Coordinators Section
- Separate pages for **Outdoor**, **KLTG**, and **Media Social** coordinators  
- Inline editing with auto-save for January–December progress  
- Remarks, site details, and editable timelines  

### 📊 Reports & Analytics
- Custom Power BI integrations  
- Dynamic filtering (month, status, year)  
- Exportable data summaries  

### 🧩 Additional Features
- Import Excel files with validation using **Laravel Excel**  
- Role-based access control via **Spatie Permissions**  
- Seamless UI built on brand colors:
  - Dark Blue `#22255b`
  - Red `#d33831`
  - Light Blue `#4bbbed`

---

## ⚙️ Tech Stack

| Layer | Tools |
|:------|:------|
| Backend | Laravel 10, PHP 8.2 |
| Frontend | Blade, TailwindCSS, Alpine.js, Livewire |
| Database | MySQL |
| Exports | Maatwebsite Excel, DomPDF |
| Auth & Roles | Laravel Breeze + Spatie Permissions |
| Calendar | FullCalendar v5.11.3 |
| Hosting | cPanel / XAMPP (local) |

---

## 🧠 Folder Highlights

```bash
app/
 ├── Http/
 │   ├── Controllers/        # Core logic for each module
 │   ├── Requests/           # Validation layer
 │   └── Middleware/
 ├── Models/                 # Eloquent models (MasterFile, OutdoorItem, etc.)
 ├── Exports/                # Excel/PDF exports
 └── Services/               # Business logic helpers

resources/views/
 ├── dashboard.blade.php     # Main control panel
 ├── master_files/           # Create/Edit/List views
 ├── coordinators/           # Outdoor, KLTG, Media sections
 └── components/             # UI components & modals

database/
 ├── migrations/
 ├── seeders/
 └── factories/
