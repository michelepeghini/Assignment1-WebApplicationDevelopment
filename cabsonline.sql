/* COS80001 - Web Application Development
 * Assignment 1 - cabsonline.sql
 * Student name: Michele Peghini
 * Student ID: 101940042
 *
 * This file contains MySQL statements necessary to create the 'cabsonline' database,
 * its relative tables 'customer' and 'booking'
 * its users ('default_user' and 'default_admin'), with their relative priviledges.  
 */

 /* 'cabsonline' database
  *
  */
CREATE DATABASE IF NOT EXISTS cabsonline;
USE cabsonline;

/* 
 * 'customer' table: stores data about customers
 */
CREATE TABLE IF NOT EXISTS customer (
    email varchar(50) NOT NULL, 
    customer_name varchar(50) NOT NULL,
    customer_pwd varchar(50) NOT NULL,
    phone_number varchar(10), -- assumed Australia only, therefore 10 digits
    PRIMARY KEY (email)
)ENGINE=InnoDB;

/* 
 * 'booking' table: stores details of a booking
 */ 
CREATE TABLE booking (
	booking_number int NOT NULL AUTO_INCREMENT,
    email_address varchar(50) NOT NULL,
    passenger_name varchar(50) NOT NULL,
    passenger_phone varchar(10) NOT NULL, -- assumed Australia only, therefore 10 digits
    pu_unit_no int DEFAULT NULL, -- not a required field, NULL by default
    pu_street_no int NOT NULL,
    pu_street_name varchar(50) NOT NULL,
    pu_suburb varchar(50) NOT NULL,
    destination_suburb varchar(50) NOT NULL, -- single field
    pu_date DATE NOT NULL, -- YYYY-MM-DD format
    pu_time TIME NOT NULL, -- HH:MM:SS format
    generated_date DATE NOT NULL, -- YYYY-MM-DD format
    generated_time TIME NOT NULL, -- HH:MM:SS format
    booking_status ENUM ('assigned', 'unassigned') NOT NULL DEFAULT 'unassigned', -- 2 possible values
    PRIMARY KEY (booking_number),
    FOREIGN KEY (email_address) REFERENCES customer(email)
)ENGINE=InnoDB;
