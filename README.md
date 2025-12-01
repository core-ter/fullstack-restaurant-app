# 🍽️ Debreceni Étterem - Online Rendelési Rendszer

Modern, teljes körű online rendelési rendszer egy fiktív debreceni étterem számára. A projekt célja egy portfólióra alkalmas, produkciós szintű webalkalmazás bemutatása.

## ✨ Főbb Funkciók

### Felhasználói Oldal
- 🛒 **Online rendelés** vendégként vagy regisztrált felhasználóként
- 🎁 **10% kedvezmény** regisztrált felhasználóknak az első rendelésnél
- ⚡ **Egyszerűsített pénztár** bejelentkezett felhasználóknak (automatikus adatkitöltés)
- 📍 **Dinamikus kiszállítási díj** számítás távolság alapján (OpenStreetMap)
- 📦 **Rendelés követés** valós idejű státusz frissítésekkel
- 📧 **Email értesítések** rendelés állapot változásokról

### Admin Felület
- 📊 **Rendeléskezelő dashboard** valós idejű státusz frissítésekkel
- 🍕 **Étlap szerkesztő** (CRUD műveletek)
- 🚚 **Kiszállítási zónák** konfigurálása
- 📈 **Rendelési statisztikák**
- 👥 **Admin hozzáférés** (biztonságos bejelentkezés)

## 🛠️ Technológiai Stack

- **Frontend:** HTML5, CSS3 (Vanilla), JavaScript (ES6+)
- **Backend:** PHP 8.x
- **Adatbázis:** MySQL 8.0+
- **Térkép:** OpenStreetMap + Leaflet.js
- **Email:** SMTP (Gmail)

## 📁 Projekt Struktúra

```
project-restaurant/
├── database/
│   ├── schema.sql          # Adatbázis séma (11 tábla)
│   └── seed.sql            # Minta adatok
├── public/                 # Nyilvános frontend oldalak
│   ├── admin/              # Admin felület
│   ├── api/                # Backend API végpontok
│   │   ├── auth/
│   │   ├── config/
│   │   ├── menu/
│   │   ├── orders/
│   │   └── utils/
│   ├── assets/             # Frontend eszközök
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   ├── uploads/            # Feltöltött képek
│   ├── index.php
│   ├── menu.php
│   ├── cart.php
│   ├── checkout.php
│   └── ...
├── vendor/                 # Composer függőségek
├── .env.example
├── .gitignore
├── composer.json
└── README.md
```

## 🚀 Telepítés és Futtatás

### Előfeltételek

- PHP 8.0 vagy újabb
- MySQL 8.0 vagy újabb
- Web szerver (Apache/Nginx) vagy PHP built-in szerver
- Composer (opcionális)

### 1. Adatbázis Beállítása

```bash
# Lépj be MySQL-be
mysql -u root -p

# Futtasd a schema fájlt
source database/schema.sql

# Töltsd be a minta adatokat
source database/seed.sql
```

### 2. Environment Konfiguráció

```bash
# Másold le az .env.example fájlt
cp .env.example .env

# Szerkeszd az .env fájlt a saját adataiddal
# - Adatbázis kapcsolat
# - SMTP beállítások (Gmail App Password szükséges)
```

### 3. Alkalmazás Indítása

**Opció A: PHP Built-in Szerver**
```bash
php -S localhost:8000 -t public
```

**Opció B: XAMPP/WAMP**
- Másold a projektet a `htdocs` mappába
- Látogasd meg: `http://localhost/project-restaurant/public`

### 4. Teszt Bejelentkezési Adatok

**Admin felület:**
- felhasználónév: `admin`
- Jelszó: `admin123`

**Teszt felhasználó:**
- Email: `test@example.com`
- Jelszó: `test123`

## 🗄️ Adatbázis Séma

A rendszer **11 táblát** tartalmaz:

1. `users` - Regisztrált felhasználók
2. `addresses` - Mentett kiszállítási címek
3. `admins` - Admin felhasználók
4. `categories` - Étel kategóriák
5. `menu_items` - Étlap tételek
6. `order_statuses` - Rendelési státuszok
7. `orders` - Rendelések
8. `order_items` - Rendelés tételek
9. `order_status_history` - Státusz változások történet
10. `delivery_zones` - Kiszállítási zónák és díjak
11. `restaurant_settings` - Étterem beállítások

## 🎨 Design Highlights
- **Modern UI/UX** glassmorphism elemekkel
- **Reszponzív design** (mobil, tablet, desktop)
- **Smooth animációk** és hover effektek
- **Színpaletta:**
  - Primary: `#E63946` (Élénk piros)
  - Secondary: `#F1FAEE` (Krémfehér)
  - Accent: `#457B9D` (Kék)
  - Success: `#06D6A0` (Zöld)

## 📧 Email Értesítések

A rendszer automatikus email értesítéseket küld:
- ✅ Rendelés elfogadva

## 🔒 Biztonság

- Bcrypt jelszó hashelés
- Email verifikáció támogatás
- SQL injection védelem
- XSS védelem

## 👨‍💻 Készítette

Ez a projekt egy portfólió munka, amely bemutatja a full-stack webfejlesztési képességeimet.

## 📄 Licenc

Ez a projekt oktatási és portfólió célokra készült.

---

**🍔 Jó étvágyat! 🍕**
