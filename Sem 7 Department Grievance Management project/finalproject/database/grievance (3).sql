-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 06:15 AM
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
-- Database: `grievance`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(5) NOT NULL,
  `Comp_ID` varchar(30) NOT NULL,
  `Complaint_Cat_ID` int(5) DEFAULT NULL,
  `complaint_description` varchar(200) DEFAULT NULL,
  `Complaint_Document` varchar(100) DEFAULT NULL,
  `complaint_datetime` datetime DEFAULT NULL,
  `Due_Date` date DEFAULT NULL,
  `End_Date` date DEFAULT NULL,
  `status` enum('pending','processing','resolved') DEFAULT 'pending',
  `Student_ID` int(5) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `F_Comp_ID` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `Comp_ID`, `Complaint_Cat_ID`, `complaint_description`, `Complaint_Document`, `complaint_datetime`, `Due_Date`, `End_Date`, `status`, `Student_ID`, `faculty_id`, `F_Comp_ID`) VALUES
(20, 'S-061224160424708', 8, 'this is about the cara not working in room 4', 'uploads/S-061224160424708.pdf', '2024-12-06 11:34:45', '2024-12-21', '2024-12-06', 'resolved', 19, 8, 8),
(21, 'C-061224190449523', 7, 'The cleanliness of the premises has been noticeably lacking, with litter and unkempt spaces affectin', 'uploads/C-061224190449523.pdf', '2024-12-06 14:36:48', '2024-12-21', '0000-00-00', 'pending', 7, 10, 4),
(22, 'S-061224222502937', 8, 'I am writing to report recurring technical issues in our department. These include [specific issue(s', 'uploads/S-061224222502937.pdf', '2024-12-06 17:55:10', '2024-12-21', '2024-12-18', 'resolved', 7, 8, 8),
(25, 'S-071224145300389', 8, 'This is to provide the issue regarding the broken handle of the staircase.', 'uploads/S-071224145300389.pdf', '2024-12-07 10:23:35', '2024-12-22', '2024-12-18', 'resolved', 7, 8, 8),
(26, 'E-081224095200701', 2, 'Exam cell services need improvement; facing delays in results and inadequate communication.', 'uploads/E-081224095200701.pdf', '2024-12-08 05:23:36', '2024-12-23', '2024-12-17', 'resolved', 16, 2, 2),
(27, 'T-101224165744580', 6, 'I am writing to bring to your attention a recurring technical issue within the Computer Science Department. Over the past few weeks, several of the department’s computers have been experiencing signif', 'uploads/T-101224165744580.jpeg', '2024-12-10 12:27:56', '2024-12-25', '2024-12-18', 'resolved', 17, 9, 10),
(29, 'Ac-131224193903509', 3, 'the complaint about the problems regarding new Msc(Cs)-7 syllabus.\r\n', 'uploads/Ac-131224193903509.pdf', '2024-12-13 15:09:37', '2024-12-28', '2024-12-18', 'resolved', 20, 10, 3),
(30, 'E-131224194051446', 2, 'The complaint regarding not getting hall ticket for exam before the date.', 'uploads/E-131224194051446.pdf', '2024-12-13 15:11:20', '2024-12-28', '2024-12-18', 'resolved', 20, 6, 2),
(31, 'I-131224194136410', 10, 'The Ac is not working and has caused trouble in lab 4 due to pungent smell .', 'uploads/I-131224194136410.pdf', '2024-12-13 15:12:08', '2024-12-28', '2024-12-18', 'resolved', 20, 7, 6),
(32, 'I-171224155259937', 10, 'infrastructure', 'uploads/I-171224155259937.pdf', '2024-12-17 11:23:16', '2025-01-01', '2024-12-18', 'resolved', 19, 7, 6),
(33, 'E-171224160033005', 2, 'exam', 'uploads/E-171224160033005.pdf', '2024-12-17 11:30:41', '2025-01-01', '2024-12-18', 'resolved', 17, 7, 2),
(34, 'Eq-171224222916627', 9, 'I kindly request attention to desktops in lab3 . It is faulty, disrupting work. Please assist urgently.\r\n\r\n', 'uploads/Eq-171224222916627.pdf', '2024-12-17 18:01:19', '2025-01-01', '2024-12-18', 'resolved', 16, 4, 1),
(35, 'Li-171224223636849', 4, 'the books which are important for our syllabus are not available in the library', 'uploads/Li-171224223636849.pdf', '2024-12-17 18:07:08', '2025-01-01', '2024-12-18', 'resolved', 16, 2, 13),
(36, 'I-181224102048755', 10, 'The Ac is not working and has caused trouble in lab 4 due to pungent smell .', 'uploads/I-181224102048755.pdf', '2024-12-18 05:51:38', '2025-01-02', '2024-12-18', 'resolved', 22, 7, 6),
(37, 'T-181224105053383', 6, 'The computer no45 in lab 1 is not working.', 'uploads/T-181224105053383.pdf', '2024-12-18 06:21:34', '2025-01-02', '0000-00-00', 'pending', 22, 9, 10),
(38, 'T-191224090430283', 6, 'There is a techincal issue in classroom 4 projector .', 'uploads/T-191224090430283.pdf', '2024-12-19 04:35:06', '2025-01-03', '0000-00-00', 'pending', 19, 9, 10);

