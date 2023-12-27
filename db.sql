CREATE TABLE `freight`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `cost` DECIMAL(8, 2) NOT NULL,
    `fx` INT NOT NULL,
    `fx_rate` DECIMAL(8, 5) NOT NULL,
    `forworder` INT NOT NULL,
    `vendors_num` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `by` INT NOT NULL,
    `company_id` INT NOT NULL
);
CREATE TABLE `purchase_item`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `purchase_id` INT NOT NULL,
    `item` INT NOT NULL,
    `qty` INT NOT NULL,
    `price` DECIMAL(8, 2) NOT NULL,
    `fx` INT NOT NULL,
    `fx_rate` DECIMAL(8, 5) NOT NULL DEFAULT '1'
);
CREATE TABLE `delivary_address`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `city` VARCHAR(255) NULL,
    `country` VARCHAR(255) NULL,
    `zip` VARCHAR(255) NULL,
    `coordinates` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `by` INT NOT NULL
);
CREATE TABLE `status`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `by` INT NOT NULL
);
CREATE TABLE `files`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `size` INT NOT NULL,
    `path` VARCHAR(255) NOT NULL,
    `date` DATE NOT NULL,
    `by` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    `update_history` TEXT NULL,
    `tags` VARCHAR(255) NULL,
    `contact_id` INT NOT NULL,
    `catagory` VARCHAR(255) NOT NULL,
    `purchase_id` INT NULL,
    `sale_id` INT NULL,
    `freight_id` INT NULL,
    `other_id` TEXT NULL,
    `company_id` INT NOT NULL
);
CREATE TABLE `forworders`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `contact` INT NOT NULL
);
CREATE TABLE `brand`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `catagory` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `by` INT NOT NULL
);
CREATE TABLE `freight_item`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `freight_id` INT NOT NULL,
    `purchase_id` INT NULL,
    `sale_id` INT NULL,
    `inventory_id` INT NULL,
    `cost` DECIMAL(8, 2) NOT NULL,
    `other_id` TEXT NULL
);
CREATE TABLE `sale_item`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `item` INT NOT NULL,
    `sale_id` INT NOT NULL,
    `qty` INT NOT NULL,
    `price` INT NOT NULL,
    `fx` INT NOT NULL,
    `fx_rate` DECIMAL(8, 5) NOT NULL DEFAULT '1',
    `inventory` INT NOT NULL
);
CREATE TABLE `company`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `image` VARCHAR(255) NULL,
    `address` VARCHAR(255) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `country` VARCHAR(255) NOT NULL,
    `zip` VARCHAR(255) NOT NULL,
    `short_name` VARCHAR(255) NOT NULL
);
CREATE TABLE `purchases`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `po_num` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `purchase_date` DATE NOT NULL,
    `by` INT NOT NULL,
    `status` INT NULL,
    `vendor` INT NOT NULL,
    `company_id` INT NOT NULL,
    `vendors_num` VARCHAR(255) NULL
);
ALTER TABLE
    `purchases` ADD UNIQUE `purchases_po_num_unique`(`po_num`);
CREATE TABLE `items`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `color` VARCHAR(255) NULL,
    `brand` INT NULL,
    `shot_name` VARCHAR(255) NOT NULL,
    `ean` INT NULL,
    `pn` VARCHAR(255) NULL,
    `description` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `by` INT NOT NULL
);
ALTER TABLE
    `items` ADD INDEX `items_name_index`(`name`);
CREATE TABLE `temp_files`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `size` INT NOT NULL,
    `date_uploaded` DATE NOT NULL,
    `temp_id` VARCHAR(255) NOT NULL,
    `by` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE `catagorys`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `by` INT NOT NULL
);
CREATE TABLE `letters`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `total_contacts` INT NULL,
    `total_files` INT NULL,
    `total_size` INT NULL,
    `sub_contact_ids` TEXT NULL,
    `letter` VARCHAR(11) NOT NULL,
    `company_id` INT NOT NULL,
    `letter_check` VARCHAR(11) NOT NULL
);
ALTER TABLE
    `letters` ADD UNIQUE `letters_letter_check_unique`(`letter_check`);
