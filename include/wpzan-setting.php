<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2>虾米播放器设置</h2><br>
	<form method="post" action="options.php">
		<?php 
			settings_fields( 'wpzan_setting_group' );
			$setting = wpzan_get_setting();
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label>食用方法</label></th>
					<td>
						添加 <code>&lt;?php wp_zan();?&gt;</code> 到需要的位置
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label>颜色设置</label></th>
					<td>
						<ul class="wpzan-color-ul">
							<?php $color = array(
									array(
										'title' => '初始状态',
										'key' => 'default-color',
										'default' => '#999'
									),
									array(
										'title' => '鼠标悬浮',
										'key' => 'hover-color',
										'default' => '#8AC78F'
									),
									array(
										'title' => '被点赞后',
										'key' => 'zaned-color',
										'default' => '#8AC78F'
									)
								);
								foreach ($color as $key => $V) {
									?>
									<li class="wpzan-color-li">
										<code><?php echo $V['title'];?></code>
										<?php $color = $setting[$V['key']] ? $setting[$V['key']] : $V['default'];?>
										<input name="<?php echo wpzan_setting_key($V['key']);?>" type="text" value="<?php echo $color;?>" id="wpzan-default-color" data-default-color="<?php echo $V['default'];?>" class="regular-text wpzan-color-picker" />
									</li>
								<?php } 
							?>
						</ul>
						<p class="description">麻麻说不要选择太奇怪的颜色组合, 么么哒(*￣￣)y</p>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="wpzan-submit-form">
			<input type="submit" class="button-primary muhermit_submit_form_btn" name="save" value="<?php _e('Save Changes') ?>"/>
		</div>
	</form>
	<style>
		.wpzan-color-li{position: relative;padding-left: 80px}
		.wpzan-color-li code{position: absolute;left: 0;top: 1px;}
	</style>		
</div>