<?php
/* --------------------------------------------------------------
Plugin Name: Mcl Clean head
Plugin URI: http://memocarilog.info/
Description: Clean tag in HTML head
Text Domain: mcl_clean_head
Domain Path: /languages
Version: 0.1
Author: Saori Miyazaki
Author URI: http://memocarilog.info/
License: GPL2
-------------------------------------------------------------- */
/*  
Copyright 2015 Saori Miyazaki ( email : saomocari@gmail.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */

/* -----------------------------------------------------------
	プラグイン有効語の設定リンク表示 
----------------------------------------------------------- */
function mcl_cleanhead_action_links( $links, $file ) {
	if ( plugin_basename( __FILE__ ) == $file ) {
		$settings_link = sprintf( '<a href="%1$s">%2$s</a>', 
		admin_url( 'options-general.php?page=mcl-clean-head.php' ), 
		__( 'Settings' , 'mcl_clean_head' ) );
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mcl_cleanhead_action_links', 10, 2 );


/* -----------------------------------------------------------
	管理画面メニューへメニュー項目を追加
----------------------------------------------------------- */
add_action( 'admin_menu', 'mcl_add_admin_menu' );
function mcl_add_admin_menu() {
	add_options_page(
		__( 'Cleanhead Setting', 'mcl_clean_head' ),
		__( 'Cleanhead Setting', 'mcl_clean_head' ),
		'manage_options',
		'mcl-clean-head.php',
		'mcl_clean_head_admin' // 定義した関数を呼び出し
	);
}

/* -----------------------------------------------------------
	管理画面を作成する関数を定義
----------------------------------------------------------- */
function mcl_clean_head_admin(){ ?>
	
	<div class="wrap">
	<?php 
			echo '<pre>';
			var_dump( $_POST );
			echo '</pre>';
			
			$optionstest = get_option( 'mcl_head_clean_option' );
			var_dump( $optionstest );
		?>
	<h2><?php _e( 'Mcl Cleanhead Setting', 'mcl_clean_head' ); ?></h2>
	<form id="mcl_clean_head_form" method="post" action="">
	<?php // nonce を発行
		wp_nonce_field( 'mcl_head_clean_options', 'mcl_head_clean_nonce' ); 
		$options = get_option( 'mcl_head_clean_option' );	
	
		// チェックボックスを定義
		function mcl_clean_head_checkbox( $options, $label, $name ){ ?>
			<p class="checkbox">
				<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_attr( $label ); ?></label>
				<input id="<?php echo esc_attr( $name ); ?>" type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1" <?php echo !empty( $options[$name] ) ? 'checked': '' ; ?> />
				
			</p>
		<?php 
		} ?>
		
		<table>
			<tr>
				<th>ジェネレーターを表示する</th>
				<td>
				<?php
					$label = 'ジェネレーターを表示';
					$name = 'mcl_hc_generator';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th>rsdxmlを表示する</th>
				<td>
				<?php
					$label = 'rsdxmlを表示';
					$name = 'mcl_hc_rsdxml';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th>wlwmanifestを表示する</th>
				<td>
				<?php
					$label = 'wlwmanifestを表示';
					$name = 'mcl_hc_wlwmanifest';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th>rsdxmlを表示する</th>
				<td>
				<?php
					$label = 'opensansを表示';
					$name = 'mcl_hc_opensans';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th>rsdxmlを表示する</th>
				<td>
				<?php
					$label = 'comments_styleを表示';
					$name = 'mcl_hc_comments_style';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th>emojiを表示する</th>
				<td>
				<?php
					$label = 'emojiを表示';
					$name = 'mcl_hc_print_emoji';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
		</table>
		
		<p class="submit"><?php submit_button(); ?></p>
	</form>
	</div>
<?php 
} // mcl_clean_head_admin

/* -----------------------------------------------------------
	フォームの値を受け取りデータベースへ保存する
----------------------------------------------------------- */
add_action( 'admin_init', 'mcl_head_clean_update');
function mcl_head_clean_update(){
	if( isset( $_POST['mcl_head_clean_nonce'] ) && $_POST['mcl_head_clean_nonce'] ){
		// nonce のチェック
		if( check_admin_referer( 'mcl_head_clean_options', 'mcl_head_clean_nonce' ) ){
			
			// 4. フォーム値の保存処理
			$mcl_hc_generator = isset( $_POST['mcl_hc_generator'] ) ? intval( $_POST['mcl_hc_generator'] ) : '';
			$mcl_hc_rsdxml = isset( $_POST['mcl_hc_rsdxml'] ) ? intval( $_POST['mcl_hc_rsdxml'] ) : '';
			$mcl_hc_wlwmanifest = isset( $_POST['mcl_hc_wlwmanifest'] ) ? intval( $_POST['mcl_hc_wlwmanifest'] ) : '';
			$mcl_hc_opensans = isset( $_POST['mcl_hc_opensans'] ) ? intval( $_POST['mcl_hc_opensans'] ) : '';
			$mcl_hc_comments_style = isset( $_POST['mcl_hc_comments_style'] ) ? intval( $_POST['mcl_hc_comments_style'] ) : '';
			$mcl_hc_print_emoji = isset( $_POST['mcl_hc_print_emoji'] ) ? intval( $_POST['mcl_hc_print_emoji'] ) : '';
			
			$array_options = array( 
				'mcl_hc_generator' => $mcl_hc_generator,
				'mcl_hc_rsdxml' => $mcl_hc_rsdxml,
				'mcl_hc_wlwmanifest' => $mcl_hc_wlwmanifest,
				'mcl_hc_rsdxml' => $mcl_hc_rsdxml,
				'mcl_hc_opensans' => $mcl_hc_opensans,
				'mcl_hc_comments_style' => $mcl_hc_comments_style,
				'mcl_hc_print_emoji' => $mcl_hc_print_emoji
			);
			update_option( 'mcl_head_clean_option', $array_options );
						
			// リダイレクトして再度フォームが送信されるエラーを防ぐ
			wp_safe_redirect( menu_page_url( 'mcl-clean-head', false ) );
		}
	}
}

/* -----------------------------------------------------------
	フック処理
----------------------------------------------------------- */

$options = get_option( 'mcl_head_clean_option' );

// generator 標記の削除
if( empty( $options['mcl_hc_generator'] ) ){
	remove_action( 'wp_head', 'wp_generator' );
}

// RSD(RPC用XML) application/rsd+xml を削除
if( empty( $options['mcl_hc_rsdxml'] ) ){
	remove_action( 'wp_head', 'rsd_link' );
}

// wlwmanifest.xml の読み込み削除（ブログ編集ツール）
if( empty( $options['mcl_hc_wlwmanifest'] ) ){
	remove_action( 'wp_head', 'wlwmanifest_link' );
}

// Open sans フォントの読み込み削除
if( empty( $options['mcl_hc_opensans'] ) ){
    function remove_wp_open_sans() {
        wp_deregister_style( 'open-sans' );
        wp_register_style( 'open-sans', false );
    }
    add_action( 'wp_enqueue_scripts', 'remove_wp_open_sans');
}

// 「最近のコメント」ウィジェット用のスタイルを削除
if( empty( $options['mcl_hc_comments_style'] ) ){
	function remove_recent_comments_style() {
		global $wp_widget_factory;
		remove_action( 'wp_head', array( $wp_widget_factory -> widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
	}
	add_action( 'widgets_init', 'remove_recent_comments_style' );
}

// 絵文字のスタイルとスクリプトの読み込み削除
if( empty( $options['mcl_hc_print_emoji'] ) ){
	remove_action( 'wp_head', 'print_emoji_detection_script' , 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );	
}