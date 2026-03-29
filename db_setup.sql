--CREATE A DATABASE & ADMIN-USER TO ADMIN A DB--
CREATE DATABASE IF NOT EXISTS modelcars;
CREATE USER 'admin01'@'localhost' IDENTIFIED BY 'password';
--GRANT ALL PRIVILEGES  TO AN ADMIN--
GRANT ALL PRIVILEGES ON modelcars.* TO 'admin01'@'localhost';

CREATE TABLE IF NOT EXISTS brand (
  brand_id INT AUTO_INCREMENT,
  brand_name VARCHAR(30) NOT NULL,
  PRIMARY KEY (brand_id));

CREATE TABLE IF NOT EXISTS scale (
  scale_id INT AUTO_INCREMENT,
  scale_name VARCHAR(30) NOT NULL,
  PRIMARY KEY (scale_id));

CREATE TABLE collection (
  collection_id INT AUTO_INCREMENT,
  category_name VARCHAR(30) NOT NULL,
  PRIMARY KEY (collection_id));

CREATE TABLE model (
  model_id INT AUTO_INCREMENT,
  model_name VARCHAR(50) NOT NULL,
  collection_id INT,
  brand_id INT,
  scale_id INT,
  description TEXT,
  PRIMARY KEY (model_id),
  FOREIGN KEY (brand_id)
      REFERENCES brand(brand_id)
      ON DELETE CASCADE,
  FOREIGN KEY (scale_id)
      REFERENCES scale(scale_id),
  FOREIGN KEY (collection_id)
      REFERENCES collection(collection_id)
      ON DELETE CASCADE);

CREATE TABLE users (
  user_id INT AUTO_INCREMENT,
  username VARCHAR(16) UNIQUE NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  PRIMARY KEY (user_id));

CREATE TABLE variant (
  variant_id INT AUTO_INCREMENT,
  model_id INT,
  variant VARCHAR(20),
  sku VARCHAR(12) UNIQUE,
  price DECIMAL(5,2),
  
  stock INT,
  imagepath VARCHAR(100),
  PRIMARY KEY (variant_id),
  FOREIGN KEY (model_id)
      REFERENCES model(model_id)
      ON DELETE CASCADE);

