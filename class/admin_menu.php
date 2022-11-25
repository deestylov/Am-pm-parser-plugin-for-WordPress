<?php

class Admin_Menu_Options {

    protected $slug = 'parser_admin_my_am';
    // Начинаем сессию
    function session_init() {
		if (!session_id()) {
			session_start();
		}
	}

    // Стартовый конструктор
    function __construct() {
    add_action( 'admin_menu', [$this, 'admin_menu'] );
    add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
    }

    function admin_menu() {
        $page_title = 'Настройка парсера';
        $menu_title = 'Парсер my-am';
        $capability = 'manage_options';
        $callback = [$this, 'callback_option_page'];
        $icon = 'dashicons-networking';

        add_menu_page($page_title, $menu_title, $capability, $this->slug, $callback, $icon);
    }

	function callback_option_page() {
		
		if (!isset($_GET['action'])) {
			$this->page_index();
			return;
		}
		
	}

    function page_index() {
        global $wpdb;
        $this->render('admin-main.php', $data);
    }

    function page_not_found() {
		$this->render('404.php');
	}

    function render($template, $data = null) {
		if (is_array($data)) {
			extract($data);
		}
		
		$filepath = dirname(__FILE__) . '/../templates/' . $template;
		include($filepath);
	}

    function enqueue_scripts() {
        
        wp_enqueue_style('am_sweetalert', 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css', null, null);
        wp_enqueue_style('am_style', plugins_url( '/../css/style.css', __FILE__ ), false) ;
        
        wp_enqueue_script('am_sweetalert', 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js', null, null, true);
        wp_enqueue_script('am_common', plugins_url('/../js/common.js', __FILE__), null, 0.01, true);

        wp_localize_script('am_common', 'am_common_data', array(
            'url' => esc_url(admin_url('admin-ajax.php')),
            'action' => 'am_parse_cats',
            'baseUrl' => get_home_url()
        ));  

        // wp_localize_script('continue_parse_cats_data', 'continue_parse_cats', array(
        //     'url' => esc_url(admin_url('admin-ajax.php')),
        //     'action' => 'continue_parse_cats',
        //     'baseUrl' => get_home_url()
        // ));  

        wp_localize_script('am_common', 'am_common_data', array(
            'url' => esc_url(admin_url('admin-ajax.php')),
            'action' => 'am_parse_subcats',
            'baseUrl' => get_home_url()
        ));  
        wp_localize_script('am_common', 'am_common_data', array(
            'url' => esc_url(admin_url('admin-ajax.php')),
            'action' => 'am_parse',
            'baseUrl' => get_home_url()
        ));  
    }





}



?>
