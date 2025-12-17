<?php

class Pagopar_Billeteras extends Pagopar_Gateway_Base {
    public function __construct() {
        parent::__construct('pagopar_billeteras', __('Pagopar Billeteras', 'pagopar'));
    }
}
