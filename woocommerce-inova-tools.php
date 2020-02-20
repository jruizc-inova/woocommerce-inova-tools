<?php
/*
Plugin Name: WooCommerce Inova Tools
Description: WooCommerce Inova Tools custom plugin
Author: José Adrian Ruiz Carmona

Version: 1.0.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Inova_Tools' ) ) {
	require 'includes/helpers/utils.php'; // Extra tools for security an customize platform

	class WC_Inova_Tools{
		private $plugin_path;
		private $id;
		private $settings;
		private $label;
		private $main_page_slug;

		public function __construct() {
			$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
			$this->basename = plugin_basename(__FILE__);
			$this->plugin_path_url=plugin_dir_url( __FILE__ );
			$this->id = 'wc_inova_tools';
			$this->label = __( 'WC Inova Tools', 'wc_inova_tools' );
			$this->main_page_slug = 'wit-admin-menu';
			$this->supported_payment_gateways = array(
				'conektacard'=>'Conekta Card – Pago con Tarjeta de Crédito o Débito',
				'ppec_paypal'=>'PayPal Exprés'
			);
			//call init functions
			$this->init_settings();
			$this->init_hooks();
			//fiter functions
			add_filter('plugin_action_links_'. $this->basename, [$this,'wit_page_settings_link']);
		}

		private function init_settings(){
			//settings key must be equals to option defined in get_option funtion if you add at the end _settings
			$this->settings['wit_dks'] = get_option('wit_dks_settings');
			$this->settings['wit_payment_gateway'] = get_option('wit_payment_gateway_settings');
			$this->settings['wit_cart_flow'] = get_option('wit_cart_flow_settings');
			$this->settings['wit_wc_extra_cfg'] = get_option('wit_wc_extra_cfg_settings');
		}

		private function init_hooks(){
			//Plugin actions
			add_action('woocommerce_checkout_order_processed', [$this, 'my_checkout_order_processed' ], 10, 4);
			add_action('woocommerce_after_checkout_form', [$this, 'custom_after_checkout_form'], 10 );
			add_action('woocommerce_payment_complete', [$this, 'wit_woocommerce_payment_complete'], 10 );
			//admin stuff
			add_action('admin_menu',  [$this,'wit_admin_menu']);
			add_action('admin_notices', function(){settings_errors();});
			add_action('admin_init', [$this,'wit_register_settings']);
			add_action('wp_enqueue_scripts', [$this,'wit_load_js_assets'] );
			//adding extrafield to product data
			add_action( 'woocommerce_product_options_general_product_data', [$this,'wit_product_options_general_product_data']); 
			add_action( 'woocommerce_process_product_meta', [$this,'wit_process_product_meta']);
			//adding extrafield to product data variations
			add_action( 'woocommerce_variation_options_pricing', [$this,'wit_variation_options_pricing'], 10, 3 );
			add_action( 'woocommerce_save_product_variation', [$this,'wit_save_product_variation'], 10, 2 );
			add_filter( 'woocommerce_available_variation', [$this,'wit_available_variation']);
		}

		/********************** Product funtions ***********************/
		function wit_product_options_general_product_data(){
			global $woocommerce, $post;
			echo '<div class="product_custom_field">';
			woocommerce_wp_text_input(
				array(
					'id' => 'DKS_products_prices_id',
					'placeholder' => '',
					'label' => __('Direksys ID products prices', 'woocommerce'),
					'desc_tip' => 'true'
				)
			);
			echo '</div>';
		}

		function wit_process_product_meta($post_id){
			$woocommerceDKS_products_prices_id = $_POST['DKS_products_prices_id'];
			if (!empty($woocommerceDKS_products_prices_id))
				update_post_meta($post_id, 'DKS_products_prices_id', esc_attr($woocommerceDKS_products_prices_id));
		}
		/********************** END Product funtions ***********************/

		/********************** Product variations funtions ***********************/
		function wit_save_product_variation( $variation_id, $i ) {
			$DKS_products_prices_id = $_POST['DKS_products_prices_id'][$i];
			if ( isset( $DKS_products_prices_id ) ) update_post_meta( $variation_id, 'DKS_products_prices_id', esc_attr( $DKS_products_prices_id ) );
		}

		function wit_variation_options_pricing( $loop, $variation_data, $variation ) {
			woocommerce_wp_text_input( array(
				'id' => 'DKS_products_prices_id[' . $loop . ']',
				'class' => 'short',
				'label' => __( 'Direksys ID products prices', 'woocommerce' ),
				'value' => get_post_meta( $variation->ID, 'DKS_products_prices_id', true )
			)
		);
		}

		function wit_available_variation( $variations ) {
			$variations['DKS_products_prices_id'] = '<div class="woocommerce_DKS_products_prices_id">Direksys ID products prices: <span>' . get_post_meta( $variations[ 'variation_id' ], 'DKS_products_prices_id', true ) . '</span></div>';
			return $variations;
		}
		/********************** END Product variations funtions ***********************/

		function wit_load_js_assets(){
			$checkout_page=getFixedCheckOutPageURL();
			$cart_page=getFixedCartPageURL();
			if(isset($this->settings['wit_cart_flow']) && array_key_exists('wit_cart_flow_enabled', $this->settings['wit_cart_flow']) && $this->settings['wit_cart_flow']['wit_cart_flow_enabled']=='yes'
				&& array_key_exists('hide_shipping_when_is_free_enabled', $this->settings['wit_cart_flow']) && $this->settings['wit_cart_flow']['hide_shipping_when_is_free_enabled']=='yes'){
				if (is_page($checkout_page)||is_page($cart_page)) {
					wp_enqueue_script('hide_shipping_when_is_free', $this->plugin_path_url.'assets/js/hide_shipping_when_is_free.js', array( 'jquery' ),false);
				}
			}
		} 

		function wit_admin_menu(){
			add_menu_page( 'WC Inova Tools', 'WooCommerce Inova Tools', 'administrator', $this->main_page_slug, [$this,'wit_render_main_page'],'/wp-content/plugins/woocommerce-inova-tools/assets/images/logoplug-bw.png');
			add_submenu_page( $this->main_page_slug, 'Direksys settings', 'Direksys settings', 'administrator', 'wit-settings-direksys', function(){ $this->wit_render_settings_page('wit_dks'); });
			add_submenu_page( $this->main_page_slug, 'Payment Gateways', 'Payment Gateways', 'administrator', 'wit-settings-payment-gateways', function(){ $this->wit_render_settings_page('wit_payment_gateway',array(
				'supported_payment_gateways'=>$this->supported_payment_gateways)); });
			add_submenu_page( $this->main_page_slug, 'WC Cart Flow Settings', 'WC Cart Flow Settings', 'administrator', 'wit-settings-cart-flow', function(){ $this->wit_render_settings_page('wit_cart_flow'); });
		}

		function wit_register_settings(){
			foreach ($this->settings as $key => $value) {
				if (method_exists($this, $key.'_settings_validate')) {
		    		//this will save the option in the wp_options table as {$key.'_settings'} the third parameter is a function that will validate your input values
					register_setting($key.'_settings', $key.'_settings', [$this, $key.'_settings_validate']);
				}
			}
		}

		function wit_dks_settings_validate($args){
			if(!isset($args['dks_endpoint_create_order']) || !is_valid_url($args['dks_endpoint_create_order'])){
		        //add a settings error because the email is invalid and make the form field blank, so that the user can enter again
				$args['dks_endpoint_create_order'] = '';
				add_settings_error('wit_dks_settings', 'dks_endpoint_create_order', __('Ingresa una URL válida para el endpoint.'), $type = 'error');   
			}
			return $args;
		}

		function wit_payment_gateway_settings_validate($args){
			return $args;
		}

		function wit_cart_flow_settings_validate($args){
			return $args;
		}

		/*settings functions*/
		private function wit_render_settings_page($key_settings='', $pdata=array()){
			if (isset($this->settings) && is_array($this->settings) && array_key_exists($key_settings, $this->settings)) {
				$data['key_settings']=$key_settings;
				$data['settings']=$this->settings[$data['key_settings']];
				if (isset($pdata) && is_array($pdata)){
					$data=array_merge($data, $pdata);
				}
				render_page(dirname(__FILE__).'/includes/admin/settings_'.str_replace('wit_', '', $key_settings).'.php', $data);
			}
		}

		function wit_page_settings_link( $links ) {
			$links[] = '<a href="' .
			admin_url( 'admin.php?page='.$this->main_page_slug ) .
			'">' . __('Settings') . '</a>';
			return $links;
		}

		function wit_render_main_page(){
			render_page(dirname(__FILE__).'/includes/admin/main_page.php'); 
		}

		function wit_woocommerce_payment_complete($order_id){
			if(isset($this->settings['wit_dks']) && array_key_exists('wit_dks_enabled', $this->settings['wit_dks']) && $this->settings['wit_dks']['wit_dks_enabled']=='yes'){
				$this->connect_dks($order_id);
			}
		} 

		function write_log($log) {
			if (true === WP_DEBUG) {
				if (is_array($log) || is_object($log)) {
					error_log(print_r($log, true));
				} else {
					error_log($log);
				}
			}
		}

		function connect_dks($order_id){
			$order = wc_get_order($order_id);
			$order->update_meta_data('order',json_encode($order));
			if (isset($this->settings['wit_dks']['dks_endpoint_create_order'])&&$this->settings['wit_dks']['dks_endpoint_create_order']!=='') {
				$response = wp_remote_post($this->settings['wit_dks']['dks_endpoint_create_order'], 
					array(
						'method' => 'POST',
						'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
						'body' =>'{"id":'.$order_id.'}' 
					)
				);
				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
					$this->write_log('WC DKS Tools:'.$error_message);
				}
			}else{
				$this->write_log('WC DKS Tools: Endpoin invalid, check configuration.');
			}
		}

		/* WooCommerce actions */
		function my_checkout_order_processed($order_id, $posted_data, $order){
			fixEmptyPostData();
			$payment_method=empty($_POST['payment_method'])?'':$_POST['payment_method'];
			//Add your payment gateway configuration here
			// when you add new payment gateway make sure that you update $this->supported_payment_gateways array is updated too. (key= payment gateway method name and value any descriptióo)
			$transaction_type = (isset($this->settings['wit_payment_gateway'])&&isset($this->settings['wit_payment_gateway'][$payment_method.'_transaction_type'])) ? $this->settings['wit_payment_gateway'][$payment_method.'_transaction_type']:'authorize';
			$dks_ptype='Credit-Card';
			if($payment_method=='conektacard'){
				$this->update_meta_data_order($order_id,$payment_method,'conekta_card',$transaction_type, $dks_ptype,$_POST['conekta-card-number'],$_POST['conekta-card-name'],$_POST['card-expiration-month'],$_POST['card-expiration-year'],$_POST['conekta-card-cvc']);
			}else if($payment_method=='ppec_paypal'){
				$this->update_meta_data_order($order_id,$payment_method,'paypal',$transaction_type,$dks_ptype);
			}
		}

		function custom_after_checkout_form() {
			$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
			echo $this->get_extra_html_payment_gateway($payment_gateways);
		}

		/*Add custom functions*/
		function get_extra_html_payment_gateway($payment_gateways){
			if (isset($payment_gateways)&& is_array($payment_gateways)) {
				foreach ($payment_gateways as $key => $value) {
					$file_html = dirname(__FILE__). '/templates/payment_gateway/after_checkout_'.$key.'.php';
					return (file_exists($file_html) ? file_get_contents($file_html) : '');
				}
			}
			return '';
		}

		private function update_meta_data_order($order_id, $payment_method, $payment_prefix_metadata,$transaction_type='',$dks_ptype='', $card_number='', $car_name='', $expiration_month='', $expiration_year='',$card_cvc=''){
			if (isset($order_id) && isset($payment_method) && isset($transaction_type)) {
				$order = wc_get_order($order_id);
				$order->update_meta_data('payment_method',$payment_method);
				if($card_number!=''){
					$order->update_meta_data($payment_prefix_metadata.'_number', LeoEncrypt($card_number));
				}
				if($car_name!=''){
					$order->update_meta_data($payment_prefix_metadata.'_name', $car_name);
				}
				if($expiration_month!=''){
					$expiration_month=str_pad($expiration_month, 2, "0", STR_PAD_LEFT); 
					$order->update_meta_data($payment_prefix_metadata.'_expiration_month', LeoEncrypt($expiration_month));
				}
				if($expiration_year!=''){
					$order->update_meta_data($payment_prefix_metadata.'_expiration_year', LeoEncrypt($expiration_year));
				}
				if($card_cvc!=''){
					$order->update_meta_data($payment_prefix_metadata.'_cvc', LeoEncrypt($card_cvc));
				}
				if($transaction_type!=''){
					$order->update_meta_data('transaction_type',$transaction_type);//purchase=Authorize & Capture | authorize =Authorize Only
				}
				if($dks_ptype!=''){
					$order->update_meta_data('payment_type',$dks_ptype);//purchase=Authorize & Capture | authorize =Authorize Only
				}
				$order->save();
				unset($order);
				return true;
			}
			return false;
		}
	}

	return new WC_Inova_Tools();
}
