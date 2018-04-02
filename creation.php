<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 11.03.2018
 * Time: 11:43
 */
include "config.php";
$mysql = new mysqli($db_config['db_address'],$db_config['db_user'],$db_config['db_password'],$db_config['db_name'],$db_config['db_port']);
$mysql->query("SET lc_time_names = 'ru_RU'");
$mysql->query("SET NAMES 'utf8'");
$mysql->query("CREATE DATABASE 'guestbook'");

$mysql->query("CREATE TABLE IF NOT EXISTS `gb_messages`(
        `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
        `username` VARCHAR(50) COLLATE=utf8_unicode_ci NOT NULL,
        `text` VARCHAR(255) COLLATE=utf8_unicode_ci NOT NULL,
        `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
        DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
$mysql->query('CREATE TABLE IF NOT EXISTS `gb_comments`(
        `id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
        `messageid` INTEGER NOT NULL,
        `text` VARCHAR(255) COLLATE=utf8_unicode_ci NOT NULL,
        `date` TIMESTAMP DEFAULT NOW()
        )
        DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ');
echo 'Creation complete';