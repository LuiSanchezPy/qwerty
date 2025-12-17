<?php

global $wpdb;
$urlAdmin = $_SERVER['REQUEST_URI'];



    # Actualizamos valores del form
    if (isset($_POST['actualizar'])){

        if (!isset($_POST['pagopar_nonce']) || !wp_verify_nonce($_POST['pagopar_nonce'], 'pagopar_guardar_formulario')) {
            wp_die('No autorizado.');
        }          

        include_once 'pagopar-sincronizacion.php';
                
        update_option('pagopar_no_agregar_impuestos', $_POST['pagopar_no_agregar_impuestos']);
        update_option('pagopar_gastos_administrativos', $_POST['pagopar_gastos_administrativos']);
        update_option('pagopar_gastos_administrativos_text', $_POST['pagopar_gastos_administrativos_text']);

        # Si la divisa de dolar a guarani se ha modificado
        if (get_option('pagopar_conversion_dolar_guarani') != $_POST['pagopar_conversion_dolar_guarani']) {
	        establecerVolverAEnviarPorCambioDivisa();
        }
        update_option('pagopar_conversion_dolar_guarani', $_POST['pagopar_conversion_dolar_guarani']);
    }
    
    // Obtenemos y sanitizamos valores guardados
    $pagopar_no_agregar_impuestos = sanitize_text_field(get_option('pagopar_no_agregar_impuestos'));
    $pagopar_gastos_administrativos = floatval(get_option('pagopar_gastos_administrativos'));
    $pagopar_gastos_administrativos_text = sanitize_text_field(get_option('pagopar_gastos_administrativos_text'));
    $pagopar_conversion_dolar_guarani = floatval(get_option('pagopar_conversion_dolar_guarani'));



?>
<style>
#dashboardEnvios .form-table th {
    width: 300px !important;
    padding:5px !important;
}

#dashboardEnvios .form-table td {
    margin-bottom: 9px;
    padding: 5px 15px 5px 10px;
    line-height: 1.3;
    vertical-align: middle;
}

pagoparFloatLeft {float:left;padding:0px 5px 0px 5px;}

</style>
<div class="wrap" id="dashboardEnvios">
    
    
<h1 class="wp-heading-inline">Configuración avanzada</h1>
<h2>Impuestos - Recargos</h2>
    

<div>
    <form method="post" action="">
        
        


      
<table class="form-table">

        
       
        
        <tr valign="top">
                <th scope="row" class="titledesc">
                        <label for="pagopar_no_agregar_impuestos">Forzar no agregar impuestos (Utilizado para solucionar ciertos problemas de conflictos)</label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <legend class="screen-reader-text"><span>Forzar no agregar impuestos (Utilizado para solucionar ciertos problemas de conflictos)</span></legend>
                        <label for="pagopar_no_agregar_impuestos">
                            <input  class="" type="checkbox" name="pagopar_no_agregar_impuestos" id="pagopar_no_agregar_impuestos" style="" value="1" <?php if ($pagopar_no_agregar_impuestos=='1'): echo ' checked="checked" '; endif;?> /> Forzar eliminar impuestos a la hora de generar el pedido</label><br/>

                        </label><br/>
                    </fieldset>
                </td>

        </tr>

    <tr>
        <th>
            <h2>Gastos</h2>
        </th>
   </tr>
    <td class="forminp">
        <fieldset>
        </fieldset>
    </td>

    <tr valign="top">

        <th scope="row" class="titledesc">
            <label  class=""  style=""/> Gastos Administrativos (%)</label><br/>
        </th>
        <td class="forminp">
            <fieldset>
                 <input  class=""
                         type="number"
                         name="pagopar_gastos_administrativos"
                         id="pagopar_gastos_administrativos"
                         style=""
                         value="<?php echo esc_attr($pagopar_gastos_administrativos);?>"
                         placeholder="Gasto Admin. en %"
                         autocomplete="false" autofocus/><br/>

                </label><br/>
            </fieldset>
        </td>

    </tr>


    <tr valign="top">

        <th scope="row" class="titledesc">
            <label  class=""  style=""/> Texto a mostrar</label><br/>
        </th>
        <td class="forminp">
            <fieldset>
                <input  class=""
                        type="text"
                        name="pagopar_gastos_administrativos_text"
                        id="pagopar_gastos_administrativos_text"
                        style=""
                        value="<?php esc_attr($pagopar_gastos_administrativos_text);?>"
                        placeholder="Texto a mostrar"
                        autocomplete="false"/><br/>

                </label><br/>
            </fieldset>
        </td>

    </tr>

    <?php if (get_woocommerce_currency() === 'USD') : ?>
        <tr valign="top">

            <th scope="row" class="titledesc">
                <label class="" style=""/> Conversión dólar a guaraní</label><br/>
            </th>
            <td class="forminp">
                <fieldset>
                    <input class=""
                           type="number"
                           name="pagopar_conversion_dolar_guarani"
                           id="pagopar_conversion_dolar_guarani"
                           step="1"
                           style=""
                           value="<?php echo esc_attr($pagopar_conversion_dolar_guarani); ?>"
                           placeholder="Conversión dólar a guaraní"
                           autocomplete="false"/><br/>

                    </label><br/>
                </fieldset>
            </td>

        </tr>
    <?php endif; ?>






</table>
        
<?php wp_nonce_field('pagopar_guardar_formulario', 'pagopar_nonce'); ?>
        
<p class="submit">
    <button name="actualizar" class="button-primary woocommerce-save-button" type="submit" id="split_billing_actualizar" value="Guardar los cambios">Actualizar</button>
</p>
        
    </form>    
    
    
    
</div>


</div>
