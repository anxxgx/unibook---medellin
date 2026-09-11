-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-09-2026 a las 23:23:58
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `unibook`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `comprador_id` int(11) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `metodo_pago` enum('pse','nequi','bancolombia','daviplata') NOT NULL,
  `estado` enum('pendiente','vendida','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_compra` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `libro_id`, `comprador_id`, `vendedor_id`, `precio`, `metodo_pago`, `estado`, `fecha_compra`, `fecha_actualizacion`) VALUES
(1, 8, 16, 13, '120000.00', 'pse', 'pendiente', '2026-09-11 14:33:05', NULL),
(2, 7, 16, 12, '200000.00', 'bancolombia', 'cancelada', '2026-09-11 14:34:15', '2026-09-11 15:53:14'),
(3, 1, 16, 12, '20.00', 'nequi', '', '2026-09-11 14:43:14', '2026-09-11 15:52:17'),
(4, 7, 16, 12, '200000.00', 'pse', 'pendiente', '2026-09-11 15:58:31', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `editorial` varchar(100) DEFAULT NULL,
  `universidad` varchar(100) DEFAULT NULL,
  `carrera` varchar(100) DEFAULT NULL,
  `semestre` varchar(50) DEFAULT NULL,
  `materia` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `estado` enum('Nuevo','Usado') DEFAULT 'Usado',
  `tipo_publicacion` enum('Venta','Intercambio','Prestamo') DEFAULT 'Venta',
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL,
  `estado_disponibilidad` enum('disponible','reservado','vendido','intercambiado') NOT NULL DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `autor`, `editorial`, `universidad`, `carrera`, `semestre`, `materia`, `descripcion`, `precio`, `estado`, `tipo_publicacion`, `imagen`, `fecha_publicacion`, `usuario_id`, `estado_disponibilidad`) VALUES
(1, 'Harry culo', 'melany', 'd1', 'upb', 'contabilidad', '2', 'Ingles', 'usado-nuevo', '20.00', 'Nuevo', 'Venta', NULL, '2026-08-12 19:51:07', 12, 'vendido'),
(2, 'Nini pupu', 'Camila', 'el ara', 'sena', 'programacion', '5', 'logica', 'usado', '20.00', 'Usado', 'Prestamo', NULL, '2026-08-12 20:45:00', 12, 'disponible'),
(3, 'Angi dueña de valery', 'Angi', 'Privado', 'upb', 'programacion de sofware', '8', 'logica', 'casi nuevo', '20.00', 'Usado', 'Prestamo', NULL, '2026-08-12 20:50:48', 12, 'disponible'),
(4, 'Jimeenis', 'Valesca', 'Privado', 'upb', 'contabilidad', '5', 'Ingles', 'Libro lindo', '17.00', 'Nuevo', 'Intercambio', 'assets/uploads/1786568461_procsio.PNG', '2026-08-12 21:01:01', 12, 'disponible'),
(5, 'Jimeenis', 'Valesca', 'Privado', 'upb', 'contabilidad', '5', 'Ingles', 'Libro lindo', '17.00', 'Nuevo', 'Intercambio', 'assets/uploads/1786568582_procsio.PNG', '2026-08-12 21:03:02', 12, 'disponible'),
(6, 'Jimeenis', 'Valesca', 'Privado', 'upb', 'contabilidad', '12', 'Ingles', 'lindito', '3.00', 'Nuevo', 'Intercambio', 'assets/uploads/1786568629_procsio.PNG', '2026-08-12 21:03:49', 12, 'disponible'),
(7, 'Maryi', 'Jimena', 'Privado', 'Pascual Bravo', 'Gastronomia', '8', 'comida', 'Rico magi de maryi', '200000.00', 'Nuevo', 'Venta', 'assets/uploads/1786736514_Captura-de-pantalla-2025-05-28-121939.png', '2026-08-14 19:41:54', 12, 'reservado'),
(8, 'Aplicaciones de las funciones Algebraicas', 'Carlos Javier Rojas Alvarez', 'no se ', 'upb', 'matematicas', '12', 'logica', 'es lindo', '120000.00', 'Nuevo', 'Venta', 'assets/uploads/1786740769_libroMATE.jpg', '2026-08-14 20:52:49', 13, 'reservado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Estudiante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `rol_id` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `correo`, `password`, `telefono`, `foto`, `fecha_registro`, `estado`, `rol_id`) VALUES
(12, 'Angimar', 'Gonzalez', 'angimargonzalez154@gmail.com', '$2y$10$6agz.X/H6qF7r8USzgYP6uWjsEFKBd3p.10ju0eHnN6T14sRQP3EG', NULL, NULL, '2026-08-12 19:33:07', 'activo', 2),
(13, 'Angimar', 'Carolina', 'angimarcarolina06@gmail.com', '$2y$10$JZT/vfA12sl6lJTy0yrp1OsWzgm9guzyrdIYZCbj2XRoxtRbSRS/m', NULL, NULL, '2026-08-14 20:47:20', 'activo', 2),
(14, 'angi', 'gonzalez', 'angi1909@gmail.com', '$2y$10$Yse543MFlnEvgyjJZVatNOIPT5GmCmx2m4EBzsX67VcbuvJPoxCVq', NULL, NULL, '2026-08-19 19:44:29', 'activo', 2),
(16, 'cristian', 'morales', 'cris@gmail.com', '$2y$10$dYGynFCi5rGmT3xI6oO1YOGD8eYJyV21WkDVSkSVHlFA0KoV0qD3W', NULL, NULL, '2026-09-02 20:10:21', 'activo', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compras_libro` (`libro_id`),
  ADD KEY `idx_compras_comprador` (`comprador_id`),
  ADD KEY `idx_compras_vendedor` (`vendedor_id`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `libros`
--
ALTER TABLE `libros`
  ADD CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
