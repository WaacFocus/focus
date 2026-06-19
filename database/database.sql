-- MySQL dump converted from SQLite
-- Generated: 2026-06-19 14:49:03

SET FOREIGN_KEY_CHECKS=0;
SET sql_mode='NO_AUTO_VALUE_ON_ZERO';

-- Table: `cache`
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `cache_locks`
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `client_service`
DROP TABLE IF EXISTS `client_service`;
CREATE TABLE `client_service` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `price_override` DECIMAL(15,4),
  `start_date` DATE,
  `end_date` DATE,
  `notes` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `client_service` (`id`, `client_id`, `service_id`, `price_override`, `start_date`, `end_date`, `notes`, `created_at`, `updated_at`) VALUES
('1', '1', '1', NULL, NULL, NULL, NULL, '2026-06-10 16:03:00', '2026-06-10 16:03:00'),
('2', '2', '1', NULL, NULL, NULL, NULL, '2026-06-10 16:08:24', '2026-06-10 16:08:24'),
('3', '2', '2', NULL, NULL, NULL, NULL, '2026-06-10 16:11:33', '2026-06-10 16:11:33'),
('6', '2', '4', NULL, '2026-06-17', NULL, NULL, '2026-06-17 08:41:57', '2026-06-17 08:41:57'),
('7', '1', '4', NULL, NULL, NULL, NULL, '2026-06-17 14:19:35', '2026-06-17 14:19:35');

