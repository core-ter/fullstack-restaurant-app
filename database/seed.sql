-- ============================================
-- Debreceni Étterem - Sample Data
-- Description: Seed data for development and testing
-- ============================================

USE restaurant_db;
SET NAMES utf8mb4;

-- ============================================
-- Insert Order Statuses
-- ============================================
INSERT INTO order_statuses (status_code, display_name, color_hex, display_order) VALUES
('pending', 'Elfogadásra vár', '#FFA500', 1),
('confirmed', 'Elfogadva', '#06D6A0', 2),
('preparing', 'Készítés alatt', '#3B82F6', 3),
('delivering', 'Kiszállítás alatt', '#8B5CF6', 4),
('completed', 'Teljesítve', '#10B981', 5),
('cancelled', 'Törölve', '#EF4444', 6);

-- ============================================
-- Insert Delivery Zones
-- ============================================
INSERT INTO delivery_zones (distance_from_km, distance_to_km, fee, delivery_time_minutes) VALUES
(0.00, 2.00, 0, 20),
(2.01, 5.00, 500, 30),
(5.01, 10.00, 1000, 45),
(10.01, 999.99, 1500, 60);

-- ============================================
-- Insert Restaurant Settings
-- ============================================
INSERT INTO restaurant_settings (
    restaurant_name, 
    address, 
    latitude, 
    longitude, 
    phone, 
    email, 
    opening_hours,
    min_order_amount
) VALUES (
    'Debreceni Étterem',
    'Debrecen, Piac utca 1.',
    47.5316,
    21.6273,
    '+36 30 123 4567',
    'info@debrecenietterem.hu',
    '{"monday": "10:00-22:00", "tuesday": "10:00-22:00", "wednesday": "10:00-22:00", "thursday": "10:00-22:00", "friday": "10:00-23:00", "saturday": "11:00-23:00", "sunday": "11:00-21:00"}',
    2000.00
);

-- ============================================
-- Insert Categories
-- ============================================
INSERT INTO categories (name, display_order) VALUES
('Levesek', 1),
('Főételek', 2),
('Saláták', 3),
('Desszertek', 4),
('Italok', 5);

-- ============================================
-- Insert Sample Menu Items
-- ============================================

-- Levesek
INSERT INTO menu_items (category_id, name, slug, description, ingredients, allergens, price, image_url, is_available, display_order) VALUES
(1, 'Gulyásleves', 'gulyasleves', 'Eredeti magyar gulyásleves, gazdag fűszerezéssel', 'Marhahús, burgonya, paprika, hagyma, fűszerek', 'gluten', 1290.00, '/assets/images/menu/gulyas.jpg', TRUE, 1),
(1, 'Halászlé', 'halaszle', 'Finom pontyból készült halászlé, csípősen', 'Pontyfilé, paprika, hagyma, paradicsom', 'fish', 1490.00, '/assets/images/menu/halaszle.jpg', TRUE, 2),
(1, 'Húsleves', 'husleves', 'Házi húsleves cérnametélttel', 'Marhahús, zöldségek, cérnametélt', 'gluten,egg', 990.00, '/assets/images/menu/husleves.jpg', TRUE, 3);

-- Főételek
INSERT INTO menu_items (category_id, name, slug, description, ingredients, allergens, price, image_url, is_available, display_order) VALUES
(2, 'Rántott hús', 'rantott-hus', 'Klasszikus rántott hús hasábburgonyával', 'Sertéshús, zsemlemorzsa, burgonya', 'gluten,egg', 2490.00, '/assets/images/menu/rantott-hus.jpg', TRUE, 1),
(2, 'Pörkölt galuskával', 'porkolt-galuska', 'Házi pörkölt nokedlivel', 'Marhahús, hagyma, paprika, galuskával', 'gluten,egg', 2690.00, '/assets/images/menu/porkolt.jpg', TRUE, 2),
(2, 'Töltött káposzta', 'toltott-kaposzta', 'Magyar töltött káposzta tejföllel', 'Savanyú káposzta, darált hús, rizs, tejföl', 'gluten,lactose', 2390.00, '/assets/images/menu/toltott-kaposzta.jpg', TRUE, 3),
(2, 'Csirkepaprikás', 'csirkepaprikas', 'Csirkepaprikás házi nokedlivel', 'Csirkemell, paprika, tejföl, nokedli', 'gluten,lactose,egg', 2590.00, '/assets/images/menu/csirkepaprikas.jpg', TRUE, 4);

