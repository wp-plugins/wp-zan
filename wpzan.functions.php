<?php
	function wp_zan($odc=false){
		global $user_ID;
		get_currentuserinfo();

		$user_ID = $user_ID ? $user_ID : 0;
		$wpzan = new wpzan(get_the_ID(), $user_ID);

		echo $wpzan->zan_button($odc);
	}

	add_action('admin_menu', 'wpzan_menu');
	function wpzan_menu() {
		add_options_page('WP-Zan 设置', 'WP-Zan 设置', 'manage_options', basename(__FILE__), 'wpzan_setting_page');
		add_action( 'admin_init', 'wpzan_setting_group');
	}

	function wpzan_setting_group() {
		register_setting( 'wpzan_setting_group', 'wpzan_setting' );
	}	

	function wpzan_setting_page(){
        @include 'include/wpzan-setting.php';
    }

    add_action('admin_enqueue_scripts', 'wpzan_setting_scripts');
    function wpzan_setting_scripts(){
		if( isset($_GET['page']) && $_GET['page'] == "wpzan.functions.php" ){
    		wp_enqueue_style( 'wp-color-picker' );
    		wp_enqueue_script( 'wpzan_setting', wpzan_js_url('wp-zan-setting'), array( 'wp-color-picker' ), false, true );	
		}
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
				'option' => "<a href=\"" . WPZAN_ADMIN_URL . "options-general.php?page=wpzan.functions.php\">设置</a>"
			);
			$actions = array_merge($myactions, $actions);
		}
		return $actions;
	}
	add_filter('plugin_action_links', 'wpzan_plugin_action_link', 10, 4);

	function wpzan_scripts(){
		wp_enqueue_style( 'wpzan', wpzan_css_url('wp-zan-0.0.5'), array(), WPZAN_VERSION );

		wp_enqueue_script('jquery');
		wp_enqueue_script( 'wpzan',  wpzan_js_url('wp-zan'), array(), WPZAN_VERSION );
        wp_localize_script( 'wpzan', 'wpzan_ajax_url', WPZAN_ADMIN_URL . "admin-ajax.php");
	}
	add_action('wp_enqueue_scripts', 'wpzan_scripts', 20, 1);

	function wpzan_head_style(){?>
		<style type="text/css">
			.wp-zan{
				color: <?php echo wpzan_get_setting('default-color');?>!important
			}
			.wp-zan:hover{
				color: <?php echo wpzan_get_setting('hover-color');?>!important
			}
			.wp-zan.zaned{
				color: <?php echo wpzan_get_setting('zaned-color');?>!important
			}
		</style>
	<?php }
	add_action( 'wp_head', 'wpzan_head_style' );

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
	function wpzan_get_setting($key=NULL){
		$setting = get_option('wpzan_setting');
		return $key ? $setting[$key] : $setting;
	}

	/**
	 * 删除设置
	 * @return [void]
	 */
	function wpzan_delete_setting(){
		delete_option('wpzan_setting');
	}

	/**
	 * [wpzan_setting_key description]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	function wpzan_setting_key($key){
		if( $key ){
			return "wpzan_setting[$key]";
		}

		return false;
	}

	/**
	 * 升级设置
	 * @param  [array] $setting
	 * @return [void]
	 */
	function wpzan_update_setting($setting){
		update_option('wpzan_setting', $setting);
	}	

	function wpzan_css_url($css_url){
		return WPZAN_URL . "/static/css/{$css_url}.css";
	}

	function wpzan_js_url($js_url){
		return WPZAN_URL . "/static/js/{$js_url}.js";
	}