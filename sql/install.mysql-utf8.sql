CREATE TABLE IF NOT EXISTS `#__jeprolab_feedback` (
  `feedback_id` int(11) unsigned NOT NULL auto_increment,
  `customer_name` varchar (64) NOT NULL,
  `customer_company` varchar (64) NOT NULL,
  `customer_phone` varchar (11) NOT NULL,
  `customer_email` varchar (255) NOT NULL,
  `request_service_id` int(11) unsigned NOT NULL,
  `enjoy_working_with_us` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy') default 'satisfy',
  `staff_courtesy` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `team_abilities` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `problem_support` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `team_availability` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `general_comment` text,
  `service_comment_or_suggestion` text,
  `how_do_you_learn_about_us` text,
  `help_us_improve_our_service` text,
  `reuse_our_services` tinyint(1) NOT NULL default '1',
  `recommend_our_services` tinyint(1) NOT NULL default '1',
  `services_speed` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `sample_delivery_speed` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `submission` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `reports_quality` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `analyze_speed` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `online_services` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  `global_quality` ENUM('highly_satisfy', 'satisfy', 'unsatisfy', 'highly_unsatisfy')  default 'satisfy',
  PRIMARY KEY (`feedback_id`)
) ENGINE=MyIsam DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_category`{
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `default_lab_id` int(11) unsigned NOT NULL,
  `depth_level` tinyint(3) NOT NULL DEFAULT '0',
  `n_left` int(11) unsigned NOT NULL DEFAULT '0',
  `n_right` int(11) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1)  NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `position` int(11) unsigned NOT NULL default '0',
  `is_root` tinyint(1)  NOT NULL NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`),
  KEY `parent_category` (`parent_id`),
  KEY `n_left_right` (`n_left`, `n_right`),
  KEY `n_left_right_published` (`n_left`, `n_right`, `published`),
  KEY `depth_level` (`depth_level`),
  KEY `n_left` (`n_left`),
  KEY `n_right` (`n_right`)
} ENGINE=MyIsam DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_category_lang` (
  `category_id` int(10) unsigned NOT NULL,
  `lab_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT '1',
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text,
  `link_rewrite` varchar(128) NOT NULL,
  `meta_title` varchar(128) default NULL,
  `meta_keywords` varchar(255) default NULL,
  `meta_description` varchar(255) default NULL,
  PRIMARY KEY (`category_id`,`lab_id`, `lang_id`),
  KEY `category_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_category_lab` (
  `category_id` int(11) NOT NULL,
  `lab_id` int(11) NOT NULL,
  `position` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`category_id`, `lab_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_category_group` (
  `category_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`category_id`,`group_id`),
  KEY `category_id` (`category_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_lab_group` (
  `lab_group_id` int(11) unsigned NOT  NULL AUTO_INCREMENT,
  `lab_group_name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `share_customer` tinyint(1) NOT NULL,
  `share_orders` tinyint(1) NOT NULL,
  `share_results` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lab_group_id`)
) ENGINE=MyIsam DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_lab` (
  `lab_id`int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lab_group_id`int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT '1',
  `theme_id` int(11) unsigned NOT NULL DEFAULT '1',
  `lab_name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lab_id`),
  KEY `lab_group_id` (`lab_group_id`),
  KEY `category_id` (`category_id`),
  KEY `theme_id` (`theme_id`)
) ENGINE=MyIsam DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_lab_url` (
  `lab_url_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lab_id` int(11) unsigned NOT NULL,
  `domain` varchar(150) NOT NULL,
  `ssl_domain` varchar(150) NOT NULL,
  `physical_uri` varchar(64) NOT NULL,
  `virtual_uri` varchar(64) NOT NULL,
  `main` TINYINT(1) NOT NULL,
  `published` TINYINT(1) NOT NULL,
  PRIMARY KEY (`lab_url_id`),
  KEY `lab_id` (`lab_id`),
  UNIQUE KEY `full_lab_url` (`domain`, `physical_uri`, `virtual_uri`),
  UNIQUE KEY `full_lab_ssl_url` (`ssl_domain`, `physical_uri`, `virtual_uri`)
) ENGINE=MyIsam DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_group` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `reduction` decimal(17,2) NOT NULL default '0.00',
  `price_display_method` TINYINT NOT NULL DEFAULT 0,
  `show_prices` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`group_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_group_lang` (
  `group_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`group_id`,`lang_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_group_lab` (
  `group_id` INT( 11 ) UNSIGNED NOT NULL,
  `lab_id` INT( 11 ) UNSIGNED NOT NULL,
    PRIMARY KEY (`group_id`, `lab_id`),
    KEY `lab_id` (`lab_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_theme` (
  `theme_id` int(11) NOT NULL  AUTO_INCREMENT,
  `theme_name` varchar(64) NOT NULL,
  `directory` varchar(64) NOT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=MyIsam DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_feeds` (
  `feed_id` int(11) NOT NULL AUTO_INCREMENT,
  `lab_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT '1',
  `feed_link` varchar(255) NOT NULL DEFAULT '#',
  `feed_title` varchar(255) NOT NULL ,
  `feed_description` text,
  `feed_author` varchar(127) NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`feed_id`)
) ENGINE=MyIsam DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_setting`(
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar (64) NOT NULL,
  `value` text ,
  `setting_group` varchar(64),
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyIsam DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_customer` (
  `customer_id` int(10) unsigned NOT NULL auto_increment,
  `lab_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `lab_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `gender_id` int(10) unsigned NOT NULL,
  `default_group_id` int(10) unsigned NOT NULL DEFAULT '1',
  `lang_id` int(10) unsigned NULL,
  `risk_id` int(10) unsigned NOT NULL DEFAULT '1',
  `company` varchar(64),
  `siret` varchar(14),
  `ape` varchar(5),
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `last_passwd_gen` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `birthday` date default NULL,
  `newsletter` tinyint(1) unsigned NOT NULL default '0',
  `ip_registration_newsletter` varchar(15) default NULL,
  `newsletter_date_add` datetime default NULL,
  `optin` tinyint(1) unsigned NOT NULL default '0',
  `website` varchar(128),
  `outstanding_allow_amount` DECIMAL( 20,6 ) NOT NULL default '0.00',
  `show_public_prices` tinyint(1) unsigned NOT NULL default '0',
  `max_payment_days` int(10) unsigned NOT NULL default '60',
  `secure_key` varchar(32) NOT NULL default '-1',
  `note` text,
  `published` tinyint(1) unsigned NOT NULL default '0',
  `is_guest` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`customer_id`),
  KEY `customer_email` (`email`),
  KEY `customer_login` (`email`,`passwd`),
  KEY `customer_passwd_id` (`customer_id`,`passwd`),
  KEY `gender_id` (`gender_id`),
  KEY `lab_group_id` (`lab_group_id`),
  KEY `lab_id` (`lab_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_country` (
  `country_id` int(10) unsigned NOT NULL auto_increment,
  `zone_id` int(10) unsigned NOT NULL,
  `currency_id` int(10) unsigned NOT NULL default '0',
  `iso_code` varchar(3) NOT NULL,
  `call_prefix` int(10) NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `contains_states` tinyint(1) NOT NULL default '0',
  `need_identification_number` tinyint(1) NOT NULL default '0',
  `need_zip_code` tinyint(1) NOT NULL default '1',
  `zip_code_format` varchar(12) NOT NULL default '',
  `display_tax_label` BOOLEAN NOT NULL,
  PRIMARY KEY (`country_id`),
  KEY `country_iso_code` (`iso_code`),
  KEY `country_zone` (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_country_lang` (
  `country_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`country_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_country_lab` (
 `country_id` INT( 11 ) UNSIGNED NOT NULL,
 `lab_id` INT( 11 ) UNSIGNED NOT NULL ,
  PRIMARY KEY (`country_id`, `lab_id`),
  KEY `lab_id` (`lab_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_currency` (
  `currency_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `iso_code` varchar(3) NOT NULL default '0',
  `iso_code_num` varchar(3) NOT NULL default '0',
  `sign` varchar(8) NOT NULL,
  `blank` tinyint(1) unsigned NOT NULL default '0',
  `format` tinyint(1) unsigned NOT NULL default '0',
  `decimals` tinyint(1) unsigned NOT NULL default '1',
  `conversion_rate` decimal(13,6) NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_currency_lab` (
  `currency_id` INT( 11 ) UNSIGNED NOT NULL,
  `lab_id` INT( 11 ) UNSIGNED NOT NULL,
  `conversion_rate` decimal(13,6) NOT NULL,
  PRIMARY KEY (`currency_id`, `lab_id`),
  KEY `lab_id` (`lab_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_state` (
  `state_id` int(10) unsigned NOT NULL auto_increment,
  `country_id` int(11) unsigned NOT NULL,
  `zone_id` int(11) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `iso_code` varchar(7) NOT NULL,
  `tax_behavior` smallint(1) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`state_id`),
  KEY `country_id` (`country_id`),
  KEY `name` (`name`),
  KEY `zone_id` (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_orders` (
  `order_id` int(10) unsigned NOT NULL auto_increment,
  `reference` VARCHAR(9),
  `lab_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `lab_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `carrier_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `cart_id` int(10) unsigned NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `address_delivery_id` int(10) unsigned NOT NULL,
  `address_invoice_id` int(10) unsigned NOT NULL,
  `current_state` int(10) unsigned NOT NULL,
  `secure_key` varchar(32) NOT NULL default '-1',
  `payment` varchar(255) NOT NULL,
  `conversion_rate` decimal(13,6) NOT NULL default 1,
  `module` varchar(255) default NULL,
  `recyclable` tinyint(1) unsigned NOT NULL default '0',
  `gift` tinyint(1) unsigned NOT NULL default '0',
  `gift_message` text,
  `mobile_theme` tinyint(1) NOT NULL default 0,
  `shipping_number` varchar(32) default NULL,
  `total_discounts` decimal(17,2) NOT NULL default '0.00',
  `total_discounts_tax_incl` decimal(17,2) NOT NULL default '0.00',
  `total_discounts_tax_excl` decimal(17,2) NOT NULL default '0.00',
  `total_paid` decimal(17,2) NOT NULL default '0.00',
  `total_paid_tax_incl` decimal(17,2) NOT NULL default '0.00',
  `total_paid_tax_excl` decimal(17,2) NOT NULL default '0.00',
  `total_paid_real` decimal(17,2) NOT NULL default '0.00',
  `total_products` decimal(17,2) NOT NULL default '0.00',
  `total_products_wt` DECIMAL(17, 2) NOT NULL default '0.00',
  `total_shipping` decimal(17,2) NOT NULL default '0.00',
  `total_shipping_tax_incl` decimal(17,2) NOT NULL default '0.00',
  `total_shipping_tax_excl` decimal(17,2) NOT NULL default '0.00',
  `carrier_tax_rate` DECIMAL(10, 3) NOT NULL default '0.00',
  `total_wrapping` decimal(17,2) NOT NULL default '0.00',
  `total_wrapping_tax_incl` decimal(17,2) NOT NULL default '0.00',
  `total_wrapping_tax_excl` decimal(17,2) NOT NULL default '0.00',
  `invoice_number` int(10) unsigned NOT NULL default '0',
  `delivery_number` int(10) unsigned NOT NULL default '0',
  `invoice_date` datetime NOT NULL,
  `delivery_date` datetime NOT NULL,
  `valid` int(1) unsigned NOT NULL default '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `cart_id` (`cart_id`),
  KEY `invoice_number` (`invoice_number`),
  KEY `carrier_id` (`carrier_id`),
  KEY `lang_id` (`lang_id`),
  KEY `currency_id` (`currency_id`),
  KEY `address_delivery_id` (`address_delivery_id`),
  KEY `address_invoice_id` (`address_invoice_id`),
  KEY `lab_group_id` (`lab_group_id`),
  KEY `lab_id` (`lab_id`),
  INDEX `date_add`(`date_add`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_customer` (
  `customer_id` int(10) unsigned NOT NULL auto_increment,
  `lab_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `lab_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `gender_id` int(10) unsigned NOT NULL,
  `default_group_id` int(10) unsigned NOT NULL DEFAULT '1',
  `lang_id` int(10) unsigned NULL,
  `risk_id` int(10) unsigned NOT NULL DEFAULT '1',
  `company` varchar(64),
  `siret` varchar(14),
  `ape` varchar(5),
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `last_passwd_gen` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `birthday` date default NULL,
  `newsletter` tinyint(1) unsigned NOT NULL default '0',
  `ip_registration_newsletter` varchar(15) default NULL,
  `newsletter_date_add` datetime default NULL,
  `optin` tinyint(1) unsigned NOT NULL default '0',
  `website` varchar(128),
  `outstanding_allow_amount` DECIMAL( 20,6 ) NOT NULL default '0.00',
  `show_public_prices` tinyint(1) unsigned NOT NULL default '0',
  `max_payment_days` int(10) unsigned NOT NULL default '60',
  `secure_key` varchar(32) NOT NULL default '-1',
  `note` text,
  `published` tinyint(1) unsigned NOT NULL default '0',
  `is_guest` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`customer_id`),
  KEY `customer_email` (`email`),
  KEY `customer_login` (`email`,`passwd`),
  KEY `customer_passwd_id` (`customer_id`,`passwd`),
  KEY `gender_id` (`gender_id`),
  KEY `lab_group_id` (`lab_group_id`),
  KEY `lab_id` (`lab_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_customer_group` (
  `customer_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`customer_id`,`group_id`),
  INDEX customer_login(group_id),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_customer_thread` (
  `customer_thread_id` int(11) unsigned NOT NULL auto_increment,
  `lab_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `lang_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned default NULL,
  `order_id` int(10) unsigned default NULL,
  `product_id` int(10) unsigned default NULL,
  `status` enum('open','closed','pending1','pending2') NOT NULL default 'open',
  `email` varchar(128) NOT NULL,
  `token` varchar(12) default NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
	PRIMARY KEY (`customer_thread_id`),
	KEY `lab_id` (`lab_id`),
	KEY `lang_id` (`lang_id`),
	KEY `contact_id` (`contact_id`),
	KEY `customer_id` (`customer_id`),
	KEY `order_id` (`order_id`),
	KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_customer_message` (
  `customer_message_id` int(10) unsigned NOT NULL auto_increment,
  `customer_thread_id` int(11) default NULL,
  `employee_id` int(10) unsigned default NULL,
  `message` text NOT NULL,
  `file_name` varchar(18) DEFAULT NULL,
  `ip_address` int(11) default NULL,
  `user_agent` varchar(128) default NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `private` TINYINT NOT NULL DEFAULT  '0',
  `read` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`customer_message_id`),
  KEY `customer_thread_id` (`customer_thread_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_customer_message_sync_imap` (
  `md5_header` varbinary(32) NOT NULL,
  KEY `md5_header_index` (`md5_header`(4))
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_connection` (
  `connection_id` int(10) unsigned NOT NULL auto_increment,
  `lab_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `lab_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `guest_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `ip_address` BIGINT NULL DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `http_referrer` varchar(255) default NULL,
  PRIMARY KEY (`connections_id`),
  KEY `guest_id` (`guest_id`),
  KEY `date_add` (`date_add`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_connection_page` (
  `connection_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime default NULL,
  PRIMARY KEY (`connections_id`,`page_id`,`time_start`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_connection_source` (
  `connection_source_id` int(10) unsigned NOT NULL auto_increment,
  `connection_id` int(10) unsigned NOT NULL,
  `http_referrer` varchar(255) default NULL,
  `request_uri` varchar(255) default NULL,
  `keywords` varchar(255) default NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`connections_source_id`),
  KEY `connections` (`connection_id`),
  KEY `order_by` (`date_add`),
  KEY `http_referrer` (`http_referrer`),
  KEY `request_uri` (`request_uri`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_address` (
  `address_id` int(10) unsigned NOT NULL auto_increment,
  `country_id` int(10) unsigned NOT NULL,
  `state_id` int(10) unsigned default NULL,
  `customer_id` int(10) unsigned NOT NULL default '0',
  `manufacturer_id` int(10) unsigned NOT NULL default '0',
  `supplier_id` int(10) unsigned NOT NULL default '0',
  `developer_id` int(10) unsigned NOT NULL default '0',
  `warehouse_id` int(10) unsigned NOT NULL default '0',
  `alias` varchar(32) NOT NULL,
  `company` varchar(64) default NULL,
  `lastname` varchar(32) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) default NULL,
  `postcode` varchar(12) default NULL,
  `city` varchar(64) NOT NULL,
  `other` text,
  `phone` varchar(32) default NULL,
  `phone_mobile` varchar(32) default NULL,
  `vat_number` varchar(32) default NULL,
  `dni` varchar(16) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `published` tinyint(1) unsigned NOT NULL default '1',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`address_id`),
  KEY `address_customer` (`customer_id`),
  KEY `country_id` (`country_id`),
  KEY `state_id` (`state_id`),
  KEY `manufacturer_id` (`manufacturer_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `developer_id` (`developer_id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_address_format` (
  `country_id` int(10) unsigned NOT NULL,
  `format` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


CREATE TABLE IF NOT EXISTS `#__jeprolab_image_lab` (
    `image_id` INT( 11 ) UNSIGNED NOT NULL,
    `lab_id` INT( 11 ) UNSIGNED NOT NULL,
    `cover` tinyint(1) NOT NULL,
    KEY (`image_id`, `lab_id`, `cover`),
    KEY `lab_id` (`lab_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeprolab_analyze_download` (
  `analyze_download_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `analyze_id` int(10) unsigned NOT NULL,
  `display_filename` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_expiration` datetime DEFAULT NULL,
  `nb_days_accessible` int(10) unsigned DEFAULT NULL,
  `nb_downloadable` int(10) unsigned DEFAULT '1',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_shareable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`analyze_download_id`),
  KEY `analyze_published` (`analyze_id`,`published`),
  UNIQUE KEY `analyze_id` (`analyze_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

--CREATE TABLE IF NOT EXISTS `#__jeprolab_feedback` () ENGINE=MyIsam DEFAULT CHARSET=utf8;--
--CREATE TABLE IF NOT EXISTS `#__jeprolab_feedback` () ENGINE=MyIsam DEFAULT CHARSET=utf8;--
--CREATE TABLE IF NOT EXISTS `#__jeprolab_feedback` () ENGINE=MyIsam DEFAULT CHARSET=utf8;--
--CREATE TABLE IF NOT EXISTS `#__jeprolab_feedback` () ENGINE=MyIsam DEFAULT CHARSET=utf8;--
--CREATE TABLE IF NOT EXISTS `#__jeprolab_feedback` () ENGINE=MyIsam DEFAULT CHARSET=utf8;--

