# Inventory Management System

A robust, object-oriented PHP and MySQL web application designed to manage product inventories, track stock levels, and safely handle database transactions. Built as part of the HSYD300-1 module coursework.

## Features

* **Object-Oriented Architecture:** Core logic encapsulated within clean, reusable PHP classes (`InventoryManager.php`).
* **Secure Database Operations:** Uses PHP Data Objects (PDO) with prepared statements to prevent SQL injection vulnerabilities.
* **Transaction Safety:** Implements MySQL database transactions to ensure data consistency during inventory updates and inserts.
* **Duplicate Prevention:** Automatic checks to prevent duplicate product records from being added to the database.
* **Data Export & Logging:** Includes functionality to export inventory records to CSV format (`export.php`) and maintain structured backup logs (`backup.csv`).

## Project Structure

```text
├── InventoryManager.php  # Core business logic and database transaction methods
├── database.php          # PDO database connection configuration
├── index.php             # Main user interface for inventory management
├── export.php            # Export script for generating inventory reports
└── backup.csv            # Backup log output file

