-- Время создания: Дек 16 2020 г., 20:52
-- Версия сервера: 5.6.47
-- Версия PHP: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- База данных: `word_art_cinema`
--
CREATE DATABASE IF NOT EXISTS `word_art_cinema` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `word_art_cinema`;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `films`
--

CREATE TABLE `films` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `year` year(4) NOT NULL,
  `description_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `posters`
--

CREATE TABLE `posters` (
  `id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `poster_url` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `raiting`
--

CREATE TABLE `raiting` (
  `id` bigint(20) NOT NULL,
  `position` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD UNIQUE KEY `url` (`url`);

--
-- Индексы таблицы `films`
--
ALTER TABLE `films`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `posters`
--
ALTER TABLE `posters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Posters_fk0` (`film_id`);

--
-- Индексы таблицы `raiting`
--
ALTER TABLE `raiting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Raiting_fk0` (`film_id`),
  ADD KEY `Raiting_fk1` (`category_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `films`
--
ALTER TABLE `films`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `posters`
--
ALTER TABLE `posters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `raiting`
--
ALTER TABLE `raiting`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `posters`
--
ALTER TABLE `posters`
  ADD CONSTRAINT `Posters_fk0` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`);

--
-- Ограничения внешнего ключа таблицы `raiting`
--
ALTER TABLE `raiting`
  ADD CONSTRAINT `Raiting_fk0` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`),
  ADD CONSTRAINT `Raiting_fk1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;
