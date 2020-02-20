<link rel="stylesheet" href="../wp-content/plugins/woocommerce-inova-tools/assets/css/admin.css">
<script type="text/javascript" src="../wp-content/plugins/woocommerce-inova-tools/assets/js/admin.js"></script>
<div class="wrap wit">
	<div class="wit-header">
		<h1>WC Cart Flow Settings</h1>
		<h2>WooCommerce Inova Tools</h2>
	</div>
	<div class="wit-body">
		<form action="options.php" method="post">
			<?php
			settings_fields( $key_settings.'_settings' );
			do_settings_sections( __FILE__ );
			$options = get_option( $key_settings.'_settings' ); 
			?>
			<table class="form-table" width="100%">
				<tr>
					<th scope="row">Habilitar opciones para modificar el flujo del carrito de compras</th>
					<td>
						<input name="<?= $key_settings;?>_settings[<?php echo $key_settings;?>_enabled]" type="checkbox" class="enable_settings" id="<?php echo $key_settings;?>_enabled" value="yes" <?php echo (isset($options[$key_settings.'_enabled']) && $options[$key_settings.'_enabled'] == 'yes') ? 'checked' : ''; ?>/>
						<br />
						<span class="description">Habilita opciones para modificar el flujo del carrito de compras en WooCommerce.</span>
					</td>
				</tr> 
				<tr class="settings_enabled_dependency">
					<th scope="row">Ocultar metodos de envío cuando este disponible el envío gratuito</th>
					<td>
						<input name="<?= $key_settings;?>_settings[hide_shipping_when_is_free_enabled]" type="checkbox"  id="hide_shipping_when_is_free_enabled" value="yes" <?php echo (isset($options['hide_shipping_when_is_free_enabled']) && $options['hide_shipping_when_is_free_enabled'] == 'yes') ? 'checked' : ''; ?>/>
						<br />
						<span class="description">Ocultar metodos de envío cuando este disponible el envío gratuito.</span>
					</td>
				</tr>
			</table>
			<input type="submit" class="button button-primary" value="<?= __('Guardar configuraciones')?>" />
		</form>
	</div>
<div>