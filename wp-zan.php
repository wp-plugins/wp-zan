<?php
/*
Plugin Name: WP-Zan
Plugin URI: http://mufeng.me/wp-zan.html
Description: Wordpress 文章点赞
Version: 0.0.5
Author: Mufeng
Author URI: http://mufeng.me
*/

define('WPZAN_VERSION', '0.0.5');
define('WPZAN_URL', plugins_url('', __FILE__));
define('WPZAN_PATH', dirname( __FILE__ ));
define('WPZAN_ADMIN_URL', admin_url());

/**
 * 定义数据库
 */
global $wpdb, $wpzan_table_name;
$wpzan_table_name = isset($table_prefix) ? ($table_prefix . 'zan') : ($wpdb->prefix . 'zan');

/**
 * 加载类
 */
require WPZAN_PATH . '/class.wpzan.php';

/**
 * 加载函数
 */
require WPZAN_PATH . '/wpzan.functions.php';

/**
 * 插件激活,新建数据库
 */
register_activation_hook(__FILE__, 'wpzan_install');

/**
 * 插件停用, 删除数据库
 */
//register_deactivation_hook(__FILE__, 'wpzan_uninstall');
