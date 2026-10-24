-- database/disasterready.sql
-- DisasterReady PH: Database Schema
-- Run this in phpMyAdmin or via MySQL CLI

CREATE DATABASE IF NOT EXISTS disasterready_ph
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE disasterready_ph;

-- ============================================================
-- TABLE: users
-- Stores both residents and admins
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('resident', 'admin') NOT NULL DEFAULT 'resident',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_email (email),
    INDEX idx_users_role (role)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: emergency_reports
-- Submitted by residents; managed by residents and admins
-- ============================================================
CREATE TABLE emergency_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    category ENUM('Flood', 'Earthquake', 'Typhoon', 'Fire', 'Landslide', 'Other') NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    status ENUM('Pending', 'Verified', 'In Progress', 'Resolved') NOT NULL DEFAULT 'Pending',
    reported_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reports_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    INDEX idx_reports_status (status),
    INDEX idx_reports_category (category),
    INDEX idx_reports_user (user_id),
    INDEX idx_reports_reported_at (reported_at)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: evacuation_centers
-- Managed by admins; displayed on the map
-- ============================================================
CREATE TABLE evacuation_centers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    address VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    contact_person VARCHAR(100) NULL,
    contact_number VARCHAR(20) NULL,
    capacity INT NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_evac_active (is_active),
    INDEX idx_evac_name (name)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: announcements
-- Created by admins; shown to residents
-- ============================================================
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    is_published BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_announcements_admin
        FOREIGN KEY (admin_id) REFERENCES users(id)
        ON DELETE CASCADE,
    INDEX idx_announcements_published (is_published),
    INDEX idx_announcements_created (created_at)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: audit_logs
-- Optional but useful for tracking admin actions
-- ============================================================
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NULL,
    entity_id INT NULL,
    details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL,
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB;