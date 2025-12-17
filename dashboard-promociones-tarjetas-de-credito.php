<?php

global $wpdb;
$urlAdmin = $_SERVER['REQUEST_URI'];


    $bancos = array(
        ""=>"Seleccionar",
        "Itau" => "Itau",
        "Ueno" => "Ueno",
        "Atlas" => "Atlas",
        "Familiar" => "Familiar"
    );


    $tipos = array(
        ""=>"Seleccionar",
        "Tarjeta" => "Tarjeta de credito",
        "QR" => "QR",
        
    );


    # guardamos valores del form
    $tabla = $wpdb->prefix . 'pagopar_promociones_tarjetas';

  
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrar'])) {
    
        if (!isset($_POST['pagopar_nonce']) || !wp_verify_nonce($_POST['pagopar_nonce'], 'pagopar_guardar_formulario')) {
            wp_die('No autorizado.');
        }            
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Actualización directa necesaria para la tabla personalizada wp_pagopar_promociones_tarjetas del plugin Pagopar, sin equivalente en APIs de WordPress.
        $wpdb->update(
            $tabla,
            array(
                'estado' => 0,
            ),
            array('id' => intval($_POST['pagopar_id_promocion']))
        );
    }

    $id_tarjeta_promo_pagopar = 0;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar'])) {

        if (!isset($_POST['pagopar_nonce']) || !wp_verify_nonce($_POST['pagopar_nonce'], 'pagopar_guardar_formulario')) {
            wp_die('No autorizado.');
        }    

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Consulta directa necesaria para la tabla personalizada wp_pagopar_promociones_tarjetas del plugin Pagopar, sin equivalente en APIs de WordPress.
        $sql = $wpdb->prepare(
            "SELECT * FROM $tabla WHERE estado=1 AND id = %d ORDER BY id DESC",
            intval($_POST['pagopar_id_promocion'])
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Consulta directa necesaria para la tabla personalizada wp_pagopar_promociones_tarjetas del plugin Pagopar, sin equivalente en APIs de WordPress.
        $results_promo = $wpdb->get_results($sql, ARRAY_A);
       
        $id_tarjeta_promo_pagopar = 0;
        $banco = null;
        $tipo=null;
        $descripcion=null;
        $descuento = null;            
        $fecha_inicio = null;                
        $fecha_fin= null;  
        $codigo_promocion= null;                                    
        if($results_edit){
            foreach ($results_edit as $row_edit) {
                $id_tarjeta_promo_pagopar = $row_edit['id'];
                $banco = $row_edit['banco'];
                $tipo = $row_edit['tipo'];
                $descripcion = $row_edit['descripcion'];
                $descuento = $row_edit['porcentaje'];
                $fecha_inicio = $row_edit['inicio_promocion'];
                $fecha_fin = $row_edit['fin_promocion'];
                $codigo_promocion = $row_edit['codigo_promocion'];
            }
        }
    }



    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    
        if (!isset($_POST['pagopar_nonce']) || !wp_verify_nonce($_POST['pagopar_nonce'], 'pagopar_guardar_formulario')) {
            wp_die('No autorizado.');
        }    

        if(!empty($_POST['filtro_banco'])){

                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Inserción directa necesaria para la tabla personalizada wp_pagopar_promociones_tarjetas del plugin Pagopar, sin equivalente en APIs de WordPress.
                    $wpdb->insert(
                        $tabla,
                        array(
                            'banco' => $_POST['filtro_banco'],
                            'descripcion' => $_POST['pagopar_descripcion'],
                            'porcentaje' => $_POST['pagopar_porcentaje'],
                            'inicio_promocion' => $_POST['pagopar_fecha_inicio'],
                            'fin_promocion' => $_POST['pagopar_fecha_fin'],
                            'codigo_promocion' => $_POST['pagopar_codigo_promocion'],
                            'tipo' => $_POST['tipo'],
                        ),
                        array(
                            '%s', 
                            '%s', 
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s'
                        )
                    );

    }
        
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modificar'])) {

        if (!isset($_POST['pagopar_nonce']) || !wp_verify_nonce($_POST['pagopar_nonce'], 'pagopar_guardar_formulario')) {
            wp_die('No autorizado.');
        }            

        $datos_a_actualizar = array(
            'banco' => $_POST['filtro_banco'],
            'descripcion' => $_POST['pagopar_descripcion'],
            'porcentaje' => $_POST['pagopar_porcentaje'],
            'inicio_promocion' => $_POST['pagopar_fecha_inicio'],
            'fin_promocion' => $_POST['pagopar_fecha_fin'],
            'codigo_promocion' => $_POST['pagopar_codigo_promocion'],
            'tipo' => $_POST['tipo'],
        );

        $condicion = array(
            'id' => $_POST['id_modificar']
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Actualización directa necesaria para la tabla personalizada wp_pagopar_promociones_tarjetas del plugin Pagopar, sin equivalente en APIs de WordPress.
        $wpdb->update(
            $tabla,
            $datos_a_actualizar,
            $condicion,
            array(
                '%s', 
                '%s', 
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            ),
            array(
                '%d' // Asegúrate de usar el formato adecuado para la condición del ID (en este caso, se asume que es un número entero)
            )

            );



    }
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Consulta directa necesaria para la tabla personalizada wp_pagopar_promociones_tarjetas del plugin Pagopar, que almacena datos de promociones y no tiene equivalente en APIs de WordPress.
    $results = $wpdb->get_results("SELECT * 
                                   FROM $tabla
                                   WHERE estado=1 
                                   ORDER BY id DESC", ARRAY_A);
  

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
    
    
<h1 class="wp-heading-inline">Promociones Tarjetas de Crédito</h1>
<h2>Tarjeta</h2>
    

<div>
    <form method="post" action="">
        
        


      
<table class="form-table">

        
       
            <tr valign="top" class="hidden">

            <th scope="row" class="titledesc">
                <label  class=""  style=""/> ID</label><br/>
            </th>
            <td class="forminp">
                <fieldset>
                    
                    <input  class=""
                            type="text"
                            name="id_tarjeta_promo_pagopar"
                            id="id_tarjeta_promo_pagopar"
                            style=""
                            placeholder="ID"
                            value='<?php echo esc_attr($id_tarjeta_promo_pagopar);?>'
                            autocomplete=false
                            
                            
                            /><br/>

                    </label><br/>
                </fieldset>
            </td>

            </tr>

        
        <tr valign="top">
                <th scope="row" class="titledesc">
                        <label for="">Banco</label>
                </th>
                <td class="forminp">
                    <fieldset>
                        
                    <select class="form-control" id="filtro_banco" name="filtro_banco"  autofocus>

                    <?php foreach ($bancos as $valor => $texto):?>
                        <?php if ($valor === $banco):?>
                            <option value=<?php echo esc_attr($valor);?> selected><?php echo esc_html($texto);?></option>
                            <?php else:?>
                             <option value=<?php echo esc_attr($valor);?>><?php echo esc_html($texto);?></option>
                        <?php endif;?>
                     <?php endforeach;?>
                     </select>
                        <br/>
                    </fieldset><br/> 
                </td>

        </tr>

    
   
        <tr valign="top">
                <th scope="row" class="titledesc">
                        <label for="">Tipo</label>
                </th>
                <td class="forminp">
                    <fieldset>
                        
                        <select class="form-control" id="tipo" name="tipo">


                        <?php foreach ($tipos as $valor => $texto):?>
                        <?php if ($valor === $tipo):?>
                            <option value=<?php echo esc_attr($valor);?> selected><?php echo esc_html($texto);?></option>
                            <?php else:?>
                             <option value=<?php echo esc_attr($valor);?>><?php echo esc_html($texto);?></option>
                        <?php endif;?>
                     <?php endforeach;?>
                    
                        </select>
                        <br/>
                    </fieldset><br/>
                </td>

        </tr>
   


    <tr valign="top">

        <th scope="row" class="titledesc">
            <label  class=""  style=""/> Descripción</label><br/>
        </th>
        <td class="forminp">
            <fieldset>
                
                <input  class=""
                        type="text"
                        name="pagopar_descripcion"
                        id="pagopar_descripcion"
                        style=""
                        value='<?php echo esc_attr($descripcion);?>'
                        placeholder="Descripción del desc."
                        autocomplete=false
                        
                        
                        /><br/>

                </label><br/>
            </fieldset>
        </td>

    </tr>


    <tr valign="top">

        <th scope="row" class="titledesc">
            <label  class=""  style=""/> Descuento (%)</label><br/>
        </th>
        <td class="forminp">
            <fieldset>
                <input  class=""
                        type="number"
                        name="pagopar_porcentaje"
                        id="pagopar_porcentaje"
                        style=""
                        placeholder="%"
                        value='<?php echo esc_attr($descuento);?>'
                        autocomplete=false
                        
                        /><br/>

                </label><br/>
            </fieldset>
        </td>

     </tr>


     <tr valign="top">

        <th scope="row" class="titledesc">
            <label  class=""  style=""/> Fecha Inicio</label><br/>
        </th>
        <td class="forminp">
            <fieldset>
                
                <input  class=""
                        type="datetime-local"
                        name="pagopar_fecha_inicio"
                        id="pagopar_fecha_inicio"
                        style=""
                        placeholder="Fecha Inicio"
                        value='<?php echo esc_attr($fecha_inicio);?>'
                        autocomplete=false
                        
                        /><br/>

                </label><br/>
            </fieldset>
        </td>

    </tr>


    <tr valign="top">

        <th scope="row" class="titledesc">
            <label  class=""  style=""/> Fecha Fin</label><br/>
        </th>
        <td class="forminp">
            <fieldset>
                
                <input  class=""
                        type="datetime-local"
                        name="pagopar_fecha_fin"
                        id="pagopar_fecha_fin"
                        style=""
                        placeholder="Fecha Fin"
                        value='<?php echo esc_attr($fecha_fin);?>'
                        autocomplete=false
                        
                        /><br/>

                </label><br/>
            </fieldset>
        </td>

    </tr>


    <tr valign="top">

<th scope="row" class="titledesc">
    <label  class=""  style=""/> Código Promoción</label><br/>
</th>
<td class="forminp">
    <fieldset>
        
        <input  class=""
                type="text"
                name="pagopar_codigo_promocion"
                id="pagopar_codigo_promocion"
                style=""
                placeholder="Codigo promoción"
                value='<?php echo esc_attr($codigo_promocion);?>'
                autocomplete=false
                
                /><br/>

        </label><br/>
    </fieldset>
</td>

</tr>







</table>

<p class="submit">

    <?php if($id_tarjeta_promo_pagopar>0):?> 
        <input id="id_modificar" name="id_modificar" value='<?php echo esc_attr($id_tarjeta_promo_pagopar);?>' class="hidden">
        <button name="modificar" class="button-primary woocommerce-save-button" type="submit" id="split_billing_actualizar" value="Modificar los cambios">Modificar</button>

    <?php else:?>

        <button name="guardar" class="button-primary woocommerce-save-button" type="submit" id="split_billing_actualizar" value="Guardar los cambios">Guardar</button>

   <?php endif;?>
   <?php wp_nonce_field('pagopar_guardar_formulario', 'pagopar_nonce'); ?>

</p>
        
<?php 
if ($results) {
    echo '<table class="form-table">';
    echo '<tr>
    <th>Banco</th>
    <th>Tipo</th>
    <th>Descripción</th>
    <th>Descuento(%)</th>
    <th>Fecha Inicio</th>
    <th>Fecha Fin</th>
    <th>Código Promoción</th>
    <th>Config.</th>
    </tr>';
    
    foreach ($results as $row) {
        echo '<tr>';
        echo '<td>' . esc_html($row['banco']) . '</td>';
        echo '<td>' . esc_html($row['tipo']) . '</td>';
        echo '<td>' . esc_html($row['descripcion']) . '</td>';
        echo '<td>' . esc_html($row['porcentaje']) . '</td>';
        echo '<td>' . esc_html(date("d/m/Y H:i:s",strtotime($row['inicio_promocion']))) . '</td>';
        echo '<td>' . esc_html(date("d/m/Y H:i:s",strtotime($row['fin_promocion']))) . '</td>';
        echo '<td>' . esc_html($row['codigo_promocion']) . '</td>';
        echo '<td>
            <form method="post" action="">   
            <input class="hidden" id="pagopar_id_promocion" name="pagopar_id_promocion" value='.esc_attr($row['id']).'>   
            <button class="form-control" type="submit" id="borrar" name="borrar">Eliminar</button>
            <button class="form-control" type="submit" id="editar" name="editar">Editar</button>
            </form>
        </td>';
        echo '</tr>';
    }
    
    echo '</table>';
} 
 
 
 ?>
        
        
        

    </form>    
    
    
    
</div>


</div>


<style>


.form-table th, .form-table td {
    border: 1px solid #dddddd;
    padding: 8px;
}

#pagopar_descripcion{
    width: 600px;
}

</style>