-- Table: `client_types`
DROP TABLE IF EXISTS `client_types`;
CREATE TABLE `client_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `sort_order` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `client_types` (`id`, `name`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'Limited Company', '1', '1', '2026-06-17 09:10:42', '2026-06-17 09:10:42'),
('2', 'Sole Trader', '2', '1', '2026-06-17 09:10:42', '2026-06-17 09:10:42'),
('3', 'Partnership', '3', '1', '2026-06-17 09:10:42', '2026-06-17 09:10:42');

-- Table: `clients`
DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_name` VARCHAR(255) NOT NULL,
  `contact_name` VARCHAR(255),
  `email` VARCHAR(255),
  `phone` VARCHAR(255),
  `address` VARCHAR(255),
  `town` VARCHAR(255),
  `county` VARCHAR(255),
  `postcode` VARCHAR(255),
  `vat_number` VARCHAR(255),
  `company_number` VARCHAR(255),
  `utr_number` VARCHAR(255),
  `status` VARCHAR(255) NOT NULL DEFAULT 'active',
  `notes` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `client_code` VARCHAR(255),
  `account_manager` VARCHAR(255),
  `fpa_year_end` DATE,
  `fpa_amount` DECIMAL(15,4),
  `billing_interval` VARCHAR(255),
  `sa_billed_separately` TINYINT(1) NOT NULL DEFAULT '0',
  `payroll_invoiced_separately` TINYINT(1) NOT NULL DEFAULT '0',
  `payroll_fpa` DECIMAL(15,4),
  `payroll_billing_interval` VARCHAR(255),
  `payment_method` VARCHAR(255),
  `paye_ref` VARCHAR(255),
  `client_type_id` BIGINT UNSIGNED,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clients` (`id`, `company_name`, `contact_name`, `email`, `phone`, `address`, `town`, `county`, `postcode`, `vat_number`, `company_number`, `utr_number`, `status`, `notes`, `created_at`, `updated_at`, `client_code`, `account_manager`, `fpa_year_end`, `fpa_amount`, `billing_interval`, `sa_billed_separately`, `payroll_invoiced_separately`, `payroll_fpa`, `payroll_billing_interval`, `payment_method`, `paye_ref`, `client_type_id`) VALUES
('1', 'First Class', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, '2026-06-08 17:01:56', '2026-06-08 17:01:56', NULL, NULL, NULL, '1000', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL),
('2', 'Snappy Limited', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, '2026-06-08 17:27:08', '2026-06-08 17:27:08', NULL, NULL, NULL, '250', 'monthly', '0', '0', NULL, NULL, NULL, NULL, NULL);

-- Table: `failed_jobs`
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` VARCHAR(255) NOT NULL,
  `queue` VARCHAR(255) NOT NULL,
  `payload` TEXT NOT NULL,
  `exception` TEXT NOT NULL,
  `failed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `job_batches`
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` BIGINT UNSIGNED NOT NULL,
  `pending_jobs` BIGINT UNSIGNED NOT NULL,
  `failed_jobs` BIGINT UNSIGNED NOT NULL,
  `failed_job_ids` TEXT NOT NULL,
  `options` TEXT,
  `cancelled_at` BIGINT UNSIGNED,
  `created_at` BIGINT UNSIGNED NOT NULL,
  `finished_at` BIGINT UNSIGNED,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `jobs`
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` TEXT NOT NULL,
  `attempts` BIGINT UNSIGNED NOT NULL,
  `reserved_at` BIGINT UNSIGNED,
  `available_at` BIGINT UNSIGNED NOT NULL,
  `created_at` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `migrations`
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
('1', '0001_01_01_000000_create_users_table', '1'),
('2', '0001_01_01_000001_create_cache_table', '1'),
('3', '0001_01_01_000002_create_jobs_table', '1'),
('4', '2026_06_08_000001_create_clients_table', '2'),
('5', '2026_06_08_000002_create_services_table', '2'),
('6', '2026_06_08_000003_create_products_table', '2'),
('7', '2026_06_08_000004_create_projects_table', '2'),
('8', '2026_06_08_000005_create_tasks_table', '2'),
('9', '2026_06_08_000006_create_client_service_table', '2'),
('10', '2026_06_08_000007_create_project_products_table', '2'),
('11', '2026_06_08_000008_create_renewals_table', '2'),
('12', '2026_06_08_000009_add_fpa_fields_to_clients_table', '3'),
('13', '2026_06_08_000010_rename_team_cam_drop_tcp_company_on_clients', '4'),
('14', '2026_06_08_000011_add_paye_ref_to_clients_table', '5'),
('15', '2026_06_09_000001_create_jobs_table', '6'),
('16', '2026_06_10_000001_add_role_to_users_table', '7'),
('17', '2026_06_17_000001_create_client_types_table', '8'),
('18', '2026_06_17_000002_add_client_type_id_to_clients_table', '9'),
('19', '2026_06_17_000003_add_is_urgent_to_tasks_table', '10');

-- Table: `password_reset_tokens`
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` DATETIME,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `practice_jobs`
DROP TABLE IF EXISTS `practice_jobs`;
CREATE TABLE `practice_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `client_id` BIGINT UNSIGNED,
  `assigned_to` BIGINT UNSIGNED NOT NULL,
  `frequency` VARCHAR(255) NOT NULL DEFAULT 'monthly',
  `due_date` DATE NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `completed_at` DATETIME,
  `notes` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `practice_jobs` (`id`, `name`, `description`, `client_id`, `assigned_to`, `frequency`, `due_date`, `status`, `completed_at`, `notes`, `created_at`, `updated_at`) VALUES
('1', 'Annual Accounts', NULL, '1', '1', 'yearly', '2026-06-30 00:00:00', 'pending', NULL, NULL, '2026-06-10 16:03:00', '2026-06-10 16:03:00'),
('2', 'Annual Accounts', NULL, '2', '1', 'yearly', '2026-06-30 00:00:00', 'completed', '2026-06-10 16:08:48', NULL, '2026-06-10 16:08:24', '2026-06-10 16:08:48'),
('3', 'Annual Accounts', NULL, '2', '1', 'yearly', '2027-06-30 00:00:00', 'pending', NULL, NULL, '2026-06-10 16:08:48', '2026-06-10 16:08:48'),
('4', 'Self Assessments', NULL, '2', '1', 'yearly', '2027-01-31 00:00:00', 'pending', NULL, NULL, '2026-06-10 16:11:33', '2026-06-10 16:11:33'),
('5', 'Self Assessment', NULL, '1', '1', 'yearly', '2026-06-30 00:00:00', 'pending', NULL, NULL, '2026-06-10 16:12:21', '2026-06-10 16:12:21'),
('7', 'VAT return', NULL, '2', '2', 'monthly', '2026-07-07 00:00:00', 'pending', NULL, NULL, '2026-06-17 08:41:57', '2026-06-17 08:41:57'),
('8', 'VAT return', NULL, '1', '2', 'monthly', '2026-07-07 00:00:00', 'pending', NULL, NULL, '2026-06-17 14:19:35', '2026-06-17 14:19:35');

-- Table: `products`
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `sku` VARCHAR(255),
  `unit_price` DECIMAL(15,4) NOT NULL DEFAULT '0',
  `unit` VARCHAR(255) NOT NULL DEFAULT 'hour',
  `category` VARCHAR(255),
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `project_products`
DROP TABLE IF EXISTS `project_products`;
CREATE TABLE `project_products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` DECIMAL(15,4) NOT NULL DEFAULT '1',
  `unit_price` DECIMAL(15,4) NOT NULL,
  `notes` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `projects`
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `status` VARCHAR(255) NOT NULL DEFAULT 'active',
  `start_date` DATE,
  `end_date` DATE,
  `budget` DECIMAL(15,4),
  `notes` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` (`id`, `client_id`, `name`, `description`, `status`, `start_date`, `end_date`, `budget`, `notes`, `created_at`, `updated_at`) VALUES
('1', '1', 'call', NULL, 'active', NULL, NULL, NULL, NULL, '2026-06-17 14:25:20', '2026-06-17 14:25:20');

-- Table: `renewals`
DROP TABLE IF EXISTS `renewals`;
CREATE TABLE `renewals` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED,
  `description` VARCHAR(255) NOT NULL,
  `renewal_date` DATE NOT NULL,
  `amount` DECIMAL(15,4),
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `billing_cycle` VARCHAR(255) NOT NULL DEFAULT 'annually',
  `next_renewal_date` DATE,
  `notes` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `services`
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `default_price` DECIMAL(15,4) NOT NULL DEFAULT '0',
  `billing_cycle` VARCHAR(255) NOT NULL DEFAULT 'annually',
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `services` (`id`, `name`, `description`, `default_price`, `billing_cycle`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'Annual Accounts', NULL, '0', 'annually', '1', '2026-06-10 15:50:26', '2026-06-10 15:50:26'),
('2', 'Self Assessment', NULL, '0', 'annually', '1', '2026-06-10 16:10:56', '2026-06-10 16:11:52'),
('3', 'Payroll', NULL, '0', 'annually', '1', '2026-06-16 07:36:48', '2026-06-16 07:36:48'),
('4', 'VAT return', NULL, '0', 'annually', '1', '2026-06-17 08:40:42', '2026-06-17 08:40:42');

-- Table: `sessions`
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED,
  `ip_address` VARCHAR(255),
  `user_agent` TEXT,
  `payload` TEXT NOT NULL,
  `last_activity` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('orZhlDTKsdZrR6H6dOIiBKyANjV8MWD9tOAkby4R', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'eyJfdG9rZW4iOiI4VlAxd28wNUNYSUU0WGI5Q2V2QWlVZGtPU0NzbEFJTGhXa0J0cm44IiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2ZvY3VzLnRlc3RcL2FwaVwvY29tcGFuaWVzLWhvdXNlXC9zZWFyY2g/cT0xNTExNzk4NCIsInJvdXRlIjoiY29tcGFuaWVzLWhvdXNlLnNlYXJjaCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', '1781688306'),