CREATE TABLE `inventory_item`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `location` INT NOT NULL,
    `item` INT NOT NULL,
    `purchase` INT NOT NULL,
    `qty` INT NOT NULL,
    `qty_remaning` INT NOT NULL,
    `other_costs(needed?)` DECIMAL(8, 2) NOT NULL,
    `cost` DECIMAL(8, 2) NOT NULL,
    `fx` INT NOT NULL,
    `fx_rate` DECIMAL(8, 5) NOT NULL DEFAULT '1',
    `status` INT NOT NULL,
    `notes` TEXT NULL,
    `sold` TINYINT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE `contacts`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `city` VARCHAR(255) NULL,
    `country` VARCHAR(255) NULL,
    `zip` VARCHAR(255) NULL,
    `vat` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `tel` VARCHAR(255) NULL,
    `contact` VARCHAR(255) NULL,
    `letter_id` INT NOT NULL,
    `catagorys` VARCHAR(255) NULL,
    `total_files` INT NULL,
    `company_id` INT NOT NULL,
    `currency` VARCHAR(255) NOT NULL DEFAULT 'USD',
    `url` VARCHAR(255) NULL,
    `coordinates` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `by` INT NOT NULL,
    `updated_at` TIMESTAMP NULL,
    `type` VARCHAR(255) NOT NULL
);
ALTER TABLE
    `contacts` ADD INDEX `contacts_name_index`(`name`);
CREATE TABLE `customers`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `contact` INT NOT NULL
);
CREATE TABLE `vendors`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `contact` INT NOT NULL
);
CREATE TABLE `locations`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `city` VARCHAR(255) NULL,
    `country` VARCHAR(255) NULL,
    `zip` INT NULL,
    `coordinates` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `by` INT NOT NULL
);
CREATE TABLE `settings`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `user_id` INT NOT NULL,
    `per_page` INT NULL,
    `logo` VARCHAR(255) NULL,
    `alert_email` VARCHAR(255) NULL,
    `alerts_enabled` TINYINT NULL,
    `alert_interval` INT NULL,
    `alert_threshold` INT NULL,
    `is_admin` TINYINT NOT NULL,
    `date_display_format` VARCHAR(255) NULL,
    `time_display_format` VARCHAR(255) NULL,
    `custom_forgot_pass_url` VARCHAR(255) NULL,
    `show_alerts_in_menu` TINYINT NULL,
    `dashboard_message` TEXT NULL,
    `show_images_in_email` TINYINT NULL,
    `admin_cc_email` CHAR(255) NULL
);
CREATE TABLE `sales`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `pi_num` VARCHAR(255) NOT NULL,
    `inv_num` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `sale_date` DATE NOT NULL,
    `invoice_date` DATE NOT NULL,
    `customer` INT NOT NULL,
    `by` INT NOT NULL,
    `company_id` INT NOT NULL,
    `customers_num` VARCHAR(255) NULL
);
ALTER TABLE
    `sales` ADD UNIQUE `sales_pi_num_unique`(`pi_num`);
ALTER TABLE
    `sales` ADD UNIQUE `sales_inv_num_unique`(`inv_num`);
CREATE TABLE `fx`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `currency` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `by` INT NOT NULL
);
CREATE TABLE `users`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NULL,
    `permissions` TEXT NULL,
    `activated` TINYINT NOT NULL,
    `activated_at` TIMESTAMP NULL,
    `last_login` TIMESTAMP NULL,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    `phone` VARCHAR(255) NULL,
    `jobtitle` VARCHAR(255) NULL,
    `avatar` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `preferences` TEXT NULL,
    `auth_level` TINYINT NOT NULL
);

