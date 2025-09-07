--
-- Create this database if it does not already exist`
--

CREATE DATABASE IF NOT EXISTS chromstack;

--
-- Use this database for the rest of the query executions`
--

USE chromstack;

--
-- Start query executions`
--

START TRANSACTION;

--
-- Create the admins table`
--

CREATE TABLE `admins` (
  `adminID` int NOT NULL AUTO_INCREMENT,
  `admin_profile` varchar(1000) NOT NULL,
  `fullname` varchar(1000) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `country` varchar(1000) NOT NULL,
  `account_password` varchar(255) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  `instagram_link` varchar(2000) NOT NULL,
  `tiktok_link` varchar(2000) NOT NULL,
  `twitter_link` varchar(2000) NOT NULL,
  `facebook_link` varchar(2000) NOT NULL,
  `account_number` bigint NOT NULL,
  `bank` varchar(2000) NOT NULL,
  `bank_code` varchar(100) NOT NULL,
   PRIMARY KEY  (`adminID`)
)  ENGINE = InnoDB;

--
-- Dumping data for admins table `
--

INSERT INTO `admins` VALUES 

(1, 'null', 'Okuzu Izuchukwu Augustine', 'izuchukwuokuzu@gmail.com', '+2348142978481', 'Nigeria', '$2y$10$iNytUrflVPfIqvTXBya5zOXhjAnLnncxVrYtoWLICLc1I5QpKag1W', 'May 15, 2023', 'null', 'null', 'null','null', 1234567890, 'null', 'null'),
(2, 'null', 'Okeke Chukwuebuka Augustine', 'okekeebuka928@gmail.com', '+2349026928911', 'Nigeria', '$2y$10$8hPxB/p3zzNdEJTvhf6rKu.dxNtCp4oXBwYNL78Cra16V5zENzWyW', 'May 15, 2023', 'null', 'null', 'null','null', 2370404934, 'Zenith Bank', 'null'),
(3, 'null', 'Lawal Babajide', 'jidelwl@gmail.com', '+2347038994103', 'Nigeria', '$2y$10$SHJvROmHxX4Jw8Jb.vbtzea0y5eFwy8qINscQo15jEpRhKDuaO2jK', 'May 15, 2023', 'null', 'null', 'null','null', 1234567890, 'null', 'null'),
(4, 'null', 'Emmanuel Okereke', 'emmanuelokereke321@gmail.com', '+2347038576596', 'Nigeria', '$2y$10$YhUT4klSh0WEBmuGjTyuAeUGSFNOtu2/l5ya9OOwPYrzFTu3VUpGe', 'May 15, 2023', 'null', 'null', 'null','null', 2370876532, 'null', 'null'),
(5, 'null', 'Wisdom Smart', 'mrwisdom8086@gmail.com', '+35677721883', 'Peru', '$2y$10$FvqIC2N/p72FZHX10JvacurSseat5OKMImBH/wiVSde3Y6ZJACDgy', 'May 15, 2023', 'null', 'null', 'null','null', 1234567890, 'null', 'null');

--
-- Login details are as follows:
--
--  Email                         Password
--  okuzuizuchukwu@gmail.com      izuchukwu
--  okekeebuka928@gmail.com       chukwuebuka
--  jidelawal@gmail.com           jide
--  emmanuelokereke321@gmail.com  emmanuel
--  mrwisdom8086@gmail.com        wisdom
--

--
-- Create the affiliates table`
--

CREATE TABLE `affiliates` (
  `affiliateID` int NOT NULL AUTO_INCREMENT,
  `affiliate_profile` varchar(1000) NOT NULL,
  `fullname` varchar(1000) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `country` varchar(1000) NOT NULL,
  `account_password` varchar(255) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  `affiliate_status` varchar(255) NOT NULL,
  `instagram_link` varchar(2000) NOT NULL,
  `tiktok_link` varchar(2000) NOT NULL,
  `twitter_link` varchar(2000) NOT NULL,
  `facebook_link` varchar(2000) NOT NULL,
  `account_number` bigint NOT NULL,
  `bank` varchar(2000) NOT NULL,
  `bank_code` varchar(100) NOT NULL,
  `recipient_code` varchar(100) NOT NULL,
   PRIMARY KEY  (`affiliateID`)
)  ENGINE = InnoDB;