('2nzgTUpmAm6DoV5hgMC6nQceJw8IwJTCNxHiQCRv', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'eyJfdG9rZW4iOiIwZkc2YlpuUkU0UlJCcmtZc3dnbUhtUTZMNDZpcVU4eXZKdWFCUjlpIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2ZvY3VzLnRlc3RcL3JlcG9ydHNcL3VwY29taW5nLWpvYnNcL3BkZlwvbGFuZHNjYXBlIiwicm91dGUiOiJyZXBvcnRzLnVwY29taW5nLWpvYnMucGRmIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9', '1781706524'),
('kPRoLrz9j9CzQ0ukAhbUgx351TtFVDuvTGkq3yfI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'eyJfdG9rZW4iOiJWMnBoMlVCTnZqMHRMNE5NN2VLZUF3SnVJbnpmdmRyQ0RMVndDeGdzIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvZm9jdXMudGVzdCJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvZm9jdXMudGVzdCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', '1781877193'),
('o4VatzEalaiiyXLYErbtYgBq4y4XLIrGA7nN3DUm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Herd/1.28.0 Chrome/120.0.6099.291 Electron/28.2.5 Safari/537.36', 'eyJfdG9rZW4iOiJqR2pkdnhWN3BCRzhsdzI1eFA5Y1VOdllOU0xONDdXU1pzamxUZmVoIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvZm9jdXMudGVzdFwvP2hlcmQ9cHJldmlldyJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvZm9jdXMudGVzdFwvP2hlcmQ9cHJldmlldyIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', '1781877207'),
('pPwkQFVEXGnPPjrxxEMkwM9w4qlDq0wzdyiXbS0S', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Herd/1.28.0 Chrome/120.0.6099.291 Electron/28.2.5 Safari/537.36', 'eyJfdG9rZW4iOiJTalRuMGRNS0hPT29tS09jckIxOGZOS2k2cGZXdlk5VlRRQ2VZeldHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2ZvY3VzLnRlc3RcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', '1781877207'),
('Y3Mbsf8toFqKGR6S5IrRR2oqOQbU3KGfwOlqcCYa', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'eyJfdG9rZW4iOiJnQ0Z3Z1ZTZ1RUUUpPaFdYWkJuaEk4SFFaWVc5SllrbzNRZ0VDbHk2IiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2ZvY3VzLnRlc3RcL2NsaWVudHMiLCJyb3V0ZSI6ImNsaWVudHMuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', '1781877622'),
('HYgClPnVcEgWzXYrQe5pTFM3jdqZXjEDBuVsyZ9O', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Herd/1.28.0 Chrome/120.0.6099.291 Electron/28.2.5 Safari/537.36', 'eyJfdG9rZW4iOiJZajFCcEF0NzluWVlXb3d5aVFXZDNKWUVnSmFyUEo3M2ZaZkNRU0ZkIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvZm9jdXMudGVzdFwvP2hlcmQ9cHJldmlldyJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvZm9jdXMudGVzdFwvP2hlcmQ9cHJldmlldyIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', '1781879055'),
('M3GONY6uhLDRfUimEfihP99ua5ICXQ8Ln6CXJK8U', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Herd/1.28.0 Chrome/120.0.6099.291 Electron/28.2.5 Safari/537.36', 'eyJfdG9rZW4iOiJvYllYZHRlWnVxQzJodEVTRm5RcUQyWEpwTnNkb2ZiZkhjTGF6Y3JrIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvZm9jdXMudGVzdFwvP2hlcmQ9cHJldmlldyJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvZm9jdXMudGVzdFwvP2hlcmQ9cHJldmlldyIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', '1781879055'),
('nlsTtf2JY8hBbJcYJGRz3TdR4JK2uSKhk2NTZ43q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Herd/1.28.0 Chrome/120.0.6099.291 Electron/28.2.5 Safari/537.36', 'eyJfdG9rZW4iOiIwZkw5U1kxWnZWbnJLbGRCN2NZUDlMeGtQRnhBMGJ2b0o1N3ZqVkZoIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2ZvY3VzLnRlc3RcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', '1781879055'),
('NLKDvcf7hpYsE7k33WSMkcmGys84xfa0KDBv5SZP', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJOOXQ5cDlIZUZnc2xCbjlCNWNvM2RsbnpiYXJXWnJkY2tkN0NmWDZ3IiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2ZvY3VzLnRlc3RcL2NsaWVudHMiLCJyb3V0ZSI6ImNsaWVudHMuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', '1781879122');

-- Table: `tasks`
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `priority` VARCHAR(255) NOT NULL DEFAULT 'medium',
  `due_date` DATE,
  `completed_at` DATETIME,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `is_urgent` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tasks` (`id`, `project_id`, `name`, `description`, `status`, `priority`, `due_date`, `completed_at`, `created_at`, `updated_at`, `is_urgent`) VALUES
('1', '1', 'Toms', NULL, 'pending', 'medium', NULL, NULL, '2026-06-17 14:25:36', '2026-06-17 14:25:36', '1');

-- Table: `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` DATETIME,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(255),
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `role` VARCHAR(255) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
('1', 'David Woods', 'david@waac.co.uk', NULL, '$2y$12$FnLkeOphf246SML9FhxmfevQ2wHaRkUJPsz2V1MJM6.rCfn0tbiYa', NULL, '2026-06-08 14:50:34', '2026-06-08 14:50:34', 'manager'),
('2', 'Luke Peek', 'luke@waac.co.uk', NULL, '$2y$12$0x/MZcjAERsZW60HeHVUoO46newMQPrPK9cl3h63wSU47AmuiDpQG', NULL, '2026-06-10 15:17:48', '2026-06-10 15:17:48', 'user');

SET FOREIGN_KEY_CHECKS=1;