-- --------------------------------------------------------

--
-- Table structure for table `complaints_backup`
--

CREATE TABLE `complaints_backup` (
  `complaint_id` int(5) NOT NULL DEFAULT 0,
  `Comp_ID` varchar(30) NOT NULL,
  `Complaint_Cat_ID` int(5) DEFAULT NULL,
  `complaint_description` varchar(100) DEFAULT NULL,
  `Complaint_Document` varchar(100) DEFAULT NULL,
  `complaint_datetime` datetime DEFAULT NULL,
  `status` enum('pending','processing','resolved') DEFAULT 'pending',
  `Due_Date` date DEFAULT NULL,
  `End_Date` date DEFAULT NULL,
  `Student_ID` int(5) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `F_Comp_ID` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints_backup`
--

INSERT INTO `complaints_backup` (`complaint_id`, `Comp_ID`, `Complaint_Cat_ID`, `complaint_description`, `Complaint_Document`, `complaint_datetime`, `status`, `Due_Date`, `End_Date`, `Student_ID`, `faculty_id`, `F_Comp_ID`) VALUES
(12, 'E-021024221553410', 2, 'df', 'uploads/E-021024221553410.pdf', '2024-10-02 18:45:55', 'pending', '2024-10-02', '2024-10-17', 5, NULL, 2),
(14, 'E-141024220104313', 2, 'the', 'uploads/E-141024220104313.pdf', '2024-10-14 18:31:05', 'pending', '2024-10-14', '2024-10-29', 5, NULL, 2),
(15, '', 2, 'the', 'uploads/.pdf', '2024-10-14 18:52:01', 'pending', '2024-10-14', '2024-10-29', 5, 2, 2),
(17, 'E-141024222607782', 2, 'the', 'uploads/E-141024222607782.pdf', '2024-10-14 18:59:50', 'pending', '2024-10-14', '2024-10-29', 5, 2, 2),
(18, 'E-141024223028299', 2, 'the', 'uploads/E-141024223028299.pdf', '2024-10-14 19:00:30', 'pending', '2024-10-14', '2024-10-29', 5, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `complaint_category`
--

CREATE TABLE `complaint_category` (
  `complaint_category_id` int(5) NOT NULL,
  `category_description` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_category`
--

INSERT INTO `complaint_category` (`complaint_category_id`, `category_description`) VALUES
(1, 'Laboratory'),
(2, 'Exam Cell'),
(3, 'Academics'),
(4, 'Library'),
(5, 'Accounts and Billing'),
(6, 'Technical Issues'),
(7, 'Cleaning and Management'),
(8, 'Safety and Security'),
(9, 'Equipments'),
(10, 'Infrastructure'),
(11, 'Others');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `faculty_id` int(5) NOT NULL,
  `user_id` int(5) DEFAULT NULL,
  `post` enum('Lecturer','Assistant Professor','Associate Professor','Professor','Head of Department') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `user_id`, `post`) VALUES