-- Saláták
INSERT INTO menu_items (category_id, name, slug, description, ingredients, allergens, price, image_url, is_available, display_order) VALUES
(3, 'Vitaminsaláta', 'vitaminsalata', 'Friss zöldségekből készült saláta', 'Saláta, paradicsom, uborka, paprika', '', 890.00, '/assets/images/menu/vitaminsalata.jpg', TRUE, 1),
(3, 'Görög saláta', 'gorog-salata', 'Fetasajttal és olívabogyóval', 'Saláta, paradicsom, uborka, feta sajt, olívabogyó', 'lactose', 1290.00, '/assets/images/menu/gorog-salata.jpg', TRUE, 2);

-- Desszertek
INSERT INTO menu_items (category_id, name, slug, description, ingredients, allergens, price, image_url, is_available, display_order) VALUES
(4, 'Somlói galuska', 'somloi-galuska', 'Eredeti somlói galuska', 'Piskóta, dió, csokoládé, tejszín', 'gluten,lactose,nuts,egg', 1190.00, '/assets/images/menu/somloi.jpg', TRUE, 1),
(4, 'Palacsinta', 'palacsinta', 'Házi palacsinta lekvárral vagy nutellával', 'Tojás, liszt, tej, lekvár/nutella', 'gluten,lactose,egg,nuts', 790.00, '/assets/images/menu/palacsinta.jpg', TRUE, 2),
(4, 'Túrógombóc', 'turogomboc', 'Klasszikus túrógombóc', 'Túró, tojás, liszt, tejföl', 'gluten,lactose,egg', 990.00, '/assets/images/menu/turogomboc.jpg', TRUE, 3);

-- Italok
INSERT INTO menu_items (category_id, name, slug, description, ingredients, allergens, price, image_url, is_available, display_order) VALUES
(5, 'Coca-Cola (0.5L)', 'coca-cola-05', 'Coca-Cola üdítő', '', '', 450.00, '/assets/images/menu/cola.jpg', TRUE, 1),
(5, 'Fanta (0.5L)', 'fanta-05', 'Fanta narancs üdítő', '', '', 450.00, '/assets/images/menu/fanta.jpg', TRUE, 2),
(5, 'Ásványvíz (0.5L)', 'asvanyviz-05', 'Szénsavas vagy szénsavmentes ásványvíz', '', '', 350.00, '/assets/images/menu/viz.jpg', TRUE, 3),
(5, 'Soproni (0.5L)', 'soproni-05', 'Soproni világos sör', '', '', 550.00, '/assets/images/menu/sor.jpg', TRUE, 4);

-- ============================================
-- Insert Sample Admin User
-- Password: admin123 (bcrypt hashed)
-- ============================================
INSERT INTO admins (username, email, password_hash, first_name, last_name, role, is_active) VALUES
('admin', 'admin@debrecenietterem.hu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Felhasználó', 'super_admin', TRUE);

-- ============================================
-- Insert Sample Test User
-- Password: test123 (bcrypt hashed)
-- ============================================
INSERT INTO users (email, password_hash, first_name, last_name, phone, email_verified, is_first_order) VALUES
('test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Teszt', 'Felhasználó', '+36301234567', TRUE, TRUE);

-- ============================================
-- Insert Sample Address for Test User
-- ============================================
INSERT INTO addresses (user_id, address_line, latitude, longitude, is_default) VALUES
(1, 'Debrecen, Kossuth tér 1.', 47.5294, 21.6256, TRUE);

-- ============================================
-- Sample data inserted successfully!
-- Login credentials:
-- Admin: admin@debrecenietterem.hu / admin123
-- User: test@example.com / test123
-- ============================================
