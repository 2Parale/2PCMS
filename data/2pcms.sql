-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 30, 2012 at 10:23 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `2pcms`
--

-- --------------------------------------------------------

--
-- Table structure for table `aff_categories`
--

CREATE TABLE IF NOT EXISTS `aff_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `category` varchar(250) NOT NULL,
  `subcategory` varchar(250) NOT NULL,
  `shop_category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_category_id` (`shop_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aff_feeds`
--

CREATE TABLE IF NOT EXISTS `aff_feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `feed_filename` varchar(200) NOT NULL,
  `feed_url` varchar(200) NOT NULL,
  `feed_desc` text NOT NULL,
  `last_date` datetime NOT NULL,
  `price_format` enum('pricedot','pricecomma') NOT NULL DEFAULT 'pricedot',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aff_feed_source`
--

CREATE TABLE IF NOT EXISTS `aff_feed_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `feed_id` int(11) NOT NULL,
  `import_date` datetime NOT NULL,
  `campaign_name` varchar(300) NOT NULL,
  `aff_category_id` int(11) NOT NULL DEFAULT '0',
  `widget_name` varchar(300) NOT NULL,
  `title` varchar(300) NOT NULL,
  `title_url` varchar(300) NOT NULL,
  `description` text NOT NULL,
  `short_message` text NOT NULL,
  `price` varchar(100) NOT NULL,
  `price_int` double NOT NULL,
  `category` varchar(300) NOT NULL,
  `subcategory` varchar(300) NOT NULL,
  `url` varchar(300) NOT NULL,
  `img_urls` varchar(500) NOT NULL,
  `other_data` text NOT NULL,
  `aff_url` varchar(300) NOT NULL,
  `create_date` datetime NOT NULL,
  `product_active` varchar(10) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `product_id_int` bigint(20) NOT NULL,
  `brand_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aff_category_id` (`aff_category_id`),
  KEY `partner_id` (`partner_id`),
  KEY `product_id_int` (`product_id_int`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `aff_partners`
--

CREATE TABLE IF NOT EXISTS `aff_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(200) NOT NULL,
  `shop_url` varchar(200) NOT NULL,
  `network` varchar(200) NOT NULL,
  `shop_desc` text NOT NULL,
  `cron_sync` tinyint(4) NOT NULL DEFAULT '0',
  `shop_content` text NOT NULL,
  `shop_logo` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `title_url` varchar(250) NOT NULL,
  `meta_desc` varchar(500) NOT NULL,
  `acontent` text NOT NULL,
  `pubdate` datetime NOT NULL,
  `shows` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `article_categories`
--

CREATE TABLE IF NOT EXISTS `article_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(250) NOT NULL,
  `category_url` varchar(250) NOT NULL,
  `article_count` int(11) NOT NULL,
  `shows` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `article_x_products`
--

CREATE TABLE IF NOT EXISTS `article_x_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `shop_product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_x_product` (`article_id`,`shop_product_id`),
  KEY `article_id` (`article_id`),
  KEY `shop_product_id` (`shop_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cron_logs`
--

CREATE TABLE IF NOT EXISTS `cron_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_log` datetime NOT NULL,
  `action_details` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `landing_pages`
--

CREATE TABLE IF NOT EXISTS `landing_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `new_title` varchar(250) NOT NULL,
  `new_description` text NOT NULL,
  `lp_url` varchar(250) NOT NULL,
  `meta_keys` varchar(300) NOT NULL,
  `meta_desc` varchar(300) NOT NULL,
  `extra_products_count` int(11) NOT NULL,
  `shows` bigint(20) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `le_logs`
--

CREATE TABLE IF NOT EXISTS `le_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `le_id` int(11) NOT NULL,
  `date_log` datetime NOT NULL,
  `status_log` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `le_partners`
--

CREATE TABLE IF NOT EXISTS `le_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `p_name` varchar(200) NOT NULL,
  `p_email` varchar(200) NOT NULL,
  `p_website` varchar(200) NOT NULL,
  `p_obs` text NOT NULL,
  `p_link_caption` varchar(100) NOT NULL,
  `p_link_title` varchar(100) NOT NULL,
  `p_link_href` varchar(300) NOT NULL,
  `p_checkpage` varchar(300) NOT NULL,
  `my_link` varchar(300) NOT NULL,
  `last_date` datetime NOT NULL,
  `last_status` tinyint(4) NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vlabel` varchar(100) NOT NULL,
  `vvalue` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `vlabel`, `vvalue`) VALUES
(1, 'website_title', '2PCMS'),
(2, 'meta_keywords', 'meta keywords updated'),
(3, 'meta_description', ''),
(4, 'website_email', 'email@domain.com'),
(5, 'website_template', 'classic'),
(6, 'website_ga_id', 'UA-xxxxxx-yy'),
(7, 'partner_logo_width', '200'),
(8, 'partner_show', 'nu'),
(9, 'img_small_width', '90'),
(10, 'img_small_height', '90'),
(11, 'img_big_width', '250'),
(12, 'img_big_height', '250'),
(13, 'mask_redirect', 'da'),
(14, 'slug_price', 'jucarii'),
(15, 'url_former', '_'),
(16, 'produse_index_noi_count', '8'),
(17, 'produse_index_visited_count', '8'),
(18, 'record_per_page', '10'),
(19, 'show_zero_count_categories', 'nu'),
(20, 'lp_show_sidebar_box', 'da'),
(21, 'lp_sidebar_count', '4'),
(22, 'article_categories_sidebar_box', 'da'),
(23, 'article_sidebar_box', 'da'),
(24, 'article_sidebar_count', '4'),
(25, 'article_product_page_box', 'da'),
(26, 'footer_categories', '6'),
(27, 'footer_products', '6'),
(28, 'footer_searches', '20'),
(29, 'facebook_link', ''),
(30, 'facebook_embed', ''),
(31, 'facebook_icon_file', 'social_facebook_box_blue'),
(32, 'facebook_icon_size', '32'),
(33, 'twitter_link', ''),
(34, 'twitter_icon_file', 'social_twitter_button_blue'),
(35, 'twitter_icon_size', '32');

-- --------------------------------------------------------

--
-- Table structure for table `shop_brands`
--

CREATE TABLE IF NOT EXISTS `shop_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand` varchar(250) NOT NULL,
  `brand_url` varchar(250) NOT NULL,
  `brand_file` varchar(250) NOT NULL,
  `details` text NOT NULL,
  `shows` int(11) NOT NULL,
  `prod_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop_categories`
--

CREATE TABLE IF NOT EXISTS `shop_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(250) NOT NULL,
  `category_url` varchar(250) NOT NULL,
  `details` text NOT NULL,
  `parent_id` int(11) NOT NULL,
  `vcount` int(11) NOT NULL,
  `pa_count` int(11) NOT NULL COMMENT 'count produse active',
  `pi_count` int(11) NOT NULL COMMENT 'count produse inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop_priceranges`
--

CREATE TABLE IF NOT EXISTS `shop_priceranges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `vmin` int(11) NOT NULL,
  `vmax` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `shop_priceranges`
--

INSERT INTO `shop_priceranges` (`id`, `label`, `vmin`, `vmax`, `position`) VALUES
(1, 'De la 1 la 50 lei', 1, 50, 0),
(2, 'De la 50 la 100 lei', 51, 100, 0),
(3, 'De la 100 la 200 lei', 101, 200, 0),
(5, 'De la 200 la 500 lei', 201, 500, 0),
(6, 'Peste 500 lei', 501, 9999, 0);

-- --------------------------------------------------------

--
-- Table structure for table `shop_products`
--

CREATE TABLE IF NOT EXISTS `shop_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `original_id` varchar(50) NOT NULL,
  `original_id_int` bigint(20) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `title_url` varchar(250) NOT NULL,
  `meta_desc` varchar(200) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `price` varchar(50) NOT NULL,
  `price_int` double NOT NULL,
  `old_price` varchar(50) NOT NULL,
  `old_price_int` double NOT NULL,
  `aff_url` varchar(250) NOT NULL,
  `img_url` varchar(250) NOT NULL,
  `create_date` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `local_img_small` varchar(300) NOT NULL,
  `local_img_big` varchar(300) NOT NULL,
  `show_inlisting` int(11) NOT NULL,
  `show_inpage` int(11) NOT NULL,
  `click` int(11) NOT NULL,
  `marked` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `partner_x_original` (`partner_id`,`original_id_int`),
  KEY `category_id` (`category_id`),
  KEY `active` (`active`),
  KEY `partner_id` (`partner_id`),
  KEY `brand_id` (`brand_id`),
  KEY `original_id_int` (`original_id_int`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop_searches`
--

CREATE TABLE IF NOT EXISTS `shop_searches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sterm` varchar(250) NOT NULL,
  `scount` int(11) NOT NULL COMMENT 'cautari',
  `vcount` int(11) NOT NULL COMMENT 'vizualizari',
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sterm` (`sterm`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `text_transform`
--

CREATE TABLE IF NOT EXISTS `text_transform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(500) NOT NULL,
  `replace` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `text_transform`
--

INSERT INTO `text_transform` (`id`, `source`, `replace`) VALUES
(1, '. ', '. <br/>'),
(2, 'Varsta recomandata', '<br />Varsta recomandata'),
(3, '!', '!<br/>'),
(4, 'Dimensiuni', '<br/>Dimensiuni'),
(5, 'Recomandat pentru copii cu varsta', '<br/>Recomandat pentru copii cu varsta'),
(6, 'Greutate', '<br/>Greutate'),
(7, 'Recomandat copiilor cu varsta', '<br/>Recomandat copiilor cu varsta'),
(9, '*', '<br/>*');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL COMMENT 'used for login',
  `last_date` datetime NOT NULL,
  `last_ip` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `email`, `last_date`, `last_ip`) VALUES
(1, 'Demo Name', 'demo', 'demo@demo.ro', '2012-04-12 00:17:45', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `shop_filter_groups`
--

CREATE TABLE `shop_filter_groups` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `group_name` varchar(250) NOT NULL,
 `group_slug` varchar(250) NOT NULL,
 `position` int(3) NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shop_filters`
--

CREATE TABLE `shop_filters` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `filter_group_id` int(11) NOT NULL,
 `filter_name` varchar(250) NOT NULL,
 `filter_slug` varchar(250) NOT NULL,
 `position` int(3) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `filter_group_id` (`filter_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shop_filters_x_products`
--

CREATE TABLE `shop_filters_x_products` (
 `id` int(15) NOT NULL AUTO_INCREMENT,
 `shop_filter_id` int(11) NOT NULL,
 `shop_product_id` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `shop_filter_id` (`shop_filter_id`),
 KEY `shop_product_id` (`shop_product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
