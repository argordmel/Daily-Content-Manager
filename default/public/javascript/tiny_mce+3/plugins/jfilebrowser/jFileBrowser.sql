-- phpMyAdmin SQL Dump
-- version 3.0.0
-- http://www.phpmyadmin.net

-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--

-- --------------------------------------------------------

--
-- Table structure for table `archivos`
--

CREATE TABLE IF NOT EXISTS `archivos` (
  `id_archivos` int(11) NOT NULL auto_increment,
  `categoria_archivos` int(11) default NULL,
  `tipo_archivos` varchar(11) default NULL,
  `id_tipo_archivos` int(11) default NULL,
  `nombre_archivos` varchar(255) default NULL,
  `archivo_archivos` varchar(255) default NULL,
  `extension_archivos` varchar(255) default NULL,
  `portada_archivos` int(11) default NULL,
  `fecha_archivos` datetime default NULL,
  PRIMARY KEY  (`id_archivos`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE IF NOT EXISTS `categorias` (
  `cat_id_cat` int(3) NOT NULL auto_increment,
  `tipo_cat` varchar(11) default '1',
  `nivel_cat` int(11) default '1',
  `parent_id_cat` int(10) default '0',
  `name_cat` varchar(255) character set latin1 default '',
  `desc_cat` text character set latin1,
  `status_cat` int(11) default '1',
  `image_cat` varchar(255) character set latin1 default NULL,
  `imagen_activa_cat` int(11) default '1',
  `orden_cat` int(11) default NULL,
  `default_cat` int(11) default NULL,
  `fecha_cat` datetime default NULL,
  `fecha_edit_cat` datetime default NULL,
  `usu_cat` varchar(255) character set latin1 default NULL,
  `usu_edit_cat` varchar(255) character set latin1 default NULL,
  PRIMARY KEY  (`cat_id_cat`),
  UNIQUE KEY `key1` (`name_cat`,`parent_id_cat`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



