-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 08-06-2026 a las 16:46:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sap_lite`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `producto_id` varchar(10) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `descuento` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `pago` varchar(100) DEFAULT 'No Especificado',
  `canal_pago` varchar(50) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente`, `producto_id`, `cantidad`, `descuento`, `total`, `pago`, `canal_pago`, `fecha_registro`, `status`) VALUES
(1, 'Planta Manufactura Interna (Estructuras Alum. Serie A)', 'PROD001', 5, 0, 741.00, 'Imputación Interna CO', '', '2026-06-08 04:11:39', 'En Proceso'),
(2, 'Planta Manufactura Interna (Estructuras Alum. Serie A)', 'PROD003', 5, 0, 741.00, 'Imputación Interna CO', '', '2026-06-08 04:11:55', 'En Proceso'),
(3, 'Planta Manufactura Interna (Estructuras Alum. Serie A)', 'PROD007', 5, 0, 741.00, 'Imputación Interna CO', '', '2026-06-08 04:11:58', 'En Proceso'),
(4, 'Planta Manufactura Interna (Estructuras Alum. Serie A)', 'PROD008', 5, 0, 741.00, 'Imputación Interna CO', '', '2026-06-08 04:12:02', 'En Proceso'),
(5, 'Planta Manufactura Interna (Estructuras Alum. Serie A)', 'PROD007', 5, 0, 741.00, 'Imputación Interna CO', '', '2026-06-08 04:12:11', 'En Proceso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produccion`
--

CREATE TABLE `produccion` (
  `id` int(11) NOT NULL,
  `lote_nombre` varchar(100) NOT NULL,
  `producto_id` varchar(10) NOT NULL,
  `cantidad_reducida` int(11) NOT NULL,
  `fecha_produccion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `produccion`
--

INSERT INTO `produccion` (`id`, `lote_nombre`, `producto_id`, `cantidad_reducida`, `fecha_produccion`) VALUES
(1, 'Lote_Vigas_H_01', 'PROD005', 15, '2026-06-08 03:52:29'),
(2, 'Lote_Planchas_Alum_02', 'PROD001', 25, '2026-06-08 03:52:29'),
(3, 'Lote_Pernos_AltaRes_03', 'PROD002', 50, '2026-06-08 03:52:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ubicacion` varchar(50) NOT NULL,
  `control_qm` varchar(20) NOT NULL,
  `stock` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `ubicacion`, `control_qm`, `stock`, `estado`) VALUES
('PROD001', 'Planchas Alum. Estructural', 'Pasillo A - Rack 2', 'Aprobado Lote 1', 115, 'Stock Alto'),
('PROD002', 'Pernos Alta Resistencia 3/4', 'Pasillo B - Casillero 5', 'Retenido Lote 4', 7, 'Crítico'),
('PROD003', 'Tubos PVC Presión 2\"', 'Pasillo C - Rack 1', 'Aprobado Lote 2', 80, 'Reabastecer'),
('PROD004', 'Cable Cobre Multipolo 4x10', 'Pasillo A - Bobina 3', 'En Inspección', 14, 'Crítico'),
('PROD005', 'Vigas Acero Perfil H', 'Patio Central - Zona Norte', 'Aprobado Lote 1', 40, 'Reabastecer'),
('PROD006', 'Resina Epóxica Industrial', 'Zona Climatizada - Gaveta 1', 'Aprobado Lote 3', 150, 'Estable'),
('PROD007', 'Planchas Cobre Electrolítico', 'Pasillo A - Rack 4', 'Aprobado Lote 2', 85, 'Reabastecer'),
('PROD008', 'Bridas de Acero Forjado 4\"', 'Pasillo B - Casillero 2', 'En Inspección', 60, 'Reabastecer'),
('PROD009', 'Electrodos de Soldadura 7018', 'Pasillo C - Estante 3', 'Aprobado Lote 5', 200, 'Estable'),
('PROD010', 'Pintura Anticorrosiva Base', 'Zona Inflamables - Bodega A', 'Aprobado Lote 1', 18, 'Reabastecer');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `rol`) VALUES
(1, 'Nelson', 'sap123', 'Admin'),
(2, 'Marcos_Logistica', 'sap123', 'MM_Operario'),
(3, 'Adriana_Finanzas', 'sap123', 'FICO_Analista');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `produccion`
--
ALTER TABLE `produccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `produccion`
--
ALTER TABLE `produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `produccion`
--
ALTER TABLE `produccion`
  ADD CONSTRAINT `produccion_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
