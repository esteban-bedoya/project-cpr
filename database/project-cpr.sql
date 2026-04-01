-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 16-02-2026 a las 18:47:30
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
  `radicado_sena` varchar(10) DEFAULT NULL,
  `asunto` varchar(200) NOT NULL,
  `detalles` text NOT NULL,
  `estado` enum('Atendido','No atendido','Pendiente') DEFAULT 'Pendiente',
  `asignado_a` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `casos`
--

INSERT INTO `casos` (`id`, `numero_caso`, `tipo_caso_id`, `tipo_proceso_id`, `radicado_sena`, `asunto`, `detalles`, `estado`, `asignado_a`, `fecha_creacion`, `fecha_cierre`) VALUES
(34, 'C-000001', 1, 2, '202020', 'Asunto del caso CAMBIO', 'Detalles del caso CAMBIO', 'No atendido', 1013341545, '2026-02-16 08:42:32', '2026-02-16 08:42:57'),
(35, 'C-000035', 2, 2, '303444', 'Evaluación de instructores terribles', 'Se evalúan instructores... terribles', 'Pendiente', 1013341545, '2026-02-16 08:55:13', '2026-02-19 23:59:59'),
(36, 'C-000036', 2, 3, '303030', 'Se denuncia convivencia', 'Esto es horrible, ayuda', 'Pendiente', 1013341545, '2026-02-16 09:28:46', '2026-02-17 23:59:59'),
(37, 'C-000037', 2, 2, '24324', 'hola', 'hola', 'Atendido', 1013341545, '2026-02-16 10:39:51', '2026-02-16 12:09:17');

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
-- Estructura de tabla para la tabla `casos_historial_campos`
--

