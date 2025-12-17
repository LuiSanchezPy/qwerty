<?php

class Pagopar_Gateway_Base extends WC_Payment_Gateway {
    public function __construct($id, $method_title) {
        $this->id = $id;
        $this->method_title = $method_title;
        $this->method_description = __("Pagopar Plug-in de Gateway de pago para WooCommerce", 'pagopar');
        $this->title = $method_title;
	    add_action('woocommerce_blocks_checkout_block_registration', array($this, 'update_default_fields_data_with_block'), 999);
	    add_filter('woocommerce_get_country_locale', array($this, 'update_address_fields_data'), 999);
	    if($this->has_block_checkout()){
		    add_filter('woocommerce_default_address_fields', array($this, 'update_default_fields_data'), 999);
	    }
    }

	private function has_block_checkout() {
		$checkout_page_id = wc_get_page_id( 'checkout' );
		$has_block_checkout = $checkout_page_id && has_block( 'woocommerce/checkout', $checkout_page_id );
		return $has_block_checkout;
	}

	public function update_address_fields_data($locale){

		if(! function_exists('has_block') || ! has_block( 'woocommerce/checkout' )) {
			return $locale;
		}

		$usar_minimizado = $pagopar->settings['usar_formulario_minimizado'] ?? 'no';

		// Si no está activo el formulario minimizado y no se elimina país, no hacemos nada
		if ($usar_minimizado == 'yes' ) {
			foreach ($locale as $key => $value) {
				$locale[$key]['company'] = [
					'required' => false,
					'hidden'   => true,
				];
				$locale[$key]['city'] = [
					'required' => false,
					'hidden'   => true,
				];
				$locale[$key]['address_2'] = [
					'required' => false,
					'hidden'   => true,
				];
				$locale[$key]['postcode'] = [
					'required' => false,
					'hidden'   => true,
				];
			}
		}



		return $locale;
	}


	function update_default_fields_data_with_block() {
		if (
			!class_exists('Automattic\WooCommerce\Blocks\Package') ||
			!class_exists('Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry') ||
			!class_exists('Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields')
		) {
			return;
		}

		$pagopar = WC()->payment_gateways()->payment_gateways()['pagopar'] ?? null;

		if (!$pagopar || empty($pagopar->settings)) {
			return;
		}

		$usar_minimizado = $pagopar->settings['usar_formulario_minimizado'] ?? 'no';
		$eliminar_pais   = $pagopar->settings['eliminar_campo_pais'] ?? 'no';

		// Si no está activo el formulario minimizado y no se elimina país, no hacemos nada
		if ($usar_minimizado !== 'yes' && $eliminar_pais !== 'yes') {
			return;
		}

		$checkout_fields     = \Automattic\WooCommerce\Blocks\Package::container()->get(\Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields::class);
		$asset_data_registry = \Automattic\WooCommerce\Blocks\Package::container()->get(\Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry::class);

		$default_address_fields = $checkout_fields->get_core_fields();

		// Marcar como no requeridos si está activado "usar formulario minimizado"
		if ($usar_minimizado === 'yes') {
			$campos_no_requeridos = ['company', 'city', 'address_2', 'postcode'];
			foreach ($campos_no_requeridos as $key) {


				if (isset($default_address_fields[$key])) {

					$default_address_fields[$key]['required'] = false;
				}
			}
		}



		$asset_data_registry->add(
			'defaultFields',
			array_merge($default_address_fields, $checkout_fields->get_additional_fields())
		);
	}

	public function update_default_fields_data($fields) {
		$pagopar = WC()->payment_gateways()->payment_gateways()['pagopar'] ?? null;

		if (!$pagopar || empty($pagopar->settings)) {
			return $fields;
		}

		$usar_minimizado = $pagopar->settings['usar_formulario_minimizado'] ?? 'no';


		if ($usar_minimizado == 'yes' ) {
			$campos_no_requeridos = ['company', 'city', 'address_2', 'postcode'];

			foreach ($fields as $key => &$field) {
				if (in_array($key, $campos_no_requeridos, true)) {
					$field['required'] = false;
				}

			}
			unset($field);
		}



		return $fields;
	}


	// Procesar pagos (puede ser modificado en clases específicas si es necesario)
    public function process_payment($order_id)
    {
        $pagopar_gateway = new Pagopar_Gateway();
        $response = $pagopar_gateway->process_payment($order_id);
        return $response;
    }

    
}
