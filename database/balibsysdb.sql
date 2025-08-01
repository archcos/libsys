-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 09:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `balibsysdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblauthor`
--

CREATE TABLE `tblauthor` (
  `authorId` int(11) NOT NULL,
  `firstName` varchar(256) NOT NULL,
  `lastName` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblbooks`
--

CREATE TABLE `tblbooks` (
  `bookId` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `dateAdded` timestamp NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) NOT NULL,
  `authorId` int(11) DEFAULT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `callNum` varchar(11) NOT NULL,
  `accessionNum` varchar(5) NOT NULL,
  `barcodeNum` varchar(11) NOT NULL,
  `publisher` varchar(30) NOT NULL,
  `publishedDate` date DEFAULT NULL,
  `edition` varchar(20) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblborrowers`
--

CREATE TABLE `tblborrowers` (
  `idNumber` int(15) NOT NULL,
  `borrowerType` varchar(10) NOT NULL,
  `dateRegistered` timestamp NOT NULL DEFAULT current_timestamp(),
  `libraryId` int(11) NOT NULL,
  `surName` varchar(20) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `middleName` varchar(20) NOT NULL,
  `emailAddress` varchar(40) NOT NULL,
  `course` int(11) DEFAULT NULL,
  `year` int(1) DEFAULT NULL,
  `position` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','','') NOT NULL,
  `birthDate` varchar(15) NOT NULL,
  `homeAddress` varchar(100) NOT NULL,
  `remarks` varchar(20) NOT NULL,
  `receipt` enum('No','Yes','','') NOT NULL,
  `timeReceived` datetime DEFAULT NULL,
  `reason` varchar(200) NOT NULL,
  `pickupDate` date DEFAULT current_timestamp(),
  `librarian` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `categoryId` int(11) NOT NULL,
  `categoryName` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcourses`
--

CREATE TABLE `tblcourses` (
  `courseId` int(11) NOT NULL,
  `level` enum('Undergraduate','Postgraduate','Doctoral','') NOT NULL,
  `courseName` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcourses`
--

INSERT INTO `tblcourses` (`courseId`, `level`, `courseName`) VALUES
(0, '', 'N/A');

-- --------------------------------------------------------

--
-- Table structure for table `tblnotifications`
--

CREATE TABLE `tblnotifications` (
  `notificationId` int(11) NOT NULL,
  `borrowerId` int(11) DEFAULT NULL,
  `bookId` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `type` enum('borrow','return','','') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `remarks` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblpenalties`
--

CREATE TABLE `tblpenalties` (
  `penaltyId` int(11) NOT NULL,
  `borrowerId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  `penalty` enum('Medium','Severe','Normal') NOT NULL,
  `cost` float NOT NULL,
  `paid` enum('No','Yes','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblreference`
--

CREATE TABLE `tblreference` (
  `referenceId` int(11) NOT NULL,
  `borrowerId` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `author` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL,
  `category` varchar(30) NOT NULL,
  `callNumber` varchar(30) NOT NULL,
  `subLocation` varchar(30) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblreturnborrow`
--

CREATE TABLE `tblreturnborrow` (
  `borrowId` int(11) NOT NULL,
  `borrowedDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `returnDate` date DEFAULT NULL,
  `borrowerId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  `librarianName` varchar(50) NOT NULL,
  `returned` enum('No','Yes','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `userId` int(11) NOT NULL,
  `accountType` enum('Librarian','Admin','','') NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL,
  `firstName` varchar(20) NOT NULL,
  `lastName` varchar(20) NOT NULL,
  `lastLogin` timestamp NOT NULL DEFAULT current_timestamp(),
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`userId`, `accountType`, `username`, `password`, `firstName`, `lastName`, `lastLogin`, `dateCreated`) VALUES
(1, 'Admin', 'rain', 'fbec17cb2fcbbd1c659b252230b48826fc563788', 'Hottest', 'Person', '2025-05-13 03:20:05', '2024-11-25 02:40:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblauthor`
--
ALTER TABLE `tblauthor`
  ADD PRIMARY KEY (`authorId`);

--
-- Indexes for table `tblbooks`
--
ALTER TABLE `tblbooks`
  ADD PRIMARY KEY (`bookId`),
  ADD KEY `fk_author` (`authorId`),
  ADD KEY `fk_category` (`categoryId`);

--
-- Indexes for table `tblborrowers`
--
ALTER TABLE `tblborrowers`
  ADD PRIMARY KEY (`idNumber`),
  ADD UNIQUE KEY `libraryId` (`libraryId`),
  ADD KEY `fk_course` (`course`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`categoryId`);

--
-- Indexes for table `tblcourses`
--
ALTER TABLE `tblcourses`
  ADD PRIMARY KEY (`courseId`);

--
-- Indexes for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  ADD PRIMARY KEY (`notificationId`),
  ADD KEY `borrowerId` (`borrowerId`),
  ADD KEY `fk_bookId` (`bookId`);

--
-- Indexes for table `tblpenalties`
--
ALTER TABLE `tblpenalties`
  ADD PRIMARY KEY (`penaltyId`);

--
-- Indexes for table `tblreference`
--
ALTER TABLE `tblreference`
  ADD PRIMARY KEY (`referenceId`),
  ADD KEY `fk_borrowerId` (`borrowerId`);

--
-- Indexes for table `tblreturnborrow`
--
ALTER TABLE `tblreturnborrow`
  ADD PRIMARY KEY (`borrowId`),
  ADD KEY `fk_borrower` (`borrowerId`),
  ADD KEY `fk_book` (`bookId`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblauthor`
--
ALTER TABLE `tblauthor`
  MODIFY `authorId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblbooks`
--
ALTER TABLE `tblbooks`
  MODIFY `bookId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblborrowers`
--
ALTER TABLE `tblborrowers`
  MODIFY `libraryId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `categoryId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcourses`
--
ALTER TABLE `tblcourses`
  MODIFY `courseId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  MODIFY `notificationId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblpenalties`
--
ALTER TABLE `tblpenalties`
  MODIFY `penaltyId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblreference`
--
ALTER TABLE `tblreference`
  MODIFY `referenceId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblreturnborrow`
--
ALTER TABLE `tblreturnborrow`
  MODIFY `borrowId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblbooks`
--
ALTER TABLE `tblbooks`
  ADD CONSTRAINT `fk_author` FOREIGN KEY (`authorId`) REFERENCES `tblauthor` (`authorId`),
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`categoryId`) REFERENCES `tblcategory` (`categoryId`);

--
-- Constraints for table `tblborrowers`
--
ALTER TABLE `tblborrowers`
  ADD CONSTRAINT `fk_course` FOREIGN KEY (`course`) REFERENCES `tblcourses` (`courseId`) ON DELETE CASCADE;

--
-- Constraints for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  ADD CONSTRAINT `fk_bookId` FOREIGN KEY (`bookId`) REFERENCES `tblbooks` (`bookId`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `tblnotifications_ibfk_1` FOREIGN KEY (`borrowerId`) REFERENCES `tblborrowers` (`idNumber`) ON DELETE CASCADE;

--
-- Constraints for table `tblreference`
--
ALTER TABLE `tblreference`
  ADD CONSTRAINT `fk_borrowerId` FOREIGN KEY (`borrowerId`) REFERENCES `tblborrowers` (`idNumber`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `tblreturnborrow`
--
ALTER TABLE `tblreturnborrow`
  ADD CONSTRAINT `fk_book` FOREIGN KEY (`bookId`) REFERENCES `tblbooks` (`bookId`),
  ADD CONSTRAINT `fk_borrower` FOREIGN KEY (`borrowerId`) REFERENCES `tblborrowers` (`idNumber`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
