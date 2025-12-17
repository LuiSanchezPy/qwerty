<?php

class Pagopar_Pix extends Pagopar_Gateway_Base {
    public function __construct() {
        parent::__construct('pagopar_pix', __('Pagopar Pix', 'pagopar'));
    }
}
