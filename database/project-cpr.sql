-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 09-02-2026 a las 16:57:44
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
  `demandante_nombre` varchar(120) NOT NULL,
  `demandante_documento` varchar(20) DEFAULT NULL,
  `demandante_contacto` varchar(120) NOT NULL,
  `asunto` varchar(200) NOT NULL,
  `detalles` text NOT NULL,
  `estado` enum('Atendido','No atendido','Pendiente') DEFAULT 'No atendido',
  `creado_por` int(11) NOT NULL,
  `asignado_a` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `casos`
--

INSERT INTO `casos` (`id`, `numero_caso`, `tipo_caso_id`, `tipo_proceso_id`, `demandante_nombre`, `demandante_documento`, `demandante_contacto`, `asunto`, `detalles`, `estado`, `creado_por`, `asignado_a`, `fecha_creacion`, `fecha_cierre`) VALUES
(5, 'C-000001', 1, 2, 'Kevin Gil Orlas', NULL, '+57 3246765898', 'Presupuesto faltante', 'Se comunica por correo denunciando un presupuesto incoherente', 'Pendiente', 1, 2, '2026-02-02 14:19:53', NULL),
(6, 'C-000006', 2, 1, 'Maria Lopez', NULL, '+57 3102345678', 'Solicitud de uniformes', 'Solicita entrega de uniformes para el personal.', 'Atendido', 2, 2, '2025-12-08 14:19:53', '2026-02-09 09:50:02'),
(7, 'C-000007', 3, 4, 'Juan Torres', NULL, '+57 3009876543', 'Derecho de petición', 'Presenta derecho de petición relacionado con logística de eventos.', 'No atendido', 1, 2, '2025-12-08 14:19:53', NULL),
(8, 'C-000008', 4, 3, 'Ana Perez', NULL, '+57 3001234567', 'Tutela por incumplimiento', 'Reclamo por incumplimiento de formación de empleados.', 'Atendido', 2, 2, '2025-12-08 14:19:53', '2026-02-09 07:08:25'),
(9, 'C-000009', 4, 3, 'Sasha De Jesus', NULL, 'sasha@gmail.com', 'Devolución de dinero', 'Hace unos días compre una camiseta y realmente no la use porque deserte del SENA.\r\n\r\nY ahora resulta que no me quieren devolver el dinero.', 'Pendiente', 2, 1013341538, '2026-02-07 21:38:08', '2026-02-08 14:21:04'),
(10, 'C-000010', 3, 4, 'Roy Jose Naranjo', NULL, '3016490549', 'Imposibilidad de recuperación.', 'Debido a las largas jornadas de formación muchos de los estudiantes no tienen tiempo de presentar recuperación.', 'Atendido', 2, 1013341538, '2026-02-08 10:38:57', '2026-02-08 10:58:31'),
(11, 'C-000011', 1, 2, 'ROY DE JESUS', NULL, '34565467', 'ASUNTO', 'DETALLES DETALLES', 'No atendido', 2, 2, '2026-02-09 10:36:26', NULL),
(12, 'C-000012', 2, 1, 'Jose Jose', '12123434', '345633564', 'Jose Jose reclama', 'Reclamación de José José ...', 'No atendido', 2, 2, '2026-02-09 10:41:40', NULL);

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

--
-- Volcado de datos para la tabla `casos_archivos`
--