--
-- Dumping data for affiliates table `
--

INSERT INTO `affiliates` VALUES 

(1, 'null', 'John Ade', 'john@gmail.com', '+2348142978481', 'Nigeria', '$2y$10$djZkZ3x/TdzCyQOT4UldKeN6tKQCzJbkg.KpsKxKdVT7DTwtXGq3W', 'Jun 03, 2023', 'Active', 'null', 'null', 'null','null', 1234567890, 'WEMA Bank', '022', 'null'),
(2, 'null', 'Chucks Okafor', 'chucks@gmail.com', '+2347038994103', 'Nigeria', '$2y$10$/1CHiA7Te60dIUdlk1mtnOehXuV0qIge6kSD63Pj71.UA8plin73q', 'Jun 03, 2023', 'Active', 'null', 'null', 'null', 'null', 0987654321, 'Access Bank', '046', 'null'),
(3, 'null', 'Bayo Hassan', 'hassan@gmail.com', '+2349026928911', 'Nigeria', '$2y$10$PeauFwzllGoCzMWicHleme3JIq5XxBXBKjdqiaER/3LWHTIQtexDi', 'Jun 03, 2023', 'Active', 'null', 'null', 'null', 'null', 234567891, 'GTBank', '046', 'null');

--
-- Login details are as follows:
--
--  Email                         Password
--  john@gmail.com                 john
--  chucks@gmail.com               chucks
--  hassan@gmail.com               hassan
--

--
-- Create the affiliates_temporary table`
--

CREATE TABLE `affiliates_temporary` (
  `affiliateID` int NOT NULL AUTO_INCREMENT,
  `affiliate_profile` varchar(1000) NOT NULL,
  `fullname` varchar(1000) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `country` varchar(1000) NOT NULL,
  `account_password` varchar(255) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  `affiliate_status` varchar(255) NOT NULL,
  `instagram_link` varchar(2000) NOT NULL,
  `tiktok_link` varchar(2000) NOT NULL,
  `twitter_link` varchar(2000) NOT NULL,
  `facebook_link` varchar(2000) NOT NULL,
  `account_number` bigint NOT NULL,
  `bank` varchar(2000) NOT NULL,
  `bank_code` varchar(100) NOT NULL,
  `recipient_code` varchar(100) NOT NULL,
   PRIMARY KEY  (`affiliateID`)
)  ENGINE = InnoDB;


--
-- Create the affiliate_program_course table`
--

CREATE TABLE `affiliate_program_course` (
  `courseID` int NOT NULL AUTO_INCREMENT,
  `course_title` varchar(255) NOT NULL,
  `course_description` varchar(5000) NOT NULL,
  `course_cover_page` varchar(255) NOT NULL,
  `course_type` varchar(255) NOT NULL,
  `course_status` varchar(255) NOT NULL,
  `course_authors` varchar(255) NOT NULL,
  `course_amount` varchar(255) NOT NULL,
  `admin_percentage` varchar(255) NOT NULL,
  `affiliate_percentage` varchar(255) NOT NULL,
  `uploaded_on` varchar(100) NOT NULL,
  `folder_path` varchar(100) NOT NULL,
  PRIMARY KEY  (`courseID`)
) ENGINE = InnoDB;

--
-- Create the affiliate_course_sales table`
--

