<link rel="stylesheet" href="../wp-content/plugins/woocommerce-inova-tools/assets/css/admin.css">
<script type="text/javascript" src="../wp-content/plugins/woocommerce-inova-tools/assets/js/admin.js"></script>
<div class="wrap wit">
	<div class="wit-header">
		<h1>Payment Gateway Settings</h1>
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
					<th scope="row">Habilitar opciones para las pasarelas de pago disponibles</th>
					<td>
						<input name="<?= $key_settings;?>_settings[<?php echo $key_settings;?>_enabled]" type="checkbox" class="enable_settings" id="<?php echo $key_settings;?>_enabled" value="yes" <?php echo (isset($options[$key_settings.'_enabled']) && $options[$key_settings.'_enabled'] == 'yes') ? 'checked' : ''; ?>/>
						<br />
						<span class="description">Habilita opciones para las pasarelas de pago disponibles.</span>
					</td>
				</tr> 
			</table>
			<?php 
				$title=__('Pasarelas de pago soportadas');
				$message='<p>'.__('Las siguientes pasarelas de pago tienen implementación para obtener metada datos necesarios para la sincronización con Direksys.').'</p>';
				$message.='<ul>';
				foreach ($supported_payment_gateways as $method_name => $description) { 
					$message.='<li><b>'.$method_name .'</b> - '.$description .'</li>';
				}
				$message.='</ul>';
				render_alert($title, $message, 'info');
			?>
			<h3><?=__('Pasarelas de pago disponibles');?></h3>
			<?php
			$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
			foreach ($payment_gateways as $key => $payment_gateway) {
				$value_selected=(array_key_exists($key.'_transaction_type', $options))?$options[$key.'_transaction_type']:'';
				$value_selected_ptype_dks=(array_key_exists($key.'_dks_payment_type', $options))?$options[$key.'_dks_payment_type']:'';
				?>
				<div class="wit-panel">
					<div class="panel-header"><?= $payment_gateway->method_title.(isset($payment_gateway->plugin_name)?' - '.$payment_gateway->plugin_name : '');?></div>
					<div class="panel-body">
						<table class="form-table" width="100%">
							<tr class="settings_enabled_dependency">
								<th scope="row"><?=__('Tipo de transacción');?></th>
								<td>
									<select class="select " name="<?= $key_settings;?>_settings[<?= $key;?>_transaction_type]" id="<?= $key;?>_transaction_type">
										<option value="undefined" <?=($value_selected=='undefined')?'selected="selected"':''?>><?=__('Sin definir');?></option>
										<option value="purchase" <?=($value_selected=='purchase')?'selected="selected"':''?>><?=__('Autorizar y capturar');?></option>
										<option value="authorize" <?=($value_selected=='authorize')?'selected="selected"':''?>><?=__('Solo autorizar');?></option>
									</select>
									<br/>
									<span class="description">Tipo de transación, definir si tiene prioridad con respecto a los ajustes predeterminados de la pasarela de pago.</span>
								</td>
							</tr> 
							<tr class="settings_enabled_dependency">
								<th scope="row"><?=__('Tipo de pago');?></th>
								<td>
									<select class="select " name="<?= $key_settings;?>_settings[<?= $key;?>_dks_payment_type]" id="<?= $key;?>_dks_payment_type">
										<option value="undefined" <?=($value_selected_ptype_dks=='undefined')?'selected="selected"':''?>><?=__('Sin definir');?></option>
										<option value="Credit-Card" <?=($value_selected_ptype_dks=='Credit-Card')?'selected="selected"':''?>><?=__('Tarjeta de crédito');?></option>
										<option value="COD" <?=($value_selected_ptype_dks=='COD')?'selected="selected"':''?>><?=__('COD');?></option>
										<option value="Referenced Deposit" <?=($value_selected_ptype_dks=='Referenced Deposit')?'selected="selected"':''?>><?=__('Depósito referenciado');?></option>
									</select>
									<br/>
									<span class="description">Tipo de pago en según el campo Ptype en direksys.</span>
								</td>
							</tr> 
						</table>
					</div>
				</div>
				<?php
			}
			?>
			<input type="submit" class="button button-primary" value="<?= __('Guardar configuraciones')?>" />
		</form>
	</div>
<div>