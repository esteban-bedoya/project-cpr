-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 08-12-2025 a las 19:09:39
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `project-cpr`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` tinyint(4) NOT NULL CHECK (`rol` between 1 and 3),
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `correo` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `documento`, `username`, `password`, `rol`, `estado`, `correo`, `telefono`, `remember_token`) VALUES
(1, '1000660615', 'Esteban Bedoya', '$2y$10$jG5m8YFKxQrZcWAnorl0LOG3mKAzjVEOan9pY7XlH6SR1.dPuYALa', 1, 1, 'esteban@gmail.com', '3246870343', '43c71ec1632d913c80599b8024a5eab5ce813886f37e1d45e1f8eeb2ed9c7663'),
(2, '13453564', 'Ronaldo Anaya', '$2y$10$lfVZ93Yiu.A4HKAziYdQROK7Nnu92hITM.FEans/0at7AtRTTVK0m', 2, 1, 'ronaldo@gmail.com', '3016490549', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `documento_unique` (`documento`),
  ADD KEY `remember_token` (`remember_token`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1013341538;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
