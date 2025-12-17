<?php

class Pagopar_Tarjetas_Promocion extends Pagopar_Gateway_Base
{
    public function __construct()
    {
        parent::__construct('pagopar_tarjetas_promocion', __('Pagopar Tarjetas Promocion', 'pagopar'));
    }
}
