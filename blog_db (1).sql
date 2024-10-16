-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2024 at 01:02 PM
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
-- Database: `blog_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`) VALUES
(5, 'admin@gmail.com', 'admin@gmail.com', '$2y$10$hTplNTHAc8s23TxRrs4kB.6LX/91N6KtfLIDs/gZre2ypPRg47ytS');

-- --------------------------------------------------------

--
-- Table structure for table `blog_likes`
--

CREATE TABLE `blog_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment`, `created_at`) VALUES
(37, 21, 27, 'Nice', '2024-10-15 02:35:34'),
(38, 21, 27, 'Good Work', '2024-10-15 02:35:39'),
(39, 22, 27, 'Amazing', '2024-10-15 10:38:30');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `liked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `visitor_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `user_id`, `liked_at`, `visitor_id`) VALUES
(310, 21, NULL, '2024-10-15 02:34:58', 2147483647),
(311, 21, NULL, '2024-10-15 02:34:59', 2147483647),
(315, 22, 27, '2024-10-15 02:35:20', 0),
(327, 21, NULL, '2024-10-15 09:38:23', 1886743715),
(328, 21, NULL, '2024-10-15 09:38:26', 2147483647),
(329, 21, NULL, '2024-10-15 09:38:27', 2147483647),
(330, 21, NULL, '2024-10-15 09:38:38', 2147483647),
(331, 21, NULL, '2024-10-15 09:38:38', 2147483647),
(332, 21, NULL, '2024-10-15 09:38:45', 2147483647),
(333, 21, NULL, '2024-10-15 09:51:59', 2147483647),
(334, 21, NULL, '2024-10-15 09:51:59', 2147483647),
(335, 21, NULL, '2024-10-15 10:00:43', 2147483647),
(336, 21, NULL, '2024-10-15 10:00:44', 2147483647);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `userLikes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`userLikes`)),
  `likes_count` int(255) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `content`, `created_at`, `updated_at`, `userLikes`, `likes_count`, `category`) VALUES
(21, 0, ' The Rise of 5G in the Philippines', 'The Philippines is embracing the 5G revolution as telecommunication companies race to expand coverage across the country. 5G promises faster internet speeds, lower latency, and greater connectivity, which is crucial for the nation’s growing tech industry and remote work setups. The potential applications of 5G in sectors such as healthcare, education, and transportation could be transformative, offering new opportunities for smart cities, telemedicine, and automation. As the Philippines continues to adapt to this technology, challenges such as infrastructure upgrades and equitable access need to be addressed.', '2024-10-14 20:02:57', '2024-10-15 10:38:54', NULL, 12, 'Technology'),
(22, 0, 'The Role of AI in Filipino Businesses', 'Artificial Intelligence (AI) is slowly making its way into Filipino businesses, from customer service chatbots to advanced data analytics. Local companies are leveraging AI to improve productivity and enhance customer experiences, with some industries like retail and banking leading the charge. AI offers a way for Filipino businesses to remain competitive in the global market, but there is a need for more education and training on how to integrate these technologies effectively. With proper implementation, AI could drive economic growth and help bridge the technology gap in the Philippines.', '2024-10-14 20:04:48', '2024-10-15 02:35:20', NULL, 1, 'Technology'),
(23, 0, 'Online Learning Revolution in the Philippines', 'The COVID-19 pandemic has forever changed the landscape of education in the Philippines. As schools transitioned to online learning, both challenges and opportunities emerged. While many students struggled with poor internet access, others found new ways to adapt through flexible learning methods. This shift has prompted schools and universities to invest in better digital infrastructure and online resources. With ongoing improvements in internet connectivity, online learning may continue to be an integral part of the educational system in the Philippines, offering more inclusive options for students across the country.\r\n', '2024-10-14 20:05:02', '2024-10-15 10:56:49', NULL, 0, 'Education'),
(24, 0, 'The Value of STEM Education for Future Filipino Innovators', 'In recent years, the Philippines has placed a greater emphasis on STEM (Science, Technology, Engineering, and Mathematics) education, recognizing its importance in fostering innovation and economic development. With the rise of technology-driven industries, a solid foundation in STEM can equip Filipino students with the skills needed to compete in a globalized world. Government and private sector initiatives have increased support for STEM programs, from scholarships to specialized training centers. By nurturing talent in these fields, the country is preparing its next generation of innovators and problem-solvers.', '2024-10-14 20:05:20', '2024-10-14 20:05:20', NULL, 0, 'Education'),
(25, 0, 'The Role of Social Media in Philippine Elections', 'Social media has become a powerful tool in shaping public opinion during elections in the Philippines. Platforms like Facebook and Twitter are not only used for campaign advertisements but also as spaces for political discourse. While this digital shift provides greater access to information and engagement with voters, it also opens the door to misinformation and fake news. As the country prepares for future elections, regulating the role of social media in political campaigns has become a crucial conversation. Ensuring transparency and responsible use of these platforms is essential for fair and informed elections.', '2024-10-14 20:05:36', '2024-10-14 20:05:36', NULL, 0, 'Politics'),
(26, 0, 'The Philippine Economic Recovery: What’s Next?', 'The Philippine economy faced significant setbacks due to the COVID-19 pandemic, but signs of recovery are starting to emerge. As businesses reopen and consumer spending increases, the government is focusing on boosting local industries and investing in infrastructure projects to stimulate growth. Key sectors such as tourism, manufacturing, and agriculture are seen as vital components of the recovery plan. However, challenges such as inflation, unemployment, and the global economic slowdown still pose threats. Moving forward, fostering a sustainable recovery will require long-term economic policies and support for small and medium-sized enterprises (SMEs).', '2024-10-14 20:05:50', '2024-10-14 20:05:50', NULL, 0, 'Economics'),
(27, 0, 'SASA', 'AS', '2024-10-14 20:08:18', '2024-10-14 20:08:18', NULL, 0, 'Technology');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 for ''user'', 1 for ''admin''',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `email`) VALUES
(25, 'john2@gmail.com', '$2y$10$A50fnCyZa4m0f6Umve/8MOQAdreUvpevRWnvBsfNREplPcSGtNLre', 0, '2024-10-12 03:01:43', 'john2@gmail.com'),
(27, 'john@gmail.com', '$2y$10$Vp3.aQtOLnISJGxoH2sic.XcWbgROLA7tE9YHdRb.mbGhEqyeDGfa', 0, '2024-10-12 03:12:46', 'john@gmail.com'),
(28, 'john4@gmail.com', '$2y$10$I1ofuvtZZ9X.7.DfWyEi7.jqZ4PIulysmK5v/iKX8EKkqhto2plDi', 0, '2024-10-14 19:37:37', 'john4@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_likes`
--
ALTER TABLE `blog_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`post_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `POST_FOREIGN_KEY` (`post_id`),
  ADD KEY `USER_FOREIGN_KEY` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`user_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FOREIGN` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_likes`
--
ALTER TABLE `blog_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=345;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `POST_FOREIGN_KEY` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `USER_FOREIGN_KEY` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
