CREATE TABLE admin_user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    email VARCHAR(256) NOT NULL,
    hash_password VARCHAR(128) NOT NULL,
    role INT NOT NULL,
    is_active BOOLEAN NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE banner (
    banner_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    image_url VARCHAR(256) NOT NULL,
    link VARCHAR(512),
    is_visible BOOLEAN NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE manufacturer (
    manufacturer_id INT AUTO_INCREMENT PRIMARY KEY,
    manufacturer_name VARCHAR(100) NOT NULL,
    description VARCHAR(1024),
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE origin (
    origin_id INT AUTO_INCREMENT PRIMARY KEY,
    country VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE product (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(256) NOT NULL,
    description TEXT NOT NULL,
    original_price DECIMAL(12,2) NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    is_visible BOOLEAN NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    origin_id INT NOT NULL,
    manufacturer_id INT NOT NULL,
    FOREIGN KEY (origin_id) REFERENCES origin(origin_id) ON DELETE CASCADE,
    FOREIGN KEY (manufacturer_id) REFERENCES manufacturer(manufacturer_id) ON DELETE CASCADE
);

CREATE TABLE product_flavor (
    product_flavor_id INT AUTO_INCREMENT PRIMARY KEY,
    flavor_name VARCHAR(30) NOT NULL,
    stock_quantity INT NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE
);

CREATE TABLE product_image (
    product_image_id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(256) NOT NULL,
    is_main BOOLEAN NOT NULL DEFAULT FALSE,
    product_id INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE
);

CREATE TABLE product_category (
    product_id INT,
    category_id INT,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category(category_id) ON DELETE CASCADE
);

CREATE TABLE `order` (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_first_name VARCHAR(20) NOT NULL,
    customer_last_name VARCHAR(30) NOT NULL,
    company_name VARCHAR(100),
    country VARCHAR(30) NOT NULL,
    address VARCHAR(256) NOT NULL,
    address2 VARCHAR(256),
    postal_code VARCHAR(20),
    city VARCHAR(30) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(256) NOT NULL,
    order_note VARCHAR(512),
    payment_method VARCHAR(50) NOT NULL,
    shipping_price DECIMAL(12,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    is_paid BOOLEAN NOT NULL,
    paid_at DATETIME,
    is_delivered BOOLEAN NOT NULL,
    delivered_at DATETIME,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE order_item (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    order_id INT NOT NULL,
    product_flavor_id INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES `order`(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_flavor_id) REFERENCES product_flavor(product_flavor_id) ON DELETE CASCADE
);
