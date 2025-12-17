<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;


final class WC_Pagopar_Gateway_Blocks_Support extends AbstractPaymentMethodType
{

    private $gateway;
    private $gateways;

    protected $name = 'pagopar'; // payment gateway id

    public function initialize()
    {
        // get payment gateway settings
        $this->settings = get_option("woocommerce_pagopar_settings", array());
        // you can also initialize your payment gateway here
        $this->gateways = WC()->payment_gateways->payment_gateways();
        $this->gateway  = $this->gateways[$this->name];
    }

    public function is_active()
    {

        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles()
    {
        // Register the script for frontend
        wp_register_script(
            'wc-pagopar-wcblock-integration',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-payment-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        wp_register_script(
            'wc-pagopar-wcblock-integration-bancard-qr',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-bancard-qr-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        wp_register_script(
            'wc-pagopar-wcblock-integration-billeteras',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-billeteras-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        wp_register_script(
            'wc-pagopar-wcblock-integration-efectivo',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-efectivo-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        wp_register_script(
            'wc-pagopar-wcblock-integration-pix',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-pix-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        wp_register_script(
            'wc-pagopar-wcblock-integration-tarjetas',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-tarjetas-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        wp_register_script(
            'wc-pagopar-wcblock-integration-tarjetas-guardadas',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-tarjetas-guardadas-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        wp_register_script(
            'wc-pagopar-wcblock-integration-tarjetas-promocion',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-tarjetas-promocion-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        wp_register_script(
            'wc-pagopar-wcblock-integration-transferencia',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-transferencia-bancaria-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        wp_register_script(
            'wc-pagopar-wcblock-integration-upay',
	        plugin_dir_url( __DIR__ ) . 'js/blocks/pagopar-upay-wcblock.js',
            array(
                'wc-blocks-registry',
                'wp-element',
                'wp-i18n',
                'jquery',
            ),
            null,
            true
        );

        return array(
            'wc-pagopar-wcblock-integration',
            'wc-pagopar-wcblock-integration-tarjetas-guardadas',
            'wc-pagopar-wcblock-integration-upay',
            'wc-pagopar-wcblock-integration-tarjetas',
            'wc-pagopar-wcblock-integration-tarjetas-promocion',
            'wc-pagopar-wcblock-integration-bancard-qr',
            'wc-pagopar-wcblock-integration-transferencia',
            'wc-pagopar-wcblock-integration-efectivo',
            'wc-pagopar-wcblock-integration-billeteras',
            'wc-pagopar-wcblock-integration-pix'
        );
    }

    public function get_payment_method_data()
    {

        $lista = aplicar_multimedios_pagos($this->gateways);
        $response = pp_obtener_lista_tarjetas(false);
        $responseDecode = json_decode($response);
        $filteredItems = array_filter($lista, function ($item) {
            return strpos($item->id, 'pagopar') === 0;
        });
        return array(
            'title'        => $this->get_setting('title'),
            'description'  => $this->get_setting('description'),
            'supports'  => array_filter($this->gateway->supports, [$this->gateway, 'supports']),
            'icon' => plugin_dir_url(__DIR__) . 'images/medios-pagos/iso-pagopar.png',
            'medios_pago' => $filteredItems,
            'tarjetasCatastradas' => $responseDecode,
            'promociones' => $this->obtenerPromociones(),
            'pluginUrl' => plugin_dir_url(__DIR__),
            'logged' => is_user_logged_in()
        );
    }

    function obtenerPromociones()
    {
        global $wpdb;
        $tabla = $wpdb->prefix . 'pagopar_promociones_tarjetas';

        $sql_consulta = "
        SELECT id, descripcion, porcentaje, tipo 
        FROM $tabla 
        WHERE estado = 1 
        AND inicio_promocion <= NOW() 
        AND fin_promocion >= NOW()
    ";
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Consulta directa necesaria para la tabla personalizada wp_pagopar_promociones_tarjetas del plugin Pagopar, que almacena datos de promociones y no tiene equivalente en APIs de WordPress.
        $results = $wpdb->get_results($sql_consulta, ARRAY_A);


        $promociones = [];

        if ($results) {
            foreach ($results as $row) {

                $promocion = (object)[
                    'id_promocion' => $row['id'],
                    'titulo' => $row['descripcion'],
                    'descripcion' => 'Promoción válida desde ' . date("d/m/Y H:i:s", strtotime($row['inicio_promocion'])) . ' hs hasta ' . date("d/m/Y H:i:s", strtotime($row['fin_promocion'])) . ' hs',
                    'porcentaje' => $row['porcentaje'],
                    'tipo' => $row['tipo']
                ];


                $promociones[] = $promocion;
            }
        }

        return $promociones;
    }
}