INSERT INTO `casos_archivos` (`id`, `caso_id`, `mensaje_id`, `nombre_archivo`, `fecha`) VALUES
(1, 5, 14, 'presupuesto.pdf', '2026-02-06 15:09:59'),
(2, 7, NULL, 'evidencia_tutela.jpg', '2026-02-06 15:09:59'),
(3, 8, 19, 'contrato_formacion.pdf', '2026-02-06 15:09:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casos_asignaciones`
--

CREATE TABLE `casos_asignaciones` (
  `id` int(11) NOT NULL,
  `caso_id` int(11) NOT NULL,
  `comisionado_id` int(11) NOT NULL,
  `asignado_por` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `casos_asignaciones`
--

INSERT INTO `casos_asignaciones` (`id`, `caso_id`, `comisionado_id`, `asignado_por`, `fecha_asignacion`) VALUES
(1, 5, 2, 1, '2026-02-06 15:09:39'),
(2, 6, 2, 1, '2026-02-06 15:09:39'),
(3, 7, 2, 1, '2026-02-06 15:09:39'),
(4, 8, 2, 1, '2026-02-06 15:09:39'),
(5, 9, 2, 2, '2026-02-07 21:38:08'),
(6, 10, 2, 2, '2026-02-08 10:38:57');

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
(5, 5, 1, 'Cambio de estado de No atendido a Pendiente', '2026-02-07 23:07:17'),
(6, 5, 2, 'Cambio de comisionado de Esteban Bedoya a Ronaldo Anaya', '2026-02-07 23:07:17'),
(7, 5, 1, 'Cambio de tipo de proceso de Presupuesto a Logística', '2026-02-07 23:07:17'),
(8, 5, 1, 'Cambio de tipo de caso de Denuncia a Solicitud', '2026-02-07 23:07:17'),
(9, 8, 2, 'Cambio de tipo de proceso de Formación a Presupuesto', '2026-02-07 23:32:10'),
(10, 8, 2, 'Cambio de tipo de caso de Tutela a Denuncia', '2026-02-07 23:32:10'),
(11, 6, 2, 'Cambio de tipo de proceso de Uniformes a Presupuesto', '2026-02-07 23:32:59'),
(12, 6, 2, 'Cambio de tipo de caso de Solicitud a Denuncia', '2026-02-07 23:32:59'),
(15, 9, 2, 'Cambio de estado de No atendido a Pendiente', '2026-02-08 08:16:23'),
(16, 9, 2, 'Cambio de estado de No atendido a Atendido', '2026-02-08 08:22:59'),
(17, 9, 2, 'Cambio de estado de No atendido a Atendido', '2026-02-08 08:34:57'),
(18, 9, 2, 'Cambio de tipo de proceso de Uniformes a Presupuesto', '2026-02-08 08:35:07'),
(19, 9, 2, 'Cambio de tipo de caso de Solicitud a Denuncia', '2026-02-08 08:35:07'),
(20, 9, 2, 'Asignó a Ana Perez como comisionado (antes: Ronaldo Anaya)', '2026-02-08 08:35:26'),
(21, 9, 2, 'Asignó a Ronaldo Anaya como comisionado (antes: Ana Perez)', '2026-02-08 08:39:19'),
(22, 9, 1, 'Cambio de estado de Atendido a Pendiente', '2026-02-08 08:41:24'),
(23, 9, 1, 'Cambio de tipo de proceso de Presupuesto a Uniformes', '2026-02-08 08:41:35'),
(24, 9, 1, 'Cambio de tipo de caso de Denuncia a Solicitud', '2026-02-08 08:41:35'),
(25, 9, 2, 'Cambio de estado de Pendiente a Atendido', '2026-02-08 09:06:53'),
(26, 9, 2, 'Cambio de estado de Atendido a Pendiente', '2026-02-08 09:54:36'),
(27, 9, 2, 'Cambio de tipo de proceso de Uniformes a Formación', '2026-02-08 09:54:36'),
(28, 9, 2, 'Cambio de tipo de caso de Solicitud a Tutela', '2026-02-08 09:54:36'),
(29, 9, 2, 'Cambio de estado de Pendiente a No atendido', '2026-02-08 09:54:52'),
(30, 9, 2, 'Cambio de tipo de proceso de Formación a Presupuesto', '2026-02-08 09:55:03'),
(31, 9, 2, 'Cambio de tipo de caso de Tutela a Denuncia', '2026-02-08 09:55:03'),
(32, 10, 2, 'Asignó a Ana Perez como comisionado (antes: Ronaldo Anaya)', '2026-02-08 10:45:16'),
(33, 10, 2, 'Cambio de estado de No atendido a Pendiente', '2026-02-08 10:47:34'),
(34, 10, 2, 'Cambio de tipo de proceso de Formación a Logística', '2026-02-08 10:48:36'),
(35, 10, 2, 'Cambio de tipo de caso de Tutela a Derecho de petición', '2026-02-08 10:48:36'),
(36, 10, 1, 'Cambio de estado de Pendiente a Atendido', '2026-02-08 10:58:31'),
(37, 9, 2, 'Cambio de estado de No atendido a Pendiente', '2026-02-08 14:17:55'),
(38, 9, 2, 'Cambio de tipo de proceso de Presupuesto a Logística', '2026-02-08 14:18:11'),
(39, 9, 2, 'Cambio de tipo de caso de Denuncia a Derecho de petición', '2026-02-08 14:18:11'),
(40, 9, 2, 'Cambio de estado de Pendiente a Atendido', '2026-02-08 14:21:04'),
(41, 9, 2, 'Cambio de tipo de proceso de Logística a Formación', '2026-02-08 17:00:55'),
(42, 9, 2, 'Cambio de tipo de caso de Derecho de petición a Tutela', '2026-02-08 17:00:55'),
(43, 9, 2, 'Asignó a Ana Perez como comisionado (antes: Ronaldo Anaya)', '2026-02-09 06:53:23'),
(44, 9, 2, 'Cambio de estado de Atendido a Pendiente', '2026-02-09 06:53:23'),
(45, 8, 2, 'Cambio de estado de Pendiente a Atendido', '2026-02-09 07:08:25'),
(46, 6, 2, 'Cambio de estado de No atendido a Atendido', '2026-02-09 09:50:02'),
(47, 7, 2, 'Cambio de estado de Atendido a No atendido', '2026-02-09 09:52:56');

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
(13, 5, 1, 'Se comunica Kevin Gil Orlas por correo denunciando un presupuesto incoherente.', NULL, '2025-12-08 14:23:03'),
(14, 5, 2, 'Revisado el caso, pendiente de validación de documentos adjuntos.', NULL, '2025-12-08 14:23:03'),
(15, 6, 2, 'Maria Lopez solicita entrega de uniformes para el personal.', NULL, '2025-12-08 14:23:03'),
(16, 6, 1, 'Se recibió solicitud, coordinando con logística.', NULL, '2025-12-08 14:23:03'),
(17, 7, 1, 'Juan Torres presenta derecho de petición relacionado con logística de eventos.', NULL, '2025-12-08 14:23:03'),
(18, 8, 2, 'Ana Perez reclama incumplimiento de formación de empleados.', NULL, '2025-12-08 14:23:03'),
(19, 8, 1, 'Se está revisando el caso y coordinando acciones.', NULL, '2025-12-08 14:23:03'),
(20, 9, 2, 'Hola hola', NULL, '2026-02-08 08:40:16'),
(21, 9, 2, 'Actualización del caso!', NULL, '2026-02-08 08:40:32'),
(22, 9, 1, 'Hola', NULL, '2026-02-08 08:41:02'),
(23, 9, 2, 'Esto es una prueba', NULL, '2026-02-08 09:54:22'),
(24, 10, 2, 'Se comunica Roy Jose Jose y indica que desea retirar los cargos', NULL, '2026-02-08 10:52:17'),
(25, 10, 2, 'Por lo tanto se justifica el cambio de atendido a pendiente.', NULL, '2026-02-08 10:53:46'),
(26, 10, 2, 'fghrgregreiogreh', NULL, '2026-02-08 10:55:47'),
(27, 10, 2, 'vkjdfgjkerhgjkrh', NULL, '2026-02-08 10:56:02'),
(28, 10, 1, 'hoklwufk', NULL, '2026-02-08 10:58:16'),
(29, 9, 2, 'Hola hola hola', NULL, '2026-02-08 14:21:18'),
(30, 9, 2, 'hi', NULL, '2026-02-08 15:29:40'),
(31, 9, 2, 'Hola', 'caso_9_1770583786.png', '2026-02-08 15:49:46'),
(32, 9, 2, 'Hola', 'caso_9_1770584047.jpeg', '2026-02-08 15:54:07'),
(33, 9, 2, 'no se', NULL, '2026-02-08 15:55:33'),
(34, 9, 2, '.', 'caso_9_1770584154.png', '2026-02-08 15:55:54'),
(35, 9, 2, 'll', 'caso_9_1770584776.png', '2026-02-08 16:06:16'),
(36, 9, 2, 'r', 'caso_9_1770585588.png', '2026-02-08 16:19:48'),
(37, 9, 2, 't', 'caso_9_1770585600.png', '2026-02-08 16:20:00'),
(38, 9, 2, 'Hola', NULL, '2026-02-08 16:31:44'),
(39, 9, 2, '', 'caso_9_1770586705.png', '2026-02-08 16:38:25'),
(40, 9, 2, '', 'caso_9_1770638238.jpeg', '2026-02-09 06:57:18'),
(41, 8, 2, 'Hola', NULL, '2026-02-09 07:08:21'),
(42, 6, 2, 'Hola hola esto es una foto para que me transfiera al Nequi...', 'caso_6_1770648635.jpeg', '2026-02-09 09:50:35'),
(43, 12, 2, 'Hola dios santo!', 'caso_12_1770651844.jpeg', '2026-02-09 10:44:04'),
(44, 12, 2, 'alo', NULL, '2026-02-09 10:44:10'),
(45, 12, 2, '', 'caso_12_1770651859.jpeg', '2026-02-09 10:44:19');

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
  `tipo_caso_id` int(11) NOT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_proceso`
--

INSERT INTO `tipos_proceso` (`id`, `nombre`, `tipo_caso_id`, `estado`) VALUES
(1, 'Uniformes', 2, 1),
(2, 'Presupuesto', 1, 1),
(3, 'Formación', 4, 1),
(4, 'Logística', 3, 1);

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
(1013341538, '23456789', 'Ana Perez', '$2y$10$hashdummy1', 1, 1, 'ana.perez@gmail.com', '3001234567', NULL, NULL, '2026-02-06 14:58:23'),
(1013341539, '333333', 'Luis Carlos Morales', '$2y$10$hashdummy2', 2, 2, 'luis.carlos@gmail.com', '3246870343', NULL, NULL, '2026-02-06 14:58:23'),
(1013341540, '45678901', 'Camila Rios', '$2y$10$H0A1jCYlhT/MRWCo0d20w.uA9qGxozF7qUI..oAGyH5OQZpp.JMem', 2, 2, 'camila.rios@gmail.com', '3207654321', NULL, NULL, '2026-02-06 14:58:23');

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
  ADD KEY `creado_por` (`creado_por`),
  ADD KEY `fk_asignado_a` (`asignado_a`);

--
-- Indices de la tabla `casos_archivos`
--
ALTER TABLE `casos_archivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caso_id` (`caso_id`),
  ADD KEY `mensaje_id` (`mensaje_id`);

--
-- Indices de la tabla `casos_asignaciones`
--
ALTER TABLE `casos_asignaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caso_id` (`caso_id`),
  ADD KEY `comisionado_id` (`comisionado_id`),
  ADD KEY `asignado_por` (`asignado_por`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tipos_proceso_tipo_caso` (`tipo_caso_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `casos_archivos`
--
ALTER TABLE `casos_archivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `casos_asignaciones`
--
ALTER TABLE `casos_asignaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `casos_historial_estado`
--
ALTER TABLE `casos_historial_estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `casos_mensajes`
--
ALTER TABLE `casos_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `tipos_caso`
--
ALTER TABLE `tipos_caso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipos_proceso`
--
ALTER TABLE `tipos_proceso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1013341542;

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
  ADD CONSTRAINT `casos_ibfk_3` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_asignado_a` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `casos_archivos`
--
ALTER TABLE `casos_archivos`
  ADD CONSTRAINT `casos_archivos_ibfk_1` FOREIGN KEY (`caso_id`) REFERENCES `casos` (`id`),
  ADD CONSTRAINT `casos_archivos_ibfk_2` FOREIGN KEY (`mensaje_id`) REFERENCES `casos_mensajes` (`id`);

--
-- Filtros para la tabla `casos_asignaciones`
--
ALTER TABLE `casos_asignaciones`
  ADD CONSTRAINT `casos_asignaciones_ibfk_1` FOREIGN KEY (`caso_id`) REFERENCES `casos` (`id`),
  ADD CONSTRAINT `casos_asignaciones_ibfk_2` FOREIGN KEY (`comisionado_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `casos_asignaciones_ibfk_3` FOREIGN KEY (`asignado_por`) REFERENCES `usuarios` (`id`);

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
-- Filtros para la tabla `tipos_proceso`
--
ALTER TABLE `tipos_proceso`
  ADD CONSTRAINT `fk_tipos_proceso_tipo_caso` FOREIGN KEY (`tipo_caso_id`) REFERENCES `tipos_caso` (`id`);

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