CREATE TABLE `server`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `side_nav` TEXT NULL,
    `top_nav` TEXT NULL,
    `metadata` TEXT NULL
);
CREATE TABLE `data_tables`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `display_name` VARCHAR(255) NOT NULL,
    `headers` TEXT NULL,
    `columns` TEXT NULL,
    `active` TINYINT,
    `order` int,
    `config` TEXT NULL,
    `uris` TEXT NULL,
    `modal` VARCHAR(255) null,
    `validation`TEXT NULL,
    `page` VARCHAR(255) null,
    `auth` INT NULL,
);

ALTER TABLE
    `purchases` ADD CONSTRAINT `purchases_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `inventory_item` ADD CONSTRAINT `inventory_item_item_foreign` FOREIGN KEY(`item`) REFERENCES `items`(`id`);
ALTER TABLE
    `items` ADD CONSTRAINT `items_brand_foreign` FOREIGN KEY(`brand`) REFERENCES `brand`(`id`);
ALTER TABLE
    `sale_item` ADD CONSTRAINT `sale_item_sale_id_foreign` FOREIGN KEY(`sale_id`) REFERENCES `sales`(`id`);
ALTER TABLE
    `locations` ADD CONSTRAINT `locations_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `settings` ADD CONSTRAINT `settings_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `catagorys` ADD CONSTRAINT `catagorys_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `purchase_item` ADD CONSTRAINT `purchase_item_fx_foreign` FOREIGN KEY(`fx`) REFERENCES `fx`(`id`);
ALTER TABLE
    `sales` ADD CONSTRAINT `sales_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `brand` ADD CONSTRAINT `brand_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `sale_item` ADD CONSTRAINT `sale_item_fx_foreign` FOREIGN KEY(`fx`) REFERENCES `fx`(`id`);
ALTER TABLE
    `purchases` ADD CONSTRAINT `purchases_company_id_foreign` FOREIGN KEY(`company_id`) REFERENCES `company`(`id`);
ALTER TABLE
    `sales` ADD CONSTRAINT `sales_customer_foreign` FOREIGN KEY(`customer`) REFERENCES `customers`(`id`);
ALTER TABLE
    `fx` ADD CONSTRAINT `fx_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `freight` ADD CONSTRAINT `freight_fx_foreign` FOREIGN KEY(`fx`) REFERENCES `fx`(`id`);
ALTER TABLE
    `freight_item` ADD CONSTRAINT `freight_item_sale_id_foreign` FOREIGN KEY(`sale_id`) REFERENCES `sales`(`id`);
ALTER TABLE
    `inventory_item` ADD CONSTRAINT `inventory_item_location_foreign` FOREIGN KEY(`location`) REFERENCES `locations`(`id`);
ALTER TABLE
    `files` ADD CONSTRAINT `files_purchase_id_foreign` FOREIGN KEY(`purchase_id`) REFERENCES `purchases`(`id`);
ALTER TABLE
    `files` ADD CONSTRAINT `files_company_id_foreign` FOREIGN KEY(`company_id`) REFERENCES `company`(`id`);
ALTER TABLE
    `items` ADD CONSTRAINT `items_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `temp_files` ADD CONSTRAINT `temp_files_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `forworders` ADD CONSTRAINT `forworders_contact_foreign` FOREIGN KEY(`contact`) REFERENCES `contacts`(`id`);
ALTER TABLE
    `freight_item` ADD CONSTRAINT `freight_item_inventory_id_foreign` FOREIGN KEY(`inventory_id`) REFERENCES `inventory_item`(`id`);
ALTER TABLE
    `purchase_item` ADD CONSTRAINT `purchase_item_purchase_id_foreign` FOREIGN KEY(`purchase_id`) REFERENCES `purchases`(`id`);
ALTER TABLE
    `files` ADD CONSTRAINT `files_contact_id_foreign` FOREIGN KEY(`contact_id`) REFERENCES `contacts`(`id`);
ALTER TABLE
    `vendors` ADD CONSTRAINT `vendors_contact_foreign` FOREIGN KEY(`contact`) REFERENCES `contacts`(`id`);
ALTER TABLE
    `purchase_item` ADD CONSTRAINT `purchase_item_item_foreign` FOREIGN KEY(`item`) REFERENCES `items`(`id`);
ALTER TABLE
    `sales` ADD CONSTRAINT `sales_company_id_foreign` FOREIGN KEY(`company_id`) REFERENCES `company`(`id`);
ALTER TABLE
    `customers` ADD CONSTRAINT `customers_contact_foreign` FOREIGN KEY(`contact`) REFERENCES `contacts`(`id`);
ALTER TABLE
    `files` ADD CONSTRAINT `files_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `freight` ADD CONSTRAINT `freight_forworder_foreign` FOREIGN KEY(`forworder`) REFERENCES `forworders`(`id`);
