-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2024 at 11:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `damsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `App_Id` int(11) NOT NULL,
  `Patient_Id` int(11) NOT NULL,
  `App_Symptom` varchar(255) DEFAULT NULL,
  `App_Comment` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `Doctor_Id` int(4) NOT NULL,
  `Doctor_password` varchar(64) DEFAULT NULL,
  `Doctor_FirstName` varchar(50) DEFAULT NULL,
  `Doctor_LastName` varchar(50) DEFAULT NULL,
  `Doctor_Address` varchar(255) DEFAULT NULL,
  `Doctor_specialities` varchar(255) DEFAULT NULL,
  `Doctor_Phone` varchar(15) DEFAULT NULL,
  `Doctor_Email` varchar(256) DEFAULT NULL,
  `Doctor_DOB` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`Doctor_Id`, `Doctor_password`, `Doctor_FirstName`, `Doctor_LastName`, `Doctor_Address`, `Doctor_specialities`, `Doctor_Phone`, `Doctor_Email`, `Doctor_DOB`) VALUES
(1010, 'doctor1010', 'Manu', 'Bora', 'F,10/4,Golf Course Rd, DFL Phase 1, Sector 27, Gurugram, Haryana, 122001', 'Ortho Surgeon: ACL & Sports injuries', '9513631396', 'orthosportin@gmail.com', '1985-02-22');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `Patient_Id` int(11) NOT NULL,
  `password` varchar(64) DEFAULT NULL,
  `Patient_FirstName` varchar(20) NOT NULL,
  `Patient_MiddleName` varchar(20) DEFAULT NULL,
  `Patient_LastName` varchar(20) DEFAULT NULL,
  `Patient_DOB` date DEFAULT NULL,
  `Patient_Gender` varchar(10) DEFAULT NULL,
  `Patient_Address` varchar(255) DEFAULT NULL,
  `Patient_Phone` varchar(15) DEFAULT NULL,
  `Patient_Email` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `profile_img` varchar(255) NOT NULL,
  `Patient_Marital_Status` varchar(20) NOT NULL,
  `security_question` varchar(255) NOT NULL,
  `security_answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`Patient_Id`, `password`, `Patient_FirstName`, `Patient_MiddleName`, `Patient_LastName`, `Patient_DOB`, `Patient_Gender`, `Patient_Address`, `Patient_Phone`, `Patient_Email`, `verified`, `profile_img`, `Patient_Marital_Status`, `security_question`, `security_answer`) VALUES
(1, '$2y$10$vuDiHnozmJwROaoY1ZDal.TSWwVT6pLRdsR.ONwsi8pwvST1VHjvK', 'admin', 'admin', 'admin', '2024-01-01', 'Male', 'admin', '9999999999', 'admin@gmail.com', 0, 'dp/1.jpg', 'Single', 'What is your mother\'s maiden name?', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`App_Id`),
  ADD KEY `fk_appointments_patient` (`Patient_Id`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`Doctor_Id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`Patient_Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `App_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `Doctor_Id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1011;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `Patient_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointments_patient` FOREIGN KEY (`Patient_Id`) REFERENCES `patients` (`Patient_Id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
