<?php

class Pagopar_Tarjetas_Guardadas extends Pagopar_Gateway_Base {
    public function __construct() {
        parent::__construct('pagopar_tarjetas_guardadas', __('Pagopar Tarjetas Guardadas', 'pagopar'));
    }
}
