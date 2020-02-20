<link rel="stylesheet" href="../wp-content/plugins/woocommerce-inova-tools/assets/css/admin.css">
<script type="text/javascript" src="../wp-content/plugins/woocommerce-inova-tools/assets/js/admin.js"></script>
<div class="wrap wit">
	<div class="wit-header">
		<h1>Direksys Settings</h1>
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
					<th scope="row">Habilitar opciones para Direksys</th>
					<td>
						<input name="<?= $key_settings;?>_settings[<?php echo $key_settings;?>_enabled]" type="checkbox" class="enable_settings" id="<?php echo $key_settings;?>_enabled" value="yes" <?php echo (isset($options[$key_settings.'_enabled']) && $options[$key_settings.'_enabled'] == 'yes') ? 'checked' : ''; ?>/>
						<br />
						<span class="description">Habilita las configuraciones necesarias para la sincronización con Inova Direksys.</span>
					</td>
				</tr> 
				<tr class="settings_enabled_dependency">
					<th scope="row">Endpoint que procesará la orden en Direksys</th>
					<td>
						<input name="<?= $key_settings;?>_settings[dks_endpoint_create_order]" type="text" id="dks_endpoint_create_order" value="<?php echo (isset($options['dks_endpoint_create_order']) && $options['dks_endpoint_create_order'] != '') ? $options['dks_endpoint_create_order'] : ''; ?>"/>
						<br />
						<span class="description">Endpoint que recibirá y procesará los datos de la orden WooCommerce(<a href="https://woocommerce.github.io/woocommerce-rest-api-docs/#retrieve-an-order" target="_blank">ver formato</a>).</span>
					</td>
				</tr>
			</table>
			<input type="submit" class="button button-primary" value="<?= __('Guardar configuraciones')?>" />
		</form>
	</div>
<div>