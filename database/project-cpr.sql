-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-02-2026 a las 21:03:51
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
-- Estructura de tabla para la tabla `casos`
--

CREATE TABLE `casos` (
  `id` int(11) NOT NULL,
  `numero_caso` varchar(20) NOT NULL,
  `tipo_caso_id` int(11) NOT NULL,
  `tipo_proceso_id` int(11) NOT NULL,
  `asunto` varchar(200) NOT NULL,
  `detalles` text NOT NULL,
  `estado` enum('Atendido','No atendido','Pendiente') DEFAULT 'No atendido',
  `asignado_a` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `casos`
--

INSERT INTO `casos` (`id`, `numero_caso`, `tipo_caso_id`, `tipo_proceso_id`, `asunto`, `detalles`, `estado`, `asignado_a`, `fecha_creacion`, `fecha_cierre`) VALUES
(16, 'C-000001', 4, 3, 'Ropa', 'No recibi la ropa de trabajo', 'No atendido', 1013341545, '2026-02-10 10:57:21', NULL),
(17, 'C-000017', 2, 1, 'Ropa', 'Ropa', 'Atendido', 1013341545, '2026-02-10 10:58:43', '2026-02-10 11:03:45'),
(18, 'C-000018', 1, 2, 'h', 'h', 'No atendido', 1013341545, '2026-02-10 11:25:33', NULL),
(19, 'C-000019', 4, 3, 'hola', 'tjy', 'Pendiente', 2, '2026-02-14 10:46:06', NULL),
(20, 'C-000020', 1, 1, 'hola', 'holq', 'No atendido', 1013341545, '2026-02-15 13:21:44', NULL),
(21, 'C-000021', 3, 9, 'Si', 'no', 'Pendiente', 1013341545, '2026-02-15 14:16:52', NULL),
(22, 'C-000022', 2, 1, 'Asunto', 'Se tienen datos etc...', 'No atendido', 1013341545, '2026-02-15 15:00:25', NULL);

--
-- Disparadores `casos`
--
DELIMITER $$
CREATE TRIGGER `cerrar_caso` BEFORE UPDATE ON `casos` FOR EACH ROW BEGIN
  IF NEW.estado = 'Atendido' AND OLD.estado <> 'Atendido' THEN
    SET NEW.fecha_cierre = CURRENT_TIMESTAMP;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `generar_numero_caso` BEFORE INSERT ON `casos` FOR EACH ROW BEGIN
    SET NEW.numero_caso = CONCAT('C-', LPAD((SELECT IFNULL(MAX(id),0)+1 FROM casos), 6, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casos_archivos`
--

CREATE TABLE `casos_archivos` (
  `id` int(11) NOT NULL,
  `caso_id` int(11) NOT NULL,
  `mensaje_id` int(11) DEFAULT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casos_historial_estado`
--

CREATE TABLE `casos_historial_estado` (
  `id` int(11) NOT NULL,
  `caso_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `casos_historial_estado`
--

INSERT INTO `casos_historial_estado` (`id`, `caso_id`, `usuario_id`, `descripcion`, `fecha`) VALUES
(64, 17, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-10 11:00:17'),
(65, 17, 1013341545, 'Asignó a Carlos Gabriel Torres como comisionado (antes: Marleny Gaviria Ardila)', '2026-02-10 11:00:30'),
(66, 17, 1013341545, 'Cambio de estado de Pendiente a Atendido', '2026-02-10 11:03:45'),
(67, 19, 2, 'Cambio de estado de No atendido a Pendiente', '2026-02-14 12:49:56'),
(68, 21, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-15 14:20:32'),
(69, 21, 1013341545, 'Cambio de tipo de proceso de Evaluación de desempeño laboral a SST', '2026-02-15 14:21:16'),
(70, 21, 1013341545, 'Cambio de tipo de caso de Denuncia a Derecho de petición', '2026-02-15 14:21:16'),
(71, 20, 1013341545, 'Cambio de tipo de caso de Tutela a Solicitud', '2026-02-15 14:58:59'),
(72, 20, 1013341545, 'Cambio de tipo de caso de Solicitud a Tutela', '2026-02-15 14:59:07'),
(73, 20, 1013341545, 'Cambio de tipo de caso de Tutela a Denuncia', '2026-02-15 14:59:10'),
(74, 20, 1013341545, 'Cambio de tipo de proceso de Ropa de trabajo a Bienestar', '2026-02-15 14:59:26'),
(75, 21, 1013341545, 'Cambio de tipo de proceso de SST a Convivencia', '2026-02-15 15:02:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casos_mensajes`
--

CREATE TABLE `casos_mensajes` (
  `id` int(11) NOT NULL,
  `caso_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `casos_mensajes`
--

INSERT INTO `casos_mensajes` (`id`, `caso_id`, `usuario_id`, `mensaje`, `archivo`, `fecha`) VALUES
(55, 17, 1013341545, 'Hable con recursos humanos, el bono esta en tramite.', NULL, '2026-02-10 11:00:00'),
(56, 19, 2, 'Hola', NULL, '2026-02-14 10:52:47'),
(57, 19, 2, '', 'caso_19_1771091384.jpeg', '2026-02-14 12:49:44'),
(58, 21, 1013341545, '', 'caso_21_1771185652.jpeg', '2026-02-15 15:00:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_caso`
--

CREATE TABLE `tipos_caso` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_caso`
--

INSERT INTO `tipos_caso` (`id`, `nombre`) VALUES
(1, 'Denuncia'),
(2, 'Solicitud'),
(3, 'Derecho de petición'),
(4, 'Tutela');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_proceso`
--

CREATE TABLE `tipos_proceso` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_proceso`
--

INSERT INTO `tipos_proceso` (`id`, `nombre`, `estado`) VALUES
(1, 'Bienestar', 0),
(2, 'Evaluación de desempeño laboral', 1),
(3, 'Ropa de trabajo', 1),
(4, 'SST', 0),
(9, 'Convivencia', 1),
(10, 'Clima organizacional', 1),
(11, 'SSEMI (Sistema salarial SENA)', 1);

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
  `remember_token` varchar(255) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `documento`, `username`, `password`, `rol`, `estado`, `correo`, `telefono`, `remember_token`, `creado_por`, `fecha_creacion`) VALUES
(1, '1000660615', 'Esteban Bedoya', '$2y$10$/HCZCM9.yIDMKg4gp9h2X.l7mnBahxu33GpstHNznqgzhVGmysSrS', 1, 1, 'esteban@gmail.com', '3246870343', '43c71ec1632d913c80599b8024a5eab5ce813886f37e1d45e1f8eeb2ed9c7663', NULL, '2026-02-06 14:58:23'),
(2, '13453564', 'Ronaldo Anaya', '$2y$10$omRQbXy3QjdDlSe2l/sAp.R0qgX7ewTMW2mpszUqruWlqYuBSKvRy', 2, 1, 'ronaldo@gmail.com', '3016490549', NULL, NULL, '2026-02-06 14:58:23'),
(1013341538, '23456789', 'Ana Perez', '$2y$10$9AvP63Z8blgSW7HiydAtGusxmGjllZs/8/H6mXB2QIzafoCRihP2a', 2, 1, 'ana@gmail.com', '3001234567', NULL, NULL, '2026-02-06 14:58:23'),
(1013341545, '45678901', 'Marleny Gaviria Ardila', '$2y$10$5mN2r4XFg6P0vuUA4VB1Teh9mOkfF/eIhwCqMkwezTgkjYg6/ZSY6', 2, 1, 'marleny.gaviria@sena.edu.co', '3207654321', NULL, NULL, '2026-02-10 09:37:33'),
(1013341546, '556433456', 'Veatris Pinzon', '$2y$10$IzcnUNxqFolEqbRIunJZM.6GPm3jVoLiX.7F8aNKK7aVR2JHRsWCe', 2, 2, 'carlos@gmail.com', '324565345', NULL, NULL, '2026-02-10 10:02:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_logins`
--

CREATE TABLE `usuarios_logins` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_logins`
--

INSERT INTO `usuarios_logins` (`id`, `usuario_id`, `fecha`, `ip`) VALUES
(1, 1, '2026-02-06 15:10:15', '192.168.1.10'),
(2, 2, '2026-02-06 15:10:15', '192.168.1.20'),
(3, 2, '2026-02-06 15:10:15', '192.168.1.21'),
(4, 1013341538, '2026-02-06 15:10:15', '192.168.1.30');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `casos`
--
ALTER TABLE `casos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_caso` (`numero_caso`),
  ADD KEY `tipo_caso_id` (`tipo_caso_id`),
  ADD KEY `tipo_proceso_id` (`tipo_proceso_id`),
  ADD KEY `fk_asignado_a` (`asignado_a`);

--
-- Indices de la tabla `casos_archivos`
--
ALTER TABLE `casos_archivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caso_id` (`caso_id`),
  ADD KEY `mensaje_id` (`mensaje_id`);

--
-- Indices de la tabla `casos_historial_estado`
--
ALTER TABLE `casos_historial_estado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caso_id` (`caso_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_caso` (`caso_id`);

--
-- Indices de la tabla `casos_mensajes`
--
ALTER TABLE `casos_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caso_id` (`caso_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `tipos_caso`
--
ALTER TABLE `tipos_caso`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipos_proceso`
--
ALTER TABLE `tipos_proceso`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `documento_unique` (`documento`),
  ADD KEY `remember_token` (`remember_token`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `usuarios_logins`
--
ALTER TABLE `usuarios_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logins_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `casos`
--
ALTER TABLE `casos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `casos_archivos`
--
ALTER TABLE `casos_archivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `casos_historial_estado`
--
ALTER TABLE `casos_historial_estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `casos_mensajes`
--
ALTER TABLE `casos_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `tipos_caso`
--
ALTER TABLE `tipos_caso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipos_proceso`
--
ALTER TABLE `tipos_proceso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1013341547;

--
-- AUTO_INCREMENT de la tabla `usuarios_logins`
--
ALTER TABLE `usuarios_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `casos`
--
ALTER TABLE `casos`
  ADD CONSTRAINT `casos_ibfk_1` FOREIGN KEY (`tipo_caso_id`) REFERENCES `tipos_caso` (`id`),
  ADD CONSTRAINT `casos_ibfk_2` FOREIGN KEY (`tipo_proceso_id`) REFERENCES `tipos_proceso` (`id`),
  ADD CONSTRAINT `fk_asignado_a` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `casos_archivos`
--
ALTER TABLE `casos_archivos`
  ADD CONSTRAINT `casos_archivos_ibfk_1` FOREIGN KEY (`caso_id`) REFERENCES `casos` (`id`),
  ADD CONSTRAINT `casos_archivos_ibfk_2` FOREIGN KEY (`mensaje_id`) REFERENCES `casos_mensajes` (`id`);

--
-- Filtros para la tabla `casos_historial_estado`
--
ALTER TABLE `casos_historial_estado`
  ADD CONSTRAINT `casos_historial_estado_ibfk_1` FOREIGN KEY (`caso_id`) REFERENCES `casos` (`id`),
  ADD CONSTRAINT `casos_historial_estado_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `casos_mensajes`
--
ALTER TABLE `casos_mensajes`
  ADD CONSTRAINT `casos_mensajes_ibfk_1` FOREIGN KEY (`caso_id`) REFERENCES `casos` (`id`),
  ADD CONSTRAINT `casos_mensajes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios_logins`
--
ALTER TABLE `usuarios_logins`
  ADD CONSTRAINT `usuarios_logins_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
