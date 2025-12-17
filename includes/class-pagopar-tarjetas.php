<?php

class Pagopar_Tarjetas extends Pagopar_Gateway_Base {
    public function __construct() {
        parent::__construct('pagopar_tarjetas', __('Pagopar Tarjetas', 'pagopar'));
    }
}
