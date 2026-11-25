-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 25 2026 г., 10:03
-- Версия сервера: 10.8.4-MariaDB
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `IV1-23-1`
--

-- --------------------------------------------------------

--
-- Структура таблицы `articles`
--

CREATE TABLE `articles` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `articles`
--

INSERT INTO `articles` (`id`, `author_id`, `name`, `text`, `created_at`, `img`) VALUES
(16, 1, 'но в моих акварелях', 'была ядовитою вода пуцуцк', '2026-11-10 13:45:30', NULL),
(22, 1, 'ВИГУКИ', 'КАНОН', '2026-11-10 14:42:58', NULL),
(23, 1, 'мимс', 'смсми', '2026-11-10 14:49:01', NULL),
(24, 1, 'hjghjgf', 'fghjjhfg', '2026-11-10 14:57:30', NULL),
(25, 1, 'dgfdfg', 'dfgdfggrd', '2026-11-10 14:57:37', NULL),
(26, 1, 'dfsdsfg', 'dsfggfds', '2026-11-10 14:57:48', NULL),
(27, 1, 'я упал FDGGFDGFD', 'rtytyryrtyrtrye', '2026-11-10 14:58:09', NULL),
(30, 1, 'ваыывваываы', 'выфаваыф', '2026-11-10 15:06:11', NULL),
(36, 2, 'safsda', 'adfsasf', '2026-11-10 15:41:56', 'uploads/Снимок экрана (14).png'),
(38, 2, 'Есть', 'хочу', '2026-11-10 15:44:20', 'uploads/87578-dettext_com-legkie-risunki-edy.png'),
(47, 2, 'OOO', 'Oпвыапавыапяв', '2026-11-17 15:08:46', NULL),
(52, 2, 'gfdssgdf', 'gfdssdfg', '2026-11-17 15:43:48', NULL),
(54, 2, 'ваыа', 'аавфыфыва апррпва', '2026-11-17 15:56:42', NULL),
(55, 2, 'орпоравыпро апыв', 'выфвыафвыа', '2026-11-24 14:46:24', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `role` enum('user','admin') NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `nickname`, `email`, `is_confirmed`, `role`, `password_hash`, `auth_token`, `created_at`) VALUES
(1, 'Nik', 'nik@mail.ru', 1, 'user', 'wqwqw', 'qweqw', '2026-03-18 11:14:40'),
(2, 'yanaaaaa', 'yanaaaaa@yanaaaaa.yanaaaaa', 1, 'user', '$2y$10$EgEPjEM0tPAavhaoljjNquRP9JT/Rm9H3S5Lnwc9burbFOtcHcwle', '175f87901e50b0344e8f264c634ab8c0fa397e8d77c1be0a2e4c121be9147aac65e9e96d0eb5685d', '2026-11-10 15:41:30');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `articles_users` (`author_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nickname` (`nickname`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
