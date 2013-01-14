-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 19-01-2012 a las 11:27:50
-- Versión del servidor: 5.1.58
-- Versión de PHP: 5.3.6-13ubuntu3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `dev_dailycontent`
--
CREATE DATABASE `dev_dailycontent` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `dev_dailycontent`;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE IF NOT EXISTS `comentario` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del comentario',
  `post_id` int(11) NOT NULL,
  `autor` varchar(128) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del autor del comentario',
  `email` varchar(128) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Email del autor del comentario',
  `url` varchar(128) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Url o página web del autor del comentario',
  `mensaje` text COLLATE utf8_spanish_ci NOT NULL COMMENT 'Contenido del comentario',
  `ip` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Dirección IP del autor del comentario',
  `estado` int(1) NOT NULL DEFAULT '1' COMMENT 'Estado del comentario',
  `me_gusta` int(11) DEFAULT NULL COMMENT 'Indicador ''Me gusta''',
  `no_me_gusta` int(11) DEFAULT NULL COMMENT 'Indicador ''No me gusta''',
  `comentario_id` int(11) DEFAULT NULL COMMENT 'Comentario padre',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro',
  `modificado_in` datetime DEFAULT NULL COMMENT 'Fecha de modificación',
  PRIMARY KEY (`id`),
  KEY `fk_comentario_post` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla que almacena los diferentes comentarios' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opcion` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `valor` text COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla que contiene algunas configuraciónes del sistema' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto`
--

CREATE TABLE IF NOT EXISTS `contacto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `empresa` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `asunto` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `mensaje` text COLLATE utf8_spanish_ci NOT NULL,
  `ip` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `atendido` varchar(2) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'NO',
  `registrado_at` datetime DEFAULT NULL,
  `modificado_in` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo`
--

CREATE TABLE IF NOT EXISTS `grupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del grupo',
  `grupo_descripcion` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del grupo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla que contiene los grupos de los usuarios' AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `grupo`
--

INSERT INTO `grupo` (`id`, `grupo_descripcion`) VALUES
(1, 'ADMINISTRADOR'),
(2, 'EDITOR'),
(3, 'AUTOR'),
(4, 'COLABORADOR'),
(5, 'LECTOR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del post',
  `usuario_id` int(11) NOT NULL COMMENT 'Identificador del usuario',
  `titulo` varchar(255) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Título del post',
  `contenido` longtext COLLATE utf8_spanish_ci NOT NULL COMMENT 'Contenido del post',
  `slug` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Slug del post',
  `fecha_publicacion` date NOT NULL COMMENT 'Fecha de publicación',
  `hora_publicacion` time DEFAULT NULL COMMENT 'Hora de publicación',
  `resumen` text COLLATE utf8_spanish_ci COMMENT 'Resumen del post',
  `es_pagina` int(1) DEFAULT NULL COMMENT 'Indica si el post es una página',
  `post_id` int(11) DEFAULT NULL COMMENT 'Relación padre del post',
  `estado` int(1) NOT NULL DEFAULT '1' COMMENT 'Estado del post',
  `visibilidad` int(1) NOT NULL DEFAULT '1' COMMENT 'Indica si el post es privado o público',
  `habilitar_comentarios` int(1) NOT NULL COMMENT 'Indica si el post recibe o no comentarios',
  `me_gusta` int(11) DEFAULT '0' COMMENT 'Indicadores de ''Me gusta''',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro',
  `modificado_in` datetime DEFAULT NULL COMMENT 'Fecha de modificación',
  PRIMARY KEY (`id`),
  KEY `fk_post_usuario` (`usuario_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla que almacena los diferentes post y/o páginas del CMS' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `post`
--

INSERT INTO `post` (`id`, `usuario_id`, `titulo`, `contenido`, `slug`, `fecha_publicacion`, `hora_publicacion`, `resumen`, `es_pagina`, `post_id`, `estado`, `visibilidad`, `habilitar_comentarios`, `me_gusta`, `registrado_at`, `modificado_in`) VALUES
(1, 1, 'Primera publicaciÃ³n', '<p>Esta es la primera publicaci&oacute;n</p>', 'primera-publicacion', '2012-01-18', '14:20:40', '<p>Esta es la primera publicaci&oacute;n</p>', NULL, NULL, 2, 1, 0, 0, '2012-01-18 14:20:40', '2012-01-18 14:22:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post_taxonomia`
--

CREATE TABLE IF NOT EXISTS `post_taxonomia` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de la taxonomía y post',
  `post_id` int(11) NOT NULL COMMENT 'Identificador del post',
  `taxonomia_id` int(11) NOT NULL COMMENT 'Identificador de la taxonomía',
  PRIMARY KEY (`id`),
  KEY `fk_post_taxonomia_post` (`post_id`),
  KEY `fk_post_taxonomia_taxonomia` (`taxonomia_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla que relaciona las taxonomías y los post' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `post_taxonomia`
--

INSERT INTO `post_taxonomia` (`id`, `post_id`, `taxonomia_id`) VALUES
(2, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `taxonomia`
--

CREATE TABLE IF NOT EXISTS `taxonomia` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de la taxonomía',
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de la taxonomía',
  `url` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Slug de la taxonomía',
  `tipo` int(11) NOT NULL DEFAULT '1' COMMENT 'Tipo de taxonomía (Categoría o Etiqueta)',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro',
  `modificado_in` datetime DEFAULT NULL COMMENT 'Fecha de modificación',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla que contiene las diferentes taxonomías (categorías y e' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `taxonomia`
--

INSERT INTO `taxonomia` (`id`, `nombre`, `url`, `tipo`, `registrado_at`, `modificado_in`) VALUES
(1, 'Categoria', 'categoria', 1, '2012-01-18 14:13:57', '1969-12-31 19:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del usuario',
  `login` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre de usuario',
  `password` text COLLATE utf8_spanish_ci NOT NULL COMMENT 'Contraseña de acceso',
  `nombre` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre real del usuario',
  `apellido` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Apellido real del usuario',
  `mail` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Correo electrónico del usuario',
  `grupo_id` int(11) NOT NULL COMMENT 'Identificador del grupo',
  `estado` int(1) NOT NULL DEFAULT '1' COMMENT 'Estado del usuario',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro',
  `modificado_in` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Fecha de modificación',
  `user_token` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Llave de la cuenta de Twitter',
  `user_secret` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Llave secreta de la cuenta de Twitter',
  PRIMARY KEY (`id`),
  KEY `fk_usuario_grupo` (`grupo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Tabla que contiene los usuarios del sistema' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `login`, `password`, `nombre`, `apellido`, `mail`, `grupo_id`, `estado`, `registrado_at`, `modificado_in`) VALUES
(1, 'admin', '9a9746d53945a4962910b17a572e68fd', 'Administrador', 'De Sistema', 'postmaster@admin.com', 1, 1, '2012-01-01 00:00:01', NULL);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `fk_comentario_post` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_post_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `post_taxonomia`
--
ALTER TABLE `post_taxonomia`
  ADD CONSTRAINT `fk_post_taxonomia_post` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_post_taxonomia_taxonomia` FOREIGN KEY (`taxonomia_id`) REFERENCES `taxonomia` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupo` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
