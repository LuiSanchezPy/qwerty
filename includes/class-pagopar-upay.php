<?php

class Pagopar_Upay extends Pagopar_Gateway_Base {
    public function __construct() {
        parent::__construct('pagopar_upay', __('Pagopar Upay', 'pagopar'));
    }
}