CREATE TABLE `casos_historial_campos` (
  `id` int(11) NOT NULL,
  `caso_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `campo` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `motivo` varchar(30) DEFAULT 'manual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `casos_historial_campos`
--

INSERT INTO `casos_historial_campos` (`id`, `caso_id`, `usuario_id`, `campo`, `valor_anterior`, `valor_nuevo`, `fecha`, `motivo`) VALUES
(8, 34, 1013341545, 'radicado_sena', '101010', '202020', '2026-02-16 08:46:24', 'manual'),
(9, 34, 1013341545, 'asunto', 'Asunto del caso', 'Asunto del caso CAMBIO', '2026-02-16 08:46:53', 'manual'),
(10, 34, 1013341545, 'detalles', 'Detalles del caso', 'Detalles del caso CAMBIO', '2026-02-16 08:46:53', 'manual'),
(11, 35, 1013341545, 'asunto', 'Evaluación de instructores', 'Evaluación de instructores MALOS', '2026-02-16 08:59:43', 'manual'),
(12, 35, 1013341545, 'detalles', 'Se evaluan instructores...', 'Se evaluan instructores... MALOS', '2026-02-16 08:59:43', 'manual'),
(13, 35, 1013341545, 'radicado_sena', '202020', '202010', '2026-02-16 08:59:43', 'manual'),
(14, 35, 1013341545, 'asunto', 'Evaluación de instructores MALOS', 'Evaluación de instructores buenos', '2026-02-16 09:04:30', 'manual'),
(15, 35, 1013341545, 'detalles', 'Se evaluan instructores... MALOS', 'Se evalúan instructores... buenos', '2026-02-16 09:04:30', 'manual'),
(16, 35, 1013341545, 'radicado_sena', '202010', '303030', '2026-02-16 09:04:30', 'manual'),
(17, 35, 1013341545, 'asunto', 'Evaluación de instructores buenos', 'Evaluación de instructores terribles', '2026-02-16 09:15:46', 'manual'),
(18, 35, 1013341545, 'detalles', 'Se evalúan instructores... buenos', 'Se evalúan instructores... terribles', '2026-02-16 09:15:46', 'manual'),
(19, 35, 1013341545, 'radicado_sena', '303030', '303444', '2026-02-16 09:15:46', 'manual'),
(20, 35, 1013341545, 'fecha_cierre', '2026-02-17 23:59:59', '2026-02-18 23:59:59', '2026-02-16 09:15:56', 'manual'),
(21, 35, 1013341545, 'fecha_cierre', '2026-02-18 23:59:59', '2026-02-19 23:59:59', '2026-02-16 09:18:55', 'manual'),
(22, 36, 1013341545, 'radicado_sena', '404040', '404030', '2026-02-16 09:29:06', 'manual'),
(23, 36, 1013341545, 'fecha_cierre', '2026-02-16 23:59:59', '2026-02-18 23:59:59', '2026-02-16 09:29:15', 'manual'),
(24, 36, 1013341545, 'fecha_cierre', '2026-02-18 23:59:59', '2026-02-27 23:59:59', '2026-02-16 09:29:42', 'manual'),
(25, 36, 1013341545, 'radicado_sena', '404030', '303030', '2026-02-16 09:30:09', 'manual'),
(26, 36, 1013341545, 'fecha_cierre', '2026-02-16 09:34:17', '2026-02-19 23:59:59', '2026-02-16 09:39:58', 'manual'),
(27, 36, 1013341545, 'fecha_cierre', '2026-02-16 10:05:43', '2026-02-18 23:59:59', '2026-02-16 10:11:54', 'manual'),
(28, 36, 1013341545, 'fecha_cierre', '2026-02-16 10:23:37', '2026-02-18 23:59:59', '2026-02-16 10:24:50', 'manual'),
(29, 36, 1013341545, 'fecha_cierre', '2026-02-16 10:25:32', '2026-02-18 23:59:59', '2026-02-16 10:28:30', 'manual'),
(30, 36, 1013341545, 'fecha_cierre', '2026-02-18 23:59:59', '2026-02-20 23:59:59', '2026-02-16 10:30:16', 'manual'),
(31, 36, 1013341545, 'fecha_cierre', '2026-02-16 16:32:10', '2026-02-26 23:59:59', '2026-02-16 10:32:26', 'manual'),
(32, 36, 1013341545, 'fecha_cierre', '2026-02-16 10:32:53', '2026-02-26 23:59:59', '2026-02-16 10:33:07', 'manual'),
(33, 36, 1013341545, 'fecha_cierre', '2026-02-16 10:35:59', '2026-02-17 23:59:59', '2026-02-16 10:39:05', 'manual'),
(34, 37, 1013341545, 'fecha_cierre', '2026-02-16 23:59:59', '2026-02-17 23:59:59', '2026-02-16 10:41:30', 'manual'),
(35, 37, 1013341545, 'fecha_cierre', '2026-02-17 23:59:59', '2026-02-16 23:59:59', '2026-02-16 10:41:39', 'manual'),
(36, 37, 1013341545, 'fecha_cierre', '2026-02-16 10:43:59', '2026-02-19 23:59:59', '2026-02-16 10:43:08', 'manual'),
(37, 37, 1013341545, 'fecha_cierre', '2026-02-19 23:59:59', '2026-02-16 23:59:59', '2026-02-16 10:43:14', 'manual'),
(38, 37, 1013341545, 'fecha_cierre', '2026-02-16 10:45:59', '2026-02-19 23:59:59', '2026-02-16 10:44:00', 'manual'),
(39, 37, 1013341545, 'fecha_cierre', '2026-02-16 10:46:59', '2026-02-19 23:59:59', '2026-02-16 10:50:57', 'manual'),
(40, 37, 1013341545, 'fecha_cierre', '2026-02-16 10:52:02', '2026-02-26 23:59:59', '2026-02-16 10:53:31', 'manual'),
(41, 37, 1013341545, 'fecha_cierre', '2026-02-26 23:59:59', '2026-02-16 23:59:59', '2026-02-16 10:53:47', 'manual'),
(42, 37, 1013341545, 'fecha_cierre', '2026-02-16 23:59:59', '2026-02-20 23:59:59', '2026-02-16 10:54:49', 'manual'),
(43, 37, 1013341545, 'fecha_cierre', '2026-02-20 23:59:59', '2026-02-16 23:59:59', '2026-02-16 10:55:15', 'manual'),
(44, 37, 1013341545, 'fecha_cierre', '2026-02-16 10:59:59', '2026-02-17 23:59:59', '2026-02-16 10:57:45', 'manual'),
(45, 37, 1013341545, 'fecha_cierre', '2026-02-16 11:05:59', '2026-02-16 17:05:00', '2026-02-16 11:01:45', 'manual'),
(46, 37, 1013341545, 'fecha_cierre', '2026-02-16 11:04:00', '2026-02-17 23:59:59', '2026-02-16 11:17:29', 'manual'),
(47, 37, 1013341545, 'fecha_cierre', '2026-02-16 11:19:00', '2026-02-17 23:59:59', '2026-02-16 11:19:13', 'manual'),
(48, 37, 1013341545, 'fecha_cierre', '2026-02-16 11:24:00', '2026-02-17 23:59:59', '2026-02-16 11:26:22', 'manual'),
(49, 37, 1013341545, 'fecha_cierre', '2026-02-16 12:00:00', '2026-02-26 23:59:59', '2026-02-16 11:40:44', 'manual'),
(50, 37, 1013341545, 'fecha_cierre', '2026-02-16 11:44:01', '2026-02-18 23:59:59', '2026-02-16 11:44:14', 'manual'),
(51, 37, 1013341545, 'fecha_cierre', '2026-02-18 23:59:59', '2026-02-16 17:46:39', '2026-02-16 11:46:39', 'auto_estado'),
(52, 37, 1013341545, 'fecha_cierre', '2026-02-16 17:46:39', '2026-02-19 23:59:59', '2026-02-16 11:49:12', 'manual'),
(53, 37, 1013341545, 'fecha_cierre', '2026-02-19 23:59:59', '2026-02-16 17:49:26', '2026-02-16 11:49:26', 'auto_estado'),
(54, 37, 1013341545, 'fecha_cierre', '2026-02-16 17:49:26', '2026-02-19 23:59:59', '2026-02-16 11:49:52', 'manual'),
(55, 37, 1013341545, 'fecha_cierre', '2026-02-16 11:54:59', '2026-02-25 23:59:59', '2026-02-16 12:04:56', 'manual'),
(56, 37, 1013341545, 'fecha_cierre', '2026-02-16 12:08:46', '2026-02-27 23:59:59', '2026-02-16 12:09:06', 'manual');

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
(77, 34, 1013341545, 'Cambio de estado de Pendiente a Atendido', '2026-02-16 08:42:57'),
(78, 34, 1013341545, 'Cambio de estado de Atendido a Pendiente', '2026-02-16 08:43:05'),
(79, 36, 1013341545, 'Cambio de estado de Pendiente a No atendido', '2026-02-16 09:31:53'),
(80, 36, 1013341545, 'Cambio de estado de No atendido a Atendido', '2026-02-16 09:32:11'),
(81, 36, 1013341545, 'Cambio de estado de Atendido a No atendido', '2026-02-16 09:34:00'),
(82, 36, 1013341545, 'Cambio de estado de No atendido a Atendido', '2026-02-16 09:34:17'),
(83, 36, 1013341545, 'Cambio de tipo de proceso de Convivencia a Ropa de trabajo', '2026-02-16 09:34:59'),
(84, 36, 1013341545, 'Cambio de tipo de caso de Denuncia a Derecho de petición', '2026-02-16 09:35:06'),
(85, 36, 1013341545, 'Cambio de tipo de caso de Derecho de petición a Solicitud', '2026-02-16 09:43:59'),
(86, 36, 1013341545, 'Cambio de estado de Atendido a Pendiente', '2026-02-16 09:47:01'),
(87, 36, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:00:00'),
(88, 36, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:00:09'),
(89, 36, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:00:16'),
(90, 36, 1013341545, 'Cambio de estado de No atendido a Atendido', '2026-02-16 10:01:45'),
(91, 36, 1013341545, 'Cambio de estado de No atendido a Atendido', '2026-02-16 10:05:43'),
(92, 36, 1013341545, 'Cambio de estado de Atendido a Pendiente', '2026-02-16 10:06:17'),
(93, 36, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:06:25'),
(94, 36, 1013341545, 'Cambio de estado de No atendido a Atendido', '2026-02-16 10:23:37'),
(95, 36, 1013341545, 'Cambio de estado de Atendido a No atendido', '2026-02-16 10:24:13'),
(96, 36, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:24:50'),
(97, 36, 1013341545, 'Cambio de estado de Pendiente a No atendido', '2026-02-16 10:25:03'),
(98, 36, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:25:15'),
(99, 36, 1013341545, 'Cambio de estado de Pendiente a No atendido', '2026-02-16 10:25:26'),
(100, 36, 1013341545, 'Cambio de estado de No atendido a Atendido', '2026-02-16 10:25:32'),
(101, 36, 1013341545, 'Cambio de estado de Atendido a Pendiente', '2026-02-16 10:28:30'),
(102, 36, 1013341545, 'Cambio de estado de Pendiente a No atendido', '2026-02-16 10:32:10'),
(103, 36, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:32:26'),
(104, 36, 1013341545, 'Cambio de estado de Pendiente a Atendido', '2026-02-16 10:32:53'),
(105, 36, 1013341545, 'Cambio de estado de Atendido a Pendiente', '2026-02-16 10:33:07'),
(106, 36, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:39:05'),
(107, 37, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:43:08'),
(108, 37, 1013341545, 'Cambio de estado de No atendido a Pendiente', '2026-02-16 10:44:00'),
(109, 37, 1013341545, 'Sistema — Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 10:50:57'),
(110, 37, 1013341545, 'Cambio de estado de Pendiente a No atendido', '2026-02-16 10:51:36'),
(111, 37, 1013341545, 'Cambio de estado de No atendido a Atendido', '2026-02-16 10:52:02'),
(112, 37, 1013341545, 'Sistema — Cambio de estado de Atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 10:53:31'),
(113, 37, 1013341545, 'Sistema — Cambio de estado automático de Pendiente a No atendido', '2026-02-16 10:57:05'),
(114, 37, 1013341545, 'Sistema — Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 10:57:45'),
(115, 37, 1013341545, 'Sistema — Cambio de estado automático de Pendiente a No atendido', '2026-02-16 10:58:08'),
(116, 37, 1013341545, 'Sistema — Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 11:01:45'),
(117, 37, 1013341545, 'Sistema — Cambio de estado automático de Pendiente a No atendido', '2026-02-16 11:02:53'),
(118, 37, 1013341545, 'Sistema — Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 11:17:29'),
(119, 37, 1013341545, 'Sistema — Cambio de estado automático de Pendiente a No atendido', '2026-02-16 11:18:05'),
(120, 37, 1013341545, 'Sistema — Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 11:19:13'),
(121, 37, 1013341545, 'Sistema — Cambio de estado automático de Pendiente a No atendido', '2026-02-16 11:19:33'),
(122, 37, 1013341545, 'Sistema — Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 11:26:22'),
(123, 37, 1013341545, 'Sistema — Cambio de estado automático de Pendiente a No atendido', '2026-02-16 11:26:46'),
(124, 37, 1013341545, 'Sistema — Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 11:40:44'),
(125, 37, 1013341545, 'Cambio de estado de Pendiente a No atendido', '2026-02-16 11:41:06'),
(126, 37, 1013341545, 'Cambio de estado de No atendido a Atendido', '2026-02-16 11:44:01'),
(127, 37, 1013341545, 'Sistema — Cambio de estado de Atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 11:44:14'),
(128, 37, 1013341545, 'Cambio de estado de Pendiente a No atendido', '2026-02-16 11:46:39'),
(129, 37, 1013341545, 'Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 11:49:12'),
(130, 37, 1013341545, 'Cambio de estado de Pendiente a No atendido', '2026-02-16 11:49:26'),
(131, 37, 1013341545, 'Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 11:49:52'),
(132, 37, 1013341545, 'Cambio de estado automático de Pendiente a No atendido', '2026-02-16 11:51:39'),
(133, 37, 1013341545, 'Cambio de estado de No atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 12:04:56'),
(134, 37, 1013341547, 'Cambio de estado automático del sistema de Pendiente a No atendido', '2026-02-16 12:07:01'),
(135, 37, 1013341545, 'Cambio de estado de No atendido a Atendido', '2026-02-16 12:08:46'),
(136, 37, 1013341545, 'Cambio de estado de Atendido a Pendiente por fecha de cierre ampliada', '2026-02-16 12:09:06'),
(137, 37, 1013341545, 'Cambio de estado de Pendiente a Atendido', '2026-02-16 12:09:17');

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
(65, 36, 1013341545, 'Hola', NULL, '2026-02-16 09:31:36'),
(66, 36, 1013341545, 'Solo cuando se cambia de forma manual por el usuario, es decir, si estaba en pendiente o atendido y lo paso a no atendido se debe actualizar la fecha de cierre por la actual...', NULL, '2026-02-16 10:30:00');

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
(1013341546, '556433456', 'Veatris Pinzon', '$2y$10$IzcnUNxqFolEqbRIunJZM.6GPm3jVoLiX.7F8aNKK7aVR2JHRsWCe', 2, 2, 'carlos@gmail.com', '324565345', NULL, NULL, '2026-02-10 10:02:16'),
(1013341547, 'SYSTEM-000', 'Sistema', '$2y$10$abcdefghijklmnopqrstuv', 1, 1, 'sistema@cpr.local', NULL, NULL, NULL, '2026-02-16 12:03:51'),
(1013341548, '345564456', 'Juan Mejia', '$2y$10$hSnpV5R8Mw.6LBYGx/gFh.8ZaOR9kGUxVPatua56ozGEbQ1LMlobi', 2, 1, 'juan@gmail.com', '', NULL, NULL, '2026-02-16 12:44:01');

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
-- Indices de la tabla `casos_historial_campos`
--
ALTER TABLE `casos_historial_campos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caso_id` (`caso_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `casos_archivos`
--
ALTER TABLE `casos_archivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `casos_historial_campos`
--
ALTER TABLE `casos_historial_campos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `casos_historial_estado`
--
ALTER TABLE `casos_historial_estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT de la tabla `casos_mensajes`
--
ALTER TABLE `casos_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1013341549;

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
-- Filtros para la tabla `casos_historial_campos`
--
ALTER TABLE `casos_historial_campos`
  ADD CONSTRAINT `casos_historial_campos_ibfk_1` FOREIGN KEY (`caso_id`) REFERENCES `casos` (`id`),
  ADD CONSTRAINT `casos_historial_campos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

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
