-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2016 at 08:48 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fyp_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `batch`
--

CREATE TABLE `batch` (
  `batchId` int(255) NOT NULL,
  `batchName` varchar(255) NOT NULL,
  `startingDate` date DEFAULT NULL,
  `isActive` tinyint(4) DEFAULT '0' COMMENT '0= inactive , 1= active',
  `sdpPart` tinyint(4) DEFAULT '1' COMMENT '0 or 1',
  `createdDtm` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='batchDeadlinesInfo';

-- --------------------------------------------------------

--
-- Table structure for table `batch_settings`
--

CREATE TABLE `batch_settings` (
  `id` int(11) NOT NULL,
  `batchId` int(11) NOT NULL DEFAULT '0',
  `male_female_group` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=no;1=yes',
  `sdp1_grading` tinyint(1) NOT NULL DEFAULT '0',
  `internal_evaluation` tinyint(1) NOT NULL DEFAULT '0',
  `sdp2_grading` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `batch_tasks`
--

CREATE TABLE `batch_tasks` (
  `taskId` int(11) NOT NULL,
  `batchId` int(11) DEFAULT NULL,
  `sdpPart` varchar(50) DEFAULT '1',
  `taskName` text,
  `taskDetail` text,
  `taskWeek` int(11) DEFAULT NULL,
  `taskDeadline` datetime DEFAULT NULL,
  `templateId` int(11) DEFAULT NULL,
  `hasDeliverable` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=has deliverable',
  `createdDtm` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `batch_templates`
--

CREATE TABLE `batch_templates` (
  `templateId` int(11) NOT NULL,
  `batchId` int(11) DEFAULT NULL,
  `templateName` varchar(100) DEFAULT NULL,
  `templateLocation` varchar(150) DEFAULT NULL,
  `uploadedDtm` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `external_examiner`
--

CREATE TABLE `external_examiner` (
  `examinerId` int(255) NOT NULL,
  `examinerName` varchar(100) NOT NULL,
  `examinerEmail` varchar(255) NOT NULL,
  `examinerPhone` varchar(50) NOT NULL,
  `examinerPassword` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdDtm` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='examinerGroupInfo';

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `facultyId` int(255) NOT NULL,
  `facultyName` varchar(255) NOT NULL,
  `facultyPhoneNo` varchar(50) NOT NULL,
  `facultyEmail` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `facultyImage` varchar(255) DEFAULT NULL,
  `facultyPassword` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) DEFAULT '0',
  `isCoordinator` tinyint(1) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Faculty Details';

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`facultyId`, `facultyName`, `facultyPhoneNo`, `facultyEmail`, `designation`, `facultyImage`, `facultyPassword`, `isAdmin`, `isCoordinator`, `isActive`, `createdDtm`) VALUES
(0, 'Super Admin', '', 'superadmin@fypms.com', '--', NULL, '123', 1, 0, 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_student_group`
--

CREATE TABLE `faculty_student_group` (
  `facultyStudentId` int(255) NOT NULL,
  `groupId` int(255) NOT NULL,
  `facultyId` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='facultyGroupInfo';

-- --------------------------------------------------------

--
-- Table structure for table `faculty_student_request`
--

CREATE TABLE `faculty_student_request` (
  `requestId` int(11) NOT NULL,
  `facultyId` int(11) DEFAULT NULL,
  `groupId` int(11) DEFAULT NULL,
  `requestDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `studentId` int(11) DEFAULT NULL,
  `groupId` int(11) DEFAULT NULL,
  `sdpPart` int(11) DEFAULT NULL,
  `comments` text,
  `grade` varchar(50) DEFAULT NULL,
  `gradedBy` int(11) DEFAULT NULL COMMENT 'User id of user',
  `gradeDtm` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Table structure for table `group_requests`
--

CREATE TABLE `group_requests` (
  `requestId` int(255) NOT NULL,
  `studentId` int(255) NOT NULL,
  `groupId` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_uploads`
--

CREATE TABLE `group_uploads` (
  `id` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `taskId` int(11) NOT NULL,
  `uploadFile` varchar(50) NOT NULL,
  `uploadedBy` int(11) NOT NULL COMMENT 'userId of uploader',
  `uploadedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `internal_evaluations`
--

CREATE TABLE `internal_evaluations` (
  `id` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `votedBy` int(11) NOT NULL COMMENT 'facultyId of Gradedby',
  `aeVote` int(11) NOT NULL COMMENT 'Allow External Exam',
  `ohVote` int(11) NOT NULL COMMENT 'Allow Partition in Open House'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_logs`
--

CREATE TABLE `meeting_logs` (
  `id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `meeting_title` varchar(50) NOT NULL,
  `meeting_dtm` datetime NOT NULL,
  `comments` text,
  `meeting_status` enum('Pending','Done','Cancelled','Postponed') NOT NULL DEFAULT 'Pending',
  `created_dtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Maintain all meeting logs of supervisors with students';

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `projectId` int(11) DEFAULT NULL,
  `projectName` varchar(50) DEFAULT NULL,
  `projectStatus` tinyint(4) DEFAULT NULL COMMENT '1=SDP-1 ,2=SDP-2,3=Passed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `project_repository`
--

CREATE TABLE `project_repository` (
  `id` int(11) NOT NULL,
  `batchId` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `repository_users`
--

CREATE TABLE `repository_users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL DEFAULT '0',
  `user_email` varchar(50) NOT NULL DEFAULT '0',
  `user_password` varchar(50) DEFAULT NULL,
  `access_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1= full-access '
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentId` int(255) NOT NULL,
  `studentName` varchar(255) CHARACTER SET utf8 NOT NULL,
  `studentCMS` varchar(50) CHARACTER SET utf8 NOT NULL,
  `studentEmail` varchar(255) CHARACTER SET utf8 NOT NULL,
  `studentPhoneNo` varchar(50) CHARACTER SET utf8 NOT NULL,
  `studentGender` varchar(10) CHARACTER SET utf8 NOT NULL,
  `studentPassword` varchar(255) CHARACTER SET utf8 NOT NULL,
  `studentImage` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `groupId` int(255) DEFAULT NULL,
  `isLeader` int(1) DEFAULT NULL,
  `batchId` int(4) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='FYP Student Records';

-- --------------------------------------------------------

--
-- Table structure for table `student_group`
--

CREATE TABLE `student_group` (
  `groupId` int(255) NOT NULL,
  `projectName` varchar(255) DEFAULT NULL,
  `batchId` int(11) DEFAULT NULL,
  `sdpPart` int(1) NOT NULL DEFAULT '1' COMMENT 'Here to check before deleting group',
  `groupLimit` int(1) NOT NULL DEFAULT '3',
  `inGroup` int(255) NOT NULL DEFAULT '1',
  `leaderId` int(255) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='groupInfo';

-- --------------------------------------------------------

--
-- Table structure for table `student_group_request`
--

CREATE TABLE `student_group_request` (
  `requestId` int(11) NOT NULL,
  `studentId` int(11) NOT NULL COMMENT 'Request sent by',
  `groupId` int(11) NOT NULL COMMENT 'Request sent to group',
  `requestDtm` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Requests sent to join a group a stored here temporarilty';

-- --------------------------------------------------------

--
-- Table structure for table `timeline_faculty`
--

CREATE TABLE `timeline_faculty` (
  `id` int(11) NOT NULL,
  `title` text,
  `details` text,
  `type` varchar(50) DEFAULT NULL,
  `batchId` int(11) DEFAULT NULL,
  `sdpPart` int(11) DEFAULT NULL,
  `createdDtm` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='timeline for faculty';

-- --------------------------------------------------------

--
-- Table structure for table `timeline_student`
--

CREATE TABLE `timeline_student` (
  `id` int(11) NOT NULL,
  `title` text,
  `details` text,
  `type` varchar(50) DEFAULT NULL,
  `taskId` int(11) DEFAULT NULL,
  `batchId` int(11) DEFAULT NULL,
  `sdpPart` int(11) DEFAULT NULL,
  `createdDtm` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Timeline for students';

-- --------------------------------------------------------

--
-- Table structure for table `work_load`
--

CREATE TABLE `work_load` (
  `loadId` int(255) NOT NULL,
  `facultyId` int(255) NOT NULL,
  `totalLoad` int(255) NOT NULL,
  `currentLoad` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='workload_Info';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batch`
--
ALTER TABLE `batch`
  ADD PRIMARY KEY (`batchId`);

--
-- Indexes for table `batch_settings`
--
ALTER TABLE `batch_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `batch_tasks`
--
ALTER TABLE `batch_tasks`
  ADD PRIMARY KEY (`taskId`);

--
-- Indexes for table `batch_templates`
--
ALTER TABLE `batch_templates`
  ADD PRIMARY KEY (`templateId`);

--
-- Indexes for table `external_examiner`
--
ALTER TABLE `external_examiner`
  ADD PRIMARY KEY (`examinerId`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`facultyId`);

--
-- Indexes for table `faculty_student_group`
--
ALTER TABLE `faculty_student_group`
  ADD PRIMARY KEY (`facultyStudentId`);

--
-- Indexes for table `faculty_student_request`
--
ALTER TABLE `faculty_student_request`
  ADD PRIMARY KEY (`requestId`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_requests`
--
ALTER TABLE `group_requests`
  ADD PRIMARY KEY (`requestId`);

--
-- Indexes for table `group_uploads`
--
ALTER TABLE `group_uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `internal_evaluations`
--
ALTER TABLE `internal_evaluations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meeting_logs`
--
ALTER TABLE `meeting_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_repository`
--
ALTER TABLE `project_repository`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `repository_users`
--
ALTER TABLE `repository_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentId`);

--
-- Indexes for table `student_group`
--
ALTER TABLE `student_group`
  ADD PRIMARY KEY (`groupId`);

--
-- Indexes for table `student_group_request`
--
ALTER TABLE `student_group_request`
  ADD PRIMARY KEY (`requestId`);

--
-- Indexes for table `timeline_faculty`
--
ALTER TABLE `timeline_faculty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timeline_student`
--
ALTER TABLE `timeline_student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `work_load`
--
ALTER TABLE `work_load`
  ADD PRIMARY KEY (`loadId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batch`
--
ALTER TABLE `batch`
  MODIFY `batchId` int(255) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `batch_settings`
--
ALTER TABLE `batch_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `batch_tasks`
--
ALTER TABLE `batch_tasks`
  MODIFY `taskId` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `batch_templates`
--
ALTER TABLE `batch_templates`
  MODIFY `templateId` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `external_examiner`
--
ALTER TABLE `external_examiner`
  MODIFY `examinerId` int(255) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `facultyId` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `faculty_student_group`
--
ALTER TABLE `faculty_student_group`
  MODIFY `facultyStudentId` int(255) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `faculty_student_request`
--
ALTER TABLE `faculty_student_request`
  MODIFY `requestId` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group_requests`
--
ALTER TABLE `group_requests`
  MODIFY `requestId` int(255) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group_uploads`
--
ALTER TABLE `group_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `internal_evaluations`
--
ALTER TABLE `internal_evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `meeting_logs`
--
ALTER TABLE `meeting_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project_repository`
--
ALTER TABLE `project_repository`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `repository_users`
--
ALTER TABLE `repository_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `studentId` int(255) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `student_group`
--
ALTER TABLE `student_group`
  MODIFY `groupId` int(255) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `student_group_request`
--
ALTER TABLE `student_group_request`
  MODIFY `requestId` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `timeline_faculty`
--
ALTER TABLE `timeline_faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `timeline_student`
--
ALTER TABLE `timeline_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `work_load`
--
ALTER TABLE `work_load`
  MODIFY `loadId` int(255) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
