-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2026 at 08:56 PM
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
-- Database: `svu_events`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name_ar`, `name_en`) VALUES
(1, 'ITE', 'ITE'),
(2, 'مسابقات', 'Competitions'),
(3, 'نشاطات ترفيهية', 'Entertainment'),
(4, 'رحلات استكشاف', 'Exploration Trips');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `category_en` varchar(100) DEFAULT NULL,
  `location_en` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `category`, `location`, `event_date`, `image`, `title_en`, `description_en`, `category_en`, `location_en`) VALUES
(1, 'حلقة بحث معمقة: تشفير العملات الرقمية وتطبيقاتها في الأنظمة المالية', 'يقدمها الطالب مؤيد محمد سامر زعويط، تستعرض تقنيات التشفير المستخدمة في البيتكوين والإيثيريوم وخوارزميات التواقيع الرقمية.', '1', '1', '2026-06-25', '1778333986_photo_2026-05-09_16-37-54.webp', 'Advanced Research Seminar: Cryptocurrency Encryption & Financial Systems', 'Presented by Mouayad MHD Samer Zaweet. Covers Bitcoin & Ethereum encryption and digital signatures.', '1', '1'),
(2, 'هاكاثون البرمجة السحابية', 'مسابقة برمجة جماعية لبناء تطبيقات سحابية باستخدام AWS وAzure خلال 48 ساعة. جوائز للفائزين.', '1', '2', '2026-09-01', '1778346265_2.webp', 'Cloud Programming Hackathon', '48-hour team coding contest building cloud apps with AWS & Azure. Prizes.', '1', '2'),
(3, 'المعرض التقني لمشاريع التخرج', 'عرض مشاريع تخرج طلاب الهندسة المعلوماتية وتقييمها من قبل لجنة أكاديمية.', '1', '3', '2026-07-07', '1778346389_3.webp', 'ITE Graduation Projects Expo', 'ITE graduation projects exhibition with academic committee evaluation.', '1', '3'),
(4, 'ورشة عمل: الذكاء الاصطناعي التوليدي', 'ورشة عملية عن نماذج GPT و Stable Diffusion وتطبيقاتها في التعليم والصناعة.', '1', '1', '2026-07-18', '1778346511_4.webp', 'Workshop: Generative AI', 'Hands-on workshop on GPT & Stable Diffusion for education and industry.', '1', '1'),
(5, 'مهرجان الطالب المبرمج', 'مسابقة برمجة تنافسية بين طلاب كليات الهندسة المعلوماتية على مستوى الجامعات السورية.', '2', '1', '2026-07-28', '1778346607_5.webp', 'Student Programmer Festival', 'Competitive programming contest among ITE students across Syrian universities.', '2', '1'),
(6, 'تحدي الأمن السيبراني', 'مسابقة Capture The Flag لاختبار مهارات الاختراق الأخلاقي واكتشاف الثغرات.', '2', '4', '2026-08-08', '1778346717_6.webp', 'Cybersecurity Challenge', 'CTF competition testing ethical hacking & vulnerability discovery skills.', '2', '4'),
(7, 'ماراثون SVU الرياضي', 'ماراثون رياضي بمشاركة طلابية واسعة يشمل سباقات 5كم و10كم مع توزيع ميداليات.', '3', '5', '2026-08-16', '1778346779_7.webp', 'SVU Sports Marathon', '5km & 10km student marathon with medals ceremony.', '3', '5'),
(8, 'رحلة استكشافية إلى قلعة الحصن', 'رحلة علمية لطلاب ITE لاستكشاف أنظمة الحماية والبناء في القلاع التاريخية وربطها بمفاهيم أمن المعلومات.', '4', '6', '2026-08-24', '1778346841_8.webp', 'Exploratory Trip to Krak des Chevaliers', 'ITE trip exploring ancient fortress defense systems linked to cybersecurity concepts.', '4', '6'),
(9, 'رحلة توثيقية إلى دمشق القديمة', 'جولة توثيقية في دمشق القديمة لاستكشاف أنظمة الري والبناء التاريخية وتوثيقها رقمياً.', '4', '7', '2026-07-02', '1778346986_9.webp', 'Documentary Tour: Old Damascus', 'Documentary tour exploring historical irrigation & construction systems for digital archiving.', '4', '7'),
(10, 'رحلة استكشاف أنظمة النقل الذكية', 'زيارة ميدانية لمركز التحكم المروري الذكي للاطلاع على أنظمة النقل الحديثة.', '4', '8', '2026-06-28', '1778347119_10.webp', 'Smart Transportation Systems Tour', 'Field visit to smart traffic control center exploring modern transport systems.', '4', '8'),
(11, 'رحلة علمية إلى مرصد دمشق الفلكي', 'رصد فلكي ليلي مع شرح لأنظمة التلسكوب ومعالجة الصور الفلكية رقمياً.', '4', '9', '2026-09-09', '1778347287_11.webp', 'Scientific Trip: Damascus Observatory', 'Night sky observation with telescope systems & digital astrophotography processing.', '4', '9'),
(12, 'مسابقة المشاريع الريادية', 'مسابقة لعرض مشاريع ريادية تقنية أمام لجنة تحكيم من أكاديميين ومستثمرين.', '2', '3', '2026-09-20', '1778347340_12.webp', 'Entrepreneurship Projects Competition', 'Tech startup pitch competition judged by academics & investors.', '2', '3'),
(13, 'مسابقة الروبوتات الذكية', 'مسابقة تصميم وبرمجة الروبوتات لتنفيذ مهام محددة باستخدام الذكاء الاصطناعي.', '2', '1', '2026-10-01', '1778347416_13.webp', 'Smart Robotics Competition', 'Robot design & programming competition using AI for specific tasks.', '2', '1'),
(14, 'اليوم الترفيهي المفتوح', 'يوم ترفيهي مفتوح يشمل ألعاباً جماعية وعروضاً موسيقية ومسابقات ثقافية.', '3', '5', '2026-10-12', '1778347481_14.webp', 'Open Entertainment Day', 'Fun day with team games, music performances & cultural quizzes.', '3', '5'),
(15, 'مهرجان الطعام العالمي', 'مهرجان طعام يقدمه طلاب الجامعة من مختلف الجنسيات لعرض ثقافاتهم.', '3', '10', '2026-10-20', '1778347591_15.webp', 'International Food Festival', 'Food festival by international students showcasing their cultures.', '3', '10'),
(16, 'أمسية الأفلام الوثائقية التقنية', 'عرض أفلام وثائقية عن تاريخ الحوسبة والذكاء الاصطناعي تليها مناقشة مفتوحة.', '3', '11', '2026-10-29', '1778347668_16.webp', 'Tech Documentary Night', 'Documentary screening on computing & AI history with open discussion.', '3', '11'),
(17, 'ورشة عمل: تطوير تطبيقات الويب', 'ورشة عملية متقدمة لتطوير تطبيقات ويب كاملة باستخدام PHP وLaravel وMySQL.', '1', '2', '2026-11-05', '1778347738_17.webp', 'Workshop: Web Application Development', 'Advanced workshop building full-stack web apps with PHP, Laravel & MySQL.', '1', '2'),
(18, 'ندوة: مستقبل الحوسبة الكمومية', 'ندوة علمية تناقش أحدث التطورات في الحوسبة الكمومية وتأثيرها على أمن المعلومات.', '1', '1', '2026-11-18', '1778347797_18.webp', 'Symposium: Future of Quantum Computing', 'Scientific symposium on latest quantum computing advances & cybersecurity impact.', '1', '1'),
(19, 'بطولة الشطرنج الجامعية', 'بطولة شطرنج تنافسية مفتوحة لجميع طلاب الجامعة مع جوائز للمراكز الأولى.', '2', '1', '2026-11-26', '1778347881_19.webp', 'University Chess Championship', 'Competitive chess tournament open to all students with prizes.', '2', '1'),
(20, 'حفل ختام الأنشطة السنوي', 'حفل ختامي لتكريم المشاركين وتوزيع الجوائز بحضور إدارة الجامعة.', '3', '11', '2026-12-04', '1778347987_20.webp', 'Annual Activities Closing Ceremony', 'Closing ceremony honoring participants with awards & university management.', '3', '11');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) UNSIGNED NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name_ar`, `name_en`) VALUES
(1, 'القاعة الذكية - كلية الهندسة المعلوماتية', 'Smart Hall - ITE Faculty'),
(2, 'مختبر الحوسبة السحابية - ITE', 'Cloud Computing Lab - ITE'),
(3, 'البهو الرئيسي - مبنى ITE', 'Main Hall - ITE Building'),
(4, 'مختبر الأمن الرقمي - ITE', 'Digital Security Lab - ITE'),
(5, 'الملعب الجامعي', 'University Stadium'),
(6, 'قلعة الحصن - حمص', 'Krak des Chevaliers - Homs'),
(7, 'دمشق القديمة', 'Old Damascus'),
(8, 'مركز النقل الذكي - دمشق', 'Smart Transport Center - Damascus'),
(9, 'مرصد دمشق الفلكي', 'Damascus Astronomical Observatory'),
(10, 'الحرم الجامعي المفتوح', 'Open Campus'),
(11, 'مسرح الجامعة', 'University Theater');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