CREATE TABLE `affiliate_course_sales` (
  `salesID` int NOT NULL AUTO_INCREMENT, 
  `sales_email` varchar(100) NOT NULL,
  `sales_contact` varchar(255) NOT NULL,
  `sales_amount` int NOT NULL,
  `sales_date` varchar(255) NOT NULL,
  `sales_time` varchar(255) NOT NULL,
  `sales_status` varchar(255) NOT NULL,
  `sales_txref` varchar(255) NOT NULL,
  `sales_type` varchar(255) NOT NULL,
  `sales_narration` varchar(255) NOT NULL, 
  `sellerID` int NOT NULL,
  `courseID` int NOT NULL,
   PRIMARY KEY  (`salesID`),
   FOREIGN KEY (`courseID`) REFERENCES `affiliate_program_course` (`courseID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

--
-- Create the affiliate_course_saless_backup table`
--

CREATE TABLE `affiliate_course_sales_backup` (
  `salesID` int NOT NULL AUTO_INCREMENT, 
  `sales_email` varchar(100) NOT NULL,
  `sales_contact` varchar(255) NOT NULL,
  `sales_amount` int NOT NULL,
  `sales_date` varchar(255) NOT NULL,
  `sales_time` varchar(255) NOT NULL,
  `sales_status` varchar(255) NOT NULL,
  `sales_txref` varchar(255) NOT NULL,
  `sales_type` varchar(255) NOT NULL,
  `sales_narration` varchar(255) NOT NULL, 
  `sellerID` int NOT NULL,
  `courseID` int NOT NULL,
  `affiliate_commission` varchar(10) NOT NULL,
   PRIMARY KEY  (`salesID`),
   FOREIGN KEY (`courseID`) REFERENCES `affiliate_program_course` (`courseID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

--
-- Create the affiliate_link_clicks table`
--

CREATE TABLE `affiliate_link_clicks` (
  `serialID` int NOT NULL AUTO_INCREMENT,
  `affiliate_email` varchar(100) NOT NULL,
  `total_clicks` int NOT NULL,
   PRIMARY KEY  (`serialID`)
) ENGINE = InnoDB;

--
-- Create the affiliates table`
--

CREATE TABLE `created_affiliates` (
  `affiliateID` int NOT NULL,
  `fullname` varchar(1000) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `country` varchar(1000) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
   PRIMARY KEY  (`affiliateID`)
)  ENGINE = InnoDB;

--
-- Create the chats table`
--

CREATE TABLE `chats` (
  `chatID` int NOT NULL AUTO_INCREMENT,
  `receiver_name` varchar(1000) NOT NULL,
  `sender_name` varchar(300) NOT NULL,
  `sender_type` varchar(500) NOT NULL,
  `receiver_type` varchar(500) NOT NULL,
   PRIMARY KEY  (`chatID`)
) ENGINE = InnoDB;

--
-- Create the chat_contents table for storing messages and files in chats`
--

CREATE TABLE `chat_contents` (
  `contentID` int NOT NULL AUTO_INCREMENT,
  `content_type` varchar(100) NOT NULL,
  `content_sender` varchar(2000) NOT NULL,
  `content_date` varchar(100) NOT NULL,
  `content_time` varchar(100) NOT NULL,
  `content_message` varchar(5000) NOT NULL,
  `content_filename` varchar(255) NOT NULL,
  `content_extension` varchar(20) NOT NULL,
  `chatID` int NOT NULL, 
   PRIMARY KEY  (`contentID`),
   FOREIGN KEY (`chatID`) REFERENCES `chats` (`chatID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

--
-- Create the chat_notifications table`
--

CREATE TABLE `chat_notifications` (
  `notificationID` int NOT NULL AUTO_INCREMENT,
  `action` varchar(2000) NOT NULL,
  `chatID` int NOT NULL,
  `created` varchar(50) NOT NULL,
  PRIMARY KEY  (`notificationID`),
   FOREIGN KEY (`chatID`) REFERENCES `chats` (`chatID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

--
-- Create the contest table`
--

CREATE TABLE `contest` (
  `contestID` int NOT NULL AUTO_INCREMENT, 
  `contest_title` varchar(2000) NOT NULL,
  `contest_description` varchar(5000) NOT NULL,
  `contest_start_date` varchar(100) NOT NULL,
  `contest_end_date` varchar(100) NOT NULL,
  `contest_status` varchar(255) NOT NULL,
  `courseID` int NOT NULL,
  `course_type` varchar(300) NOT NULL,
   PRIMARY KEY  (`contestID`)
) ENGINE = InnoDB;

--
-- Create the general_notifications table`
--

CREATE TABLE `general_notifications` (
  `notificationID` int NOT NULL AUTO_INCREMENT,
  `notification_title` varchar(1000) NOT NULL,
  `notification_details` varchar(1000) NOT NULL,
  `notification_type` varchar(255) NOT NULL,
  `notification_name` varchar(255) NOT NULL,
  `notification_receiver_email` varchar(255) NOT NULL,
  `notification_date` varchar(50) NOT NULL,
  `notification_status` varchar(50) NOT NULL,
  PRIMARY KEY  (`notificationID`)
) ENGINE = InnoDB;

--
-- Create the membership_payment table`
--

CREATE TABLE `membership_payment` (
  `paymentID` int NOT NULL AUTO_INCREMENT,
  `payment_email` varchar(1000) NOT NULL,
  `payment_type` varchar(1000) NOT NULL,
  `paid_amount` int NOT NULL,
  `payment_date` varchar(500) NOT NULL,
  `payment_status` varchar(500) NOT NULL,
  `payment_ref` varchar(500) NOT NULL,
   PRIMARY KEY  (`paymentID`)
) ENGINE = InnoDB;

--
-- Create the membership_commission_history table`
--

CREATE TABLE `membership_commission_history` (
  `membershipID` int NOT NULL AUTO_INCREMENT,
  `membership_email` varchar(1000) NOT NULL,
  `membership_type` varchar(1000) NOT NULL,
  `commission_amount` int NOT NULL,
   PRIMARY KEY  (`membershipID`)
) ENGINE = InnoDB;

--
-- Create the membership_earnings_history table`
--

CREATE TABLE `membership_earning_history` (
  `membershipID` int NOT NULL AUTO_INCREMENT,
  `membership_email` varchar(1000) NOT NULL,
  `membership_type` varchar(1000) NOT NULL,
  `earning_amount` int NOT NULL,
   PRIMARY KEY  (`membershipID`)
) ENGINE = InnoDB;

--
-- Create the mailbox table`
--

CREATE TABLE `mailbox` (
  `mailID` int NOT NULL AUTO_INCREMENT,
  `mail_type` varchar(100) NOT NULL,
  `mail_subject` varchar(1000) NOT NULL,
  `mail_sender` varchar(2000) NOT NULL,
  `mail_receiver` varchar(2000) NOT NULL,
  `mail_date` varchar(100) NOT NULL,
  `mail_time` varchar(100) NOT NULL,
  `mail_message` varchar(5000) NOT NULL,
  `mail_filename` varchar(255) NOT NULL,
  `mail_extension` varchar(20) NOT NULL,
   PRIMARY KEY  (`mailID`)
) ENGINE = InnoDB;

--
-- Create the mail_listing table`
--

CREATE TABLE `mail_listing` (
  `addressID` int NOT NULL AUTO_INCREMENT,
  `mail_address` varchar(1000) NOT NULL,
   PRIMARY KEY (`addressID`)
) ENGINE = InnoDB;

--
-- Create the purchased_courses table`
--

CREATE TABLE `purchased_courses` (
  `purchaseID` int NOT NULL AUTO_INCREMENT,
  `purchase_date` varchar(100) NOT NULL,
  `purchase_status` varchar(100) NOT NULL,
  `buyer_email` varchar(100) NOT NULL,
  `course_amount` int NOT NULL,
  `courseID` int NOT NULL,
  `course_type` varchar(300) NOT NULL,
  `trackingID` varchar(100) NOT NULL,
   PRIMARY KEY  (`purchaseID`)
)  ENGINE = InnoDB;

--
-- Create the reviews table`
--

CREATE TABLE `reviews` (
  `reviewID` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(300) NOT NULL,
  `profile` varchar(100) NOT NULL,
  `review_comment` varchar(5000) NOT NULL,
  `review_time` varchar(100) NOT NULL,
  `courseID` int NOT NULL,
  `course_type` varchar(300) NOT NULL,
   PRIMARY KEY  (`reviewID`)
) ENGINE = InnoDB;

--
-- Create the site_details table`
--

CREATE TABLE `site_details` (
  `detailID` int NOT NULL AUTO_INCREMENT,
  `site_logo` varchar(2000) NOT NULL,
  `site_name` varchar(2000) NOT NULL,
  `site_instagram_link` varchar(2000) NOT NULL,
  `site_twitter_link` varchar(2000) NOT NULL,
  `site_facebook_link` varchar(2000) NOT NULL,
  `site_tiktok_link` varchar(2000) NOT NULL,
  `site_telegram_link` varchar(2000) NOT NULL,
  `site_status` varchar(100) NOT NULL,
   PRIMARY KEY  (`detailID`)
) ENGINE = InnoDB;

--
-- Create the transaction_payments table`
--

CREATE TABLE `transaction_payments` (
  `paymentID` int NOT NULL AUTO_INCREMENT, 
  `payment_email` varchar(100) NOT NULL,
  `payment_amount` int NOT NULL,
  `payment_account` varchar(255) NOT NULL,
  `payment_bank` varchar(255) NOT NULL,
  `payment_date` varchar(255) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `payment_txref` varchar(255) NOT NULL,
   PRIMARY KEY (`paymentID`)
) ENGINE = InnoDB;

--
-- Create the transaction_payments_backup table`
--

CREATE TABLE `transaction_payments_backup` (
  `paymentID` int NOT NULL, 
  `payment_email` varchar(100) NOT NULL,
  `payment_amount` int NOT NULL,
  `payment_account` varchar(255) NOT NULL,
  `payment_bank` varchar(255) NOT NULL,
  `payment_date` varchar(255) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `payment_txref` varchar(255) NOT NULL,
   PRIMARY KEY (`paymentID`)
) ENGINE = InnoDB;

--
-- Create the transaction_batch_listing table`
--

CREATE TABLE `transaction_batch_listing` (
  `listID` int NOT NULL AUTO_INCREMENT,
  `transaction_title` varchar(2000) NOT NULL,
  `transaction_date` varchar(1000) NOT NULL,
  `batchID` int NOT NULL,
   PRIMARY KEY (`listID`)
) ENGINE = InnoDB;


--
-- Create the uploaded_courses table`
--

CREATE TABLE `uploaded_courses` (
  `courseID` int NOT NULL AUTO_INCREMENT,
  `course_title` varchar(255) NOT NULL,
  `course_description` varchar(5000) NOT NULL,
  `course_cover_page` varchar(255) NOT NULL,
  `course_type` varchar(255) NOT NULL,
  `course_status` varchar(255) NOT NULL,
  `course_authors` varchar(255) NOT NULL,
  `course_amount` varchar(255) NOT NULL,
  `admin_percentage` varchar(255) NOT NULL,
  `affiliate_percentage` varchar(255) NOT NULL,
  `vendor_percentage` varchar(255) NOT NULL,
  `uploaded_on` varchar(100) NOT NULL,
  `folder_path` varchar(100) NOT NULL,
   PRIMARY KEY  (`courseID`)
) ENGINE = InnoDB;

--
-- Create the uploaded_course_sales table`
--

CREATE TABLE `uploaded_course_sales` (
  `salesID` int NOT NULL AUTO_INCREMENT, 
  `sales_email` varchar(100) NOT NULL,
  `sales_contact` varchar(255) NOT NULL,
  `sales_amount` int NOT NULL,
  `sales_date` varchar(255) NOT NULL,
  `sales_time` varchar(255) NOT NULL,
  `sales_status` varchar(255) NOT NULL,
  `sales_txref` varchar(255) NOT NULL,
  `sales_type` varchar(255) NOT NULL,
  `sales_narration` varchar(255) NOT NULL,
  `sellerID` int NOT NULL,
  `courseID` int NOT NULL,
   PRIMARY KEY  (`salesID`),
   FOREIGN KEY (`courseID`) REFERENCES `uploaded_courses` (`courseID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

--
-- Create the uploaded_course_saless_backup table`
--

CREATE TABLE `uploaded_course_sales_backup` (
  `salesID` int NOT NULL AUTO_INCREMENT, 
  `sales_email` varchar(100) NOT NULL,
  `sales_contact` varchar(255) NOT NULL,
  `sales_amount` int NOT NULL,
  `sales_date` varchar(255) NOT NULL,
  `sales_time` varchar(255) NOT NULL,
  `sales_status` varchar(255) NOT NULL,
  `sales_txref` varchar(255) NOT NULL,
  `sales_type` varchar(255) NOT NULL,
  `sales_narration` varchar(255) NOT NULL,
  `sellerID` int NOT NULL,
  `courseID` int NOT NULL,
  `affiliate_commission` varchar(10) NOT NULL,
   PRIMARY KEY  (`salesID`),
   FOREIGN KEY (`courseID`) REFERENCES `uploaded_courses` (`courseID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

--
-- Create the users table`
--

CREATE TABLE `users` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `user_profile` varchar(1000) NOT NULL,
  `fullname` varchar(1000) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `country` varchar(1000) NOT NULL,
  `account_password` varchar(255) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  `user_status` varchar(255) NOT NULL,
   PRIMARY KEY  (`userID`)
) ENGINE = InnoDB;

--
-- Create the users_temporary table`
--

CREATE TABLE `users_temporary` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `user_profile` varchar(1000) NOT NULL,
  `fullname` varchar(1000) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `country` varchar(1000) NOT NULL,
  `account_password` varchar(255) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  `user_status` varchar(255) NOT NULL,
   PRIMARY KEY  (`userID`)
) ENGINE = InnoDB;


--
-- Create the vendors table`
--

CREATE TABLE `vendors` (
  `vendorID` int NOT NULL AUTO_INCREMENT,
  `vendor_profile` varchar(1000) NOT NULL,
  `fullname` varchar(1000) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `country` varchar(1000) NOT NULL,
  `account_password` varchar(255) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  `vendor_status` varchar(255) NOT NULL,
  `instagram_link` varchar(2000) NOT NULL,
  `tiktok_link` varchar(2000) NOT NULL,
  `twitter_link` varchar(2000) NOT NULL,
  `facebook_link` varchar(2000) NOT NULL,
  `account_number` bigint NOT NULL,
  `bank` varchar(2000) NOT NULL,
  `bank_code` varchar(100) NOT NULL,
   PRIMARY KEY  (`vendorID`)
)  ENGINE = InnoDB;

--
-- Dumping data for vendors table `
--

INSERT INTO `vendors` VALUES 

(1, 'null', 'Jerry Doe', 'jerry@gmail.com', '+18142978481', 'USA', '$2y$10$/rKKhxZuYxsMqLYIZQ7oY.UWYuu/CeIHD/Yz2JR8LEN/uyU6NLmZe', 'Jun 03, 2023', 'Active', 'null', 'null', 'null','null', 0902345678, 'Zenith Bank', '045'),
(2, 'null', 'James Gosling', 'james@gmail.com', '+2538994103', 'German', '$2y$10$0qxnntu6pGyivJNAhef8M.L.VGd.KIXDX3kG4zG2QPmCRX4Em5u2O', 'Jun 03, 2023', 'Active', 'null', 'null', 'null', 'null', 2351890865, 'UBA Bank', '066'),
(3, 'null', 'Elon Musk', 'musk@gmail.com', '+189026928911', 'South Africa', '$2y$10$GYZajnmkHomv35r5fOHVVu2ptqqu/emMgQtn5/2RRccYcNM/at.uu', 'Jun 03, 2023', 'Active', 'null', 'null', 'null', 'null', 2345671280, 'Sterling Bank', '053');

--
-- Login details are as follows:
--
--  Email                         Password
--  jerry@gmail.com                 jerry
--  james@gmail.com                 james
--  musk@gmail.com                  musk
--

--
-- Finish up the query executions and commit the changes to the server`
--

COMMIT;
