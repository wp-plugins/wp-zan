<?php
	function wp_zan(){
		global $user_ID;
		get_currentuserinfo();

		$user_ID = $user_ID ? $user_ID : 0;
		$wpzan = new wpzan(get_the_ID(), $user_ID);

		echo $wpzan->zan_button();
	}

	function wpzan_install(){
		global $wpdb, $wpzan_table_name;

		if( $wpdb->get_var("show tables like '{$wpzan_table_name}'") != $wpzan_table_name ) {
			$wpdb->query("CREATE TABLE {$wpzan_table_name} (
				id      BIGINT(20) NOT NULL AUTO_INCREMENT,
				post_id BIGINT(20) NOT NULL,
				user_id BIGINT(20) NOT NULL,
				ip_address VARCHAR(25) NOT NULL,
				UNIQUE KEY id (id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
		}
	}

	function wpzan_uninstall(){
		global $wpdb, $wpzan_table_name;

		$wpdb->query("DROP TABLE IF EXISTS {$wpzan_table_name}");
	}

	function wpzan_plugin_action_link($actions, $plugin_file, $plugin_data){
		if( strpos($plugin_file, 'wp-zan') !== false && is_plugin_active($plugin_file) ){
			$myactions = array(
				'option' => "<a href=\"" . WPZAN_ADMIN_URL . "options-general.php?page=class.wpzan.php\">设置</a>"
			);
			$actions = array_merge($myactions, $actions);
		}
		return $actions;
	}
	add_filter('plugin_action_links', 'wpzan_plugin_action_link', 10, 4);

	function wpzan_scripts(){
		wp_enqueue_style( 'wpzan', wpzan_css_url('wp-zan'), array(), WPZAN_VERSION );

		wp_enqueue_script('jquery');
		wp_enqueue_script( 'wpzan',  wpzan_js_url('wp-zan'), array(), WPZAN_VERSION );
        wp_localize_script( 'wpzan', 'wpzan_ajax_url', WPZAN_ADMIN_URL . "admin-ajax.php");
	}
	add_action('wp_enqueue_scripts', 'wpzan_scripts', 20, 1);

	function wpzan_callback(){
		$user_id = $_POST['user_id'];
		$post_id = $_POST['post_id'];

		$wpzan = new wpzan($post_id, $user_id);
		if( $wpzan->is_zan() ){
			$result = array(
				'status' => 300
			);
		}else{
			$wpzan->add_zan();

			$result = array(
				'status' => 200,
				'count' => $wpzan->zan_count
			);
		}

		header('Content-type: application/json');
		echo json_encode($result);
		exit;
	}
	add_action( 'wp_ajax_wpzan', 'wpzan_callback');
	add_action( 'wp_ajax_nopriv_wpzan', 'wpzan_callback');

	/**
	 * 获取设置
	 * @return [array]
	 */
	function get_setting(){
		return get_option('wpzan_setting');
	}

	/**
	 * 删除设置
	 * @return [void]
	 */
	function delete_setting(){
		delete_option('wpzan_setting');
	}

	/**
	 * 升级设置
	 * @param  [array] $setting
	 * @return [void]
	 */
	function update_setting($setting){
		update_option('wpzan_setting', $setting);
	}	

	function wpzan_css_url($css_url){
		return WPZAN_URL . "/static/css/{$css_url}.css";
	}

	function wpzan_js_url($js_url){
		return WPZAN_URL . "/static/js/{$js_url}.js";
	}