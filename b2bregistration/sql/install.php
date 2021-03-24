<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2bregistration` (
    `id_b2bregistration` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) NOT NULL,
    `middle_name` varchar(255),
    `name_suffix` varchar(255),
    `flag` tinyint(1) default \'0\',
    `active` tinyint(1) default \'0\',
    PRIMARY KEY  (`id_b2bregistration`, `id_customer`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2b_custom_fields` (
    `id_b2b_custom_fields` int(11) NOT NULL AUTO_INCREMENT,
    `b2b_field_type` varchar(255),
    `active` tinyint(1) default \'0\',
    `position` tinyint(4) default 0,
    `field_required` tinyint(1) default \'0\',
    PRIMARY KEY  (`id_b2b_custom_fields`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2b_custom_fields_lang` (
    `id_b2b_custom_fields` int(11) NOT NULL AUTO_INCREMENT,
    `id_lang` int(11) NOT NULL,
    `id_shop` int(50),
    `b2b_field_name` varchar(255) default NULL,
    PRIMARY KEY  (`id_b2b_custom_fields`, `id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2b_custom_fields_shop` (
    `id_b2b_custom_fields` int(11) NOT NULL AUTO_INCREMENT,
    `id_shop` int(11) NOT NULL,
    PRIMARY KEY  (`id_b2b_custom_fields`, `id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2b_fields_data` (
    `id_b2b_fields_data` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) Not null,
    `b2b_field_name` varchar(255),
    `b2b_field_title` varchar(255),
    `id_field` int(11),
    PRIMARY KEY  (`id_b2b_fields_data`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
