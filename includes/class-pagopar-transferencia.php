<?php

class Pagopar_Transferencia extends Pagopar_Gateway_Base {
    public function __construct() {
        parent::__construct('pagopar_transferencia_bancaria', __('Pagopar Transferencia Bancaria', 'pagopar'));
    }
}
