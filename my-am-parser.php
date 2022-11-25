<?php
/**
 * Plugin Name: Невероятный парсер товаров с сайта my-am.pm 😇
 * * Plugin URI:  https://kolista.ru
 * * Description: Сборщик товаров с сайта my-am.pm в корректный для выгрузки csv-файл.
 * Author: Роман Дистайлов
 * Author URI: mailto:deestylov@ya.ru
 * * Author DEMO: mailto:deestylov@ya.ru
 * Version: 1.1
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
 
define('PARENT_SLUG', __FILE__);

require_once('libs/domparse/simple_html_dom.php'); 
require_once('libs/creat-csv.php'); 
require_once('class/admin_menu.php');
require_once('class/parsing.php');





new Admin_Menu_Options();
new Parsing ();



