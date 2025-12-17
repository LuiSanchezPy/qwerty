<?php

class Pagopar_Bancard_QR extends Pagopar_Gateway_Base {
    public function __construct() {
        parent::__construct('pagopar_bancard_qr', __('Pagopar Bancard QR', 'pagopar'));
    }
}