(1, 10, 'Professor'),
(2, 11, 'Lecturer'),
(3, 13, ''),
(4, 15, 'Head of Department'),
(5, 28, 'Head of Department'),
(6, 29, 'Professor'),
(7, 30, 'Associate Professor'),
(8, 31, 'Professor'),
(9, 32, 'Professor'),
(10, 33, 'Professor'),
(11, 41, 'Assistant Professor'),
(12, 42, ''),
(13, 43, '');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_complaints_cat`
--

CREATE TABLE `faculty_complaints_cat` (
  `faculty_complaint_cat` int(5) NOT NULL,
  `Faculty_ID` int(5) DEFAULT NULL,
  `Complaint_Category_ID` int(5) DEFAULT NULL,
  `assigned_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_complaints_cat`
--

INSERT INTO `faculty_complaints_cat` (`faculty_complaint_cat`, `Faculty_ID`, `Complaint_Category_ID`, `assigned_date`) VALUES
(1, 4, 9, '2024-10-02 12:29:05'),
(2, 2, 2, '2024-10-02 12:29:05'),
(3, 11, 3, '2024-12-06 14:51:34'),
(4, 10, 7, '2024-12-06 14:52:36'),
(5, 9, 9, '2024-12-06 14:53:11'),
(6, 7, 10, '2024-12-06 14:53:37'),
(7, 7, 3, '2024-12-06 14:54:06'),
(8, 8, 8, '2024-12-06 14:54:29'),
(9, 11, 9, '2024-12-07 11:43:12'),
(10, 9, 6, '2024-12-10 16:57:40'),
(11, 10, 10, '2024-12-13 19:35:12'),
(12, 7, 2, '2024-12-13 19:43:30'),
(13, 2, 4, '2024-12-17 22:28:18');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(5) NOT NULL,
  `user_id` int(5) DEFAULT NULL,
  `enrollment_id` varchar(15) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `class` varchar(30) NOT NULL,
  `semester` int(2) NOT NULL,
  `batch` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `user_id`, `enrollment_id`, `phone`, `class`, `semester`, `batch`) VALUES
(5, 16, 'ENR005', '4567890123', 'CS105', 2, 2022),
(7, 19, '2.02129E+11', '234567234', 'Msc(cs)', 7, 1),
(16, 35, '202128900009.0', '6445738202.0', 'Msc(cs)', 7, 1),
(17, 36, '202128900011.0', '6475823942.0', 'Msc(cs)', 7, 1),
(18, 37, '202128900013.0', '5463827542.0', 'Msc(cs)', 7, 1),
(19, 38, '202128900015.0', '5463729102.0', 'Msc(cs)', 7, 1),
(20, 39, '202128900018.0', '6574820182.0', 'Msc(cs)', 7, 1),
(21, 40, '202128900019.0', '5473920234.0', 'Msc(cs)', 7, 1),
(22, 44, '234567854', '5473920236', 'Msc(cs)', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(5) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','faculty') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `password`, `role`) VALUES
(1, 'Admin', '', 'admin@gmail.com', 'admin', 'admin'),
(3, 'John', 'Doe', 'john.doe@example.com', 'Password123', 'admin'),
(10, 'Michael', 'Johnson', 'michaelj@example.com', '$2y$10$aWh6pVOWJV6gsvvD7czqa.C96lJtwpSwnO4c09XXge19LYOeZ0uBm', 'faculty'),
(11, 'Sarah', 'Brown', 'sarahb@example.com', '$2y$10$pedhQGMw5Wz23i9LZes5V.AwySJK5QoFcMD6CA.NwNXmxOKXPmaIC', 'faculty'),
(13, 'David', 'Green', 'davidg@example.com', '$2y$10$PCO/7F3IQAz9p0VeHR2eL.SspMYh9WaGicsuFV/B43JWeIbrgXrJu', 'faculty'),
(15, 'Emily', 'Moore', 'emilym@example.com', '$2y$10$t8j3UEc.hm0kcmTFBNgX5OwxdFLAg8iyPYBtcMxR1FdH9CGjr8NRu', 'faculty'),
(16, 'Chris', 'Miller', 'chrism@example.com', '$2y$10$pK1jht1xgHriWPeYil.SmuR0MIC5JalYY.dL4VpxLcLlxHIdNJROa', 'student'),
(19, 'Ekta', 'Agja', 'ekta@gmail.com', '$2y$10$7odgqmK7qgr1yTFMR30JhuhcktFj5.08Y4mCU3RuktjvwkqkZQe2.', 'student'),
(20, 'Arya', 'Ganotra', 'arya@gmail.com', '$2y$10$VlM7LQfBjo1yUhht1HC38uqgHgTZVkc8Az/FQ2.x.ZvE1OI5Kxyw.', 'student'),
(22, 'Nidhi', 'Chavda', 'nidhi@gmail.com', '$2y$10$9QTf1QdYQFgkggiSZZxVmO7pR1DK.K6ZbqXyUvrWWeyN8s0yr0.2O', 'student'),
(23, 'Vedant', 'Dave', 'vedant@gmail.com', '$2y$10$mCB3uMP3hbgJJwAvtOeaLOHMhjGAPAn.SOngwgUNrsdgO1amli1LO', 'student'),
(24, 'Harsh', 'Dedakiya', 'harsh@gmail.com', '$2y$10$nZqif2hwlIAHdm8R31P9ceIaJ.IkOrCwCmLa9N9VJzljaTL5F4qOG', 'student'),
(25, 'Krupanshi', 'Gandhi', 'krupanshi@gmail.com', '$2y$10$ZioMI5s/xGJzmiBRxmYosO6gEjDq1YMGAhxK2tagTGKL4Z7juWy8m', 'student'),
(26, 'Aryan', 'Hirpara', 'aryan@gmail.com', '$2y$10$Rv3x42Yp1UfQ8EWi42aVS.V9W5tr3NgWN0aWIDuODio0aV1ddlm1m', 'student'),
(27, 'Kashish', 'Jobaliya', 'kashish@gmail.com', '$2y$10$Mf/2EtiDFwPBUcL/FC6ZoOj8j72GFn6UOpdlpDR8weYXKLqFTlO.u', 'student'),
(28, 'Dr. Jyoti', 'Pareek', 'drjyotipareek@gmail.com', '$2y$10$NpJy8kna42iXlSyuaYvY.umBQr1ZdcXjaChR1HIBkIuw0VSQi45Ia', 'faculty'),
(29, 'Dr. Hiren', 'Joshi', 'hdjoshi@gujaratuniversity.ac.in', '$2y$10$AVDaL1rWEgJI6IrmhLzAO.S0IkBk0FLitoKn7jATBEHzQR6Ijesqi', 'faculty'),
(30, 'Dr. Hardik', 'Joshi', 'drhardik_joshi@gmail.com', '$2y$10$bPu4XsHhtCWAudzgIyQ2XOy4QP0nCfj3chmdzH/aVgYOtTr5EmY8S', 'faculty'),
(31, 'Dr. Bhumika', 'Shah', 'drbhumika_shah@gmail.com', '$2y$10$1JkCFAd4iuBt4c0oAhpsyezjBJfxGX4SJUVZ1ToXShGhTHh4q9erq', 'faculty'),
(32, 'Dr. Jigna', 'Satani', 'drjigna_satani@gmail.com', '$2y$10$89UnQZpPJ/BKTjpU/ZI9oOLATV/.ShFjJPMJFivBsRabgFnY.7lT6', 'faculty'),
(33, 'Mr. Jay', 'Patel', 'mrjay_patel@gmail.com', '$2y$10$pUDzdzxWVP9sWvqrnd.La.OFAt86xZG19ElIkGs5RH222FV1vNY8O', 'faculty'),
(34, 'Mr. Admin', 'User', 'admin@email.com', '$2y$10$msKIxby0SUSypvEEV56vzuWNQGYFysHXxd.ErDrRFONgFi6sM3qjm', 'admin'),
(35, 'Trupal', 'Lathiya', 'trupal@gmail.com', '$2y$10$/OJzap/e3ArbI7H.mjeQFu4kqkQ6isTqLXoMUoheb6cSt9NNDv48i', 'student'),
(36, 'Devansh', 'Makwana', 'devansh@gmail.com', '$2y$10$8AweVR5dwzhmZcdYDLSrDegHTZQj3gooXElxwsyNk2c3YthCA0gmW', 'student'),
(37, 'Twinkle', 'Manke', 'twinkle@gmail.com', '$2y$10$OQhMPRaIAI9gpZihR7X1xOygX0BbfgkqGgNNK84kulBPy2k.hfFTK', 'student'),
(38, 'Tirth', 'Mehta', 'tirth@gmail.com', '$2y$10$pwlKmpADLopun0EScQ/NPuQ8TeNDRhbk36IM2iOkmAWjokVJwHU4S', 'student'),
(39, 'Hem', 'Shah', 'hem@gmail.com', '$2y$10$UpSWwo2agD6nVBnQtadlI.b7wxKwRylrKoLaFL6lBSUnt/2Zl00K.', 'student'),
(40, 'Kalpi', 'Shah', 'kalpi@gmail.com', '$2y$10$lzv84VMbkDzDaeKj351p6OZRMAp/YNuUHnZeOkpl6TFs/85UjHtxe', 'student'),
(41, 'Dr. Maitry', 'Jhaveri', 'drmaitry_javeri@gmail.com', '$2y$10$Hj4iC5wxo8UCj1piY7zmqe/FX5v0T5oiHGBzLX55hrHVPisT7LWWy', 'faculty'),
(42, 'Miss. Saloni', 'Shah', 'misssaloni_shah@gmail.com', '$2y$10$ZhRrt72FPi7oKmLUdY3Ane6obCCmqyhcw/K46JmmyOIccfuGVh2PO', 'faculty'),
(43, 'Miss. Sneha', 'Arun', 'misssneha_arun@gmail.com', '$2y$10$UZNBdNZxolcLDkEXq1Y.oOCbplKWzcy9sJa0JYdVm8OH1Yq0n3S9i', 'faculty'),
(44, 'Janvi', 'Chauhan', 'janvi.chauhan4599@gmail.com', '$2y$10$L/5ZcBwJdD.Ygu2yyYqdi.3shbtanh8SFXC9zeOIzyCIELp00FPMK', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `Complaint_Cat_ID` (`Complaint_Cat_ID`),
  ADD KEY `Student_ID` (`Student_ID`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `F_Comp_ID` (`F_Comp_ID`);

--
-- Indexes for table `complaint_category`
--
ALTER TABLE `complaint_category`
  ADD PRIMARY KEY (`complaint_category_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `faculty_complaints_cat`
