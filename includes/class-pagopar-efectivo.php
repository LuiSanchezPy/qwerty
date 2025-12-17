<?php

class Pagopar_Efectivo extends Pagopar_Gateway_Base {
    public function __construct() {
        parent::__construct('pagopar_efectivo', __('Pagopar Efectivo', 'pagopar'));
    }
}
