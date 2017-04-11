DROP DATABASE IF EXISTS book_catalog;
CREATE DATABASE IF NOT EXISTS book_catalog;
USE book_catalog;

CREATE TABLE IF NOT EXISTS author
(
  id INT PRIMARY KEY AUTO_INCREMENT,
  first_name VARCHAR(255) NOT NULL ,
  last_name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS book
(
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL ,
  description TEXT,
  image_url VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS author_book
(
  id INT PRIMARY KEY AUTO_INCREMENT,
  book_id INT NOT NULL ,
  author_id INT NOT NULL
);

CREATE TABLE IF NOT EXISTS user (
  id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255),
  password VARCHAR(255)

);

CREATE TABLE rental (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL ,
  book_id INT NOT NULL ,
  start_time DATETIME,
  end_time DATETIME
);

ALTER TABLE author_book
    ADD CONSTRAINT book_constraint FOREIGN KEY (book_id) REFERENCES book (id),
  ADD CONSTRAINT author_constraint FOREIGN KEY (author_id) REFERENCES author (id);

ALTER TABLE rental
    ADD CONSTRAINT rental_user_constraint FOREIGN KEY (user_id) REFERENCES user (id),
  ADD CONSTRAINT rental_book_constraint FOREIGN KEY (book_id) REFERENCES book (id)