ALTER TABLE
    `status` ADD CONSTRAINT `status_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `delivary_address` ADD CONSTRAINT `delivary_address_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `contacts` ADD CONSTRAINT `contacts_company_id_foreign` FOREIGN KEY(`company_id`) REFERENCES `company`(`id`);
ALTER TABLE
    `contacts` ADD CONSTRAINT `contacts_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `freight_item` ADD CONSTRAINT `freight_item_purchase_id_foreign` FOREIGN KEY(`purchase_id`) REFERENCES `purchases`(`id`);
ALTER TABLE
    `purchases` ADD CONSTRAINT `purchases_vendor_foreign` FOREIGN KEY(`vendor`) REFERENCES `vendors`(`id`);
ALTER TABLE
    `sale_item` ADD CONSTRAINT `sale_item_inventory_foreign` FOREIGN KEY(`inventory`) REFERENCES `inventory_item`(`id`);
ALTER TABLE
    `freight` ADD CONSTRAINT `freight_by_foreign` FOREIGN KEY(`by`) REFERENCES `users`(`id`);
ALTER TABLE
    `sale_item` ADD CONSTRAINT `sale_item_item_foreign` FOREIGN KEY(`item`) REFERENCES `items`(`id`);
ALTER TABLE
    `inventory_item` ADD CONSTRAINT `inventory_item_purchase_foreign` FOREIGN KEY(`purchase`) REFERENCES `purchases`(`id`);
ALTER TABLE
    `freight_item` ADD CONSTRAINT `freight_item_freight_id_foreign` FOREIGN KEY(`freight_id`) REFERENCES `freight`(`id`);
ALTER TABLE
    `files` ADD CONSTRAINT `files_freight_id_foreign` FOREIGN KEY(`freight_id`) REFERENCES `freight`(`id`);
ALTER TABLE
    `contacts` ADD CONSTRAINT `contacts_letter_id_foreign` FOREIGN KEY(`letter_id`) REFERENCES `letters`(`id`);
ALTER TABLE
    `files` ADD CONSTRAINT `files_sale_id_foreign` FOREIGN KEY(`sale_id`) REFERENCES `sales`(`id`);
ALTER TABLE
    `inventory_item` ADD CONSTRAINT `inventory_item_status_foreign` FOREIGN KEY(`status`) REFERENCES `status`(`id`);
ALTER TABLE
    `delivary_address` ADD CONSTRAINT `delivary_address_customer_id_foreign` FOREIGN KEY(`customer_id`) REFERENCES `customers`(`id`);
ALTER TABLE
    `inventory_item` ADD CONSTRAINT `inventory_item_fx_foreign` FOREIGN KEY(`fx`) REFERENCES `fx`(`id`);
ALTER TABLE
    `letters` ADD CONSTRAINT `letters_company_id_foreign` FOREIGN KEY(`company_id`) REFERENCES `company`(`id`);
ALTER TABLE
    `purchases` ADD CONSTRAINT `purchases_status_foreign` FOREIGN KEY(`status`) REFERENCES `status`(`id`);
    ALTER TABLE
    `freight` ADD CONSTRAINT `freight_company_id_foreign` FOREIGN KEY(`company_id`) REFERENCES `company`(`id`);