--
ALTER TABLE `faculty_complaints_cat`
  ADD PRIMARY KEY (`faculty_complaint_cat`),
  ADD KEY `Faculty_ID` (`Faculty_ID`),
  ADD KEY `Complaint_Category_ID` (`Complaint_Category_ID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `complaint_category`
--
ALTER TABLE `complaint_category`
  MODIFY `complaint_category_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `faculty_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `faculty_complaints_cat`
--
ALTER TABLE `faculty_complaints_cat`
  MODIFY `faculty_complaint_cat` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`Complaint_Cat_ID`) REFERENCES `complaint_category` (`complaint_category_id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`Student_ID`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `complaints_ibfk_3` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`),
  ADD CONSTRAINT `complaints_ibfk_4` FOREIGN KEY (`F_Comp_ID`) REFERENCES `faculty_complaints_cat` (`faculty_complaint_cat`);

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_complaints_cat`
--
ALTER TABLE `faculty_complaints_cat`
  ADD CONSTRAINT `faculty_complaints_cat_ibfk_1` FOREIGN KEY (`Faculty_ID`) REFERENCES `faculty` (`faculty_id`),
  ADD CONSTRAINT `faculty_complaints_cat_ibfk_2` FOREIGN KEY (`Complaint_Category_ID`) REFERENCES `complaint_category` (`complaint_category_id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
