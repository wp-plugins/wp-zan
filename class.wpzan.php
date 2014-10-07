<?php

class wpzan {
	
	private		$ip;
	public		$post_id;
	public		$user_id;
	public		$zan_count;
	public		$is_loggedin;
	
	public function __construct($post_id, $user_id){
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->post_id = $post_id;
		$this->user_id = $user_id;
		
		if( $user_id && $user_id > 0 ){
			$this->is_loggedin = true;
		}
		
		$this->zan_count();
	}

	public function zan_count(){
		global $wpdb, $wpzan_table_name;
		
		// check in the db for zan
		$zan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_id) FROM $wpzan_table_name WHERE post_id = %d", $this->post_id));
		
		// returns zan, return 0 if no zan were found
		$this->zan_count = $zan_count;
		
	}
	
	public function is_zan(){
		if( isset($_COOKIE['wp_zan_'.$this->post_id]) ){
			return true;
		}

		global $wpdb, $wpzan_table_name;
		
		if($this->is_loggedin){
			// user is logged in	
			$zan_check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_id) FROM $wpzan_table_name
											WHERE	post_id = %d
											AND		user_id = %d", $this->post_id, $this->user_id));
		} else{
			// user not logged in, check by ip address
			$zan_check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_id) FROM $wpzan_table_name
											WHERE	post_id = %d
											AND		ip_address = %s
											AND		user_id = %d", $this->post_id, $this->ip, 0));
		}

		$zan_check = intval($zan_check);

		return $zan_check && $zan_check > 0;
	}
	
	public function add_zan(){
		global $wpdb, $wpzan_table_name;
		
		if( !$this->is_zan() ){
			$wpdb->insert($wpzan_table_name, array('post_id' => $this->post_id, 
													'user_id' => $this->user_id,
													'ip_address' => $this->ip), array('%d', '%d', '%s'));

			$expire = time() + 365*24*60*60;
        	setcookie('wp_zan_'.$this->post_id, $this->post_id, $expire, '/', $_SERVER['HTTP_HOST'], false);
		}

		$this->zan_count();
	}
		
	public function zan_button($odc){
		$class = $this->is_zan() ? 'wp-zan zaned' : 'wp-zan';
		$userId = $this->is_loggedin ? $this->user_id : 0;	
		$postId = $this->post_id;

		$action = "wpzan($postId, $userId)";
		
		$btn_html = $odc ? '<a id="wp-zan-%d" class="%s" onclick="%s" href="javascript:;">%d</a>' : '<a id="wp-zan-%d" class="%s" onclick="%s" href="javascript:;"><i class="icon-wpzan"></i>赞 (<span>%d</span>)</a>';
		$button = sprintf($btn_html, $postId, $class, $action, $this->zan_count);

		return $button;
	}
}

?>