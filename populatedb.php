<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'initdb.php';
require_once 'config.php';
$faker = Faker\Factory::create();

$initSQL = <<<'SQL'
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
  name VARCHAR(255) NOT NULL UNIQUE ,
  first_name VARCHAR(255),
  last_name VARCHAR(255),
  address VARCHAR(255),
  city VARCHAR(255),
  zip_code VARCHAR(255),
  country VARCHAR(255),
  email VARCHAR(255) NOT NULL UNIQUE ,
  pass_hash VARCHAR(255)

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
  ADD CONSTRAINT rental_book_constraint FOREIGN KEY (book_id) REFERENCES book (id);

SQL;

$dbh->prepare($initSQL)->execute();

$addAuthorSQL = $dbh->prepare(<<<'SQL'
INSERT INTO book_catalog.author (first_name, last_name) VALUES (:firstName, :lastName);
SQL
);
$addBookSQL = $dbh->prepare(<<<'SQL'
INSERT INTO book_catalog.book (title, description, image_url) VALUES (:title, :description, :imageURL);
SQL
);
$linkBookSQL = $dbh->prepare(<<<'SQL'
INSERT INTO book_catalog.author_book (author_id, book_id) VALUES (:authorID, :bookID);
SQL
);
for ($i=1 ; $i <= 100; $i++ ) {
    $addAuthorSQL->execute(['firstName' => $faker->firstName, 'lastName' => $faker->lastName]);
}

for ($i=1 ; $i <= 500; $i++ ) {
    $addBookSQL->execute([
        'title' => ucwords($faker->bs),
        'description' => $faker->realText(),
        'imageURL' => $faker->imageUrl(480,480)
    ]);
    $linkBookSQL->execute(['bookID' => $i, 'authorID' => rand(1,100)]);
}

$addUserSQL  = $dbh->prepare(<<<'SQL'
INSERT INTO book_catalog.user (email, name, pass_hash) VALUES (:userEmail, :userName, :userPassword);
SQL
);
$addUserSQL->execute([
    'userEmail' => 'admin@localhost',
    'userName' => ADMIN_USERNAME,
    'userPassword' => crypt(ADMIN_PASSWORD, DB_SALT)
]);
