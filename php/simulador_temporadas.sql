-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-12-2024 a las 13:43:56
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
-- Base de datos: `simulador_temporadas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `carrera_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `escuderias`
--

CREATE TABLE `escuderias` (
  `escuderia_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `escuderias_carreras`
--

CREATE TABLE `escuderias_carreras` (
  `escuderia_id` int(11) NOT NULL,
  `carrera_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pilotos`
--

CREATE TABLE `pilotos` (
  `piloto_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pilotos_escuderias`
--

CREATE TABLE `pilotos_escuderias` (
  `piloto_id` int(11) NOT NULL,
  `escuderia_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`carrera_id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `escuderias`
--
ALTER TABLE `escuderias`
  ADD PRIMARY KEY (`escuderia_id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `escuderias_carreras`
--
ALTER TABLE `escuderias_carreras`
  ADD PRIMARY KEY (`escuderia_id`,`carrera_id`),
  ADD KEY `carrera_id` (`carrera_id`);

--
-- Indices de la tabla `pilotos`
--
ALTER TABLE `pilotos`
  ADD PRIMARY KEY (`piloto_id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pilotos_escuderias`
--
ALTER TABLE `pilotos_escuderias`
  ADD PRIMARY KEY (`piloto_id`,`escuderia_id`),
  ADD KEY `escuderia_id` (`escuderia_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `carrera_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `escuderias`
--
ALTER TABLE `escuderias`
  MODIFY `escuderia_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pilotos`
--
ALTER TABLE `pilotos`
  MODIFY `piloto_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `escuderias_carreras`
--
ALTER TABLE `escuderias_carreras`
  ADD CONSTRAINT `escuderias_carreras_ibfk_1` FOREIGN KEY (`escuderia_id`) REFERENCES `escuderias` (`escuderia_id`),
  ADD CONSTRAINT `escuderias_carreras_ibfk_2` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`carrera_id`);

--
-- Filtros para la tabla `pilotos_escuderias`
--
ALTER TABLE `pilotos_escuderias`
  ADD CONSTRAINT `pilotos_escuderias_ibfk_1` FOREIGN KEY (`piloto_id`) REFERENCES `pilotos` (`piloto_id`),
  ADD CONSTRAINT `pilotos_escuderias_ibfk_2` FOREIGN KEY (`escuderia_id`) REFERENCES `escuderias` (`escuderia_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
