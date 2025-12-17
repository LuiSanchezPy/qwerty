const settingsPagopar = window.wc.wcSettings.getSetting('pagopar_data', {});

function registerPaymentMethod(key, value) {
    let label = window.wp.htmlEntities.decodeEntities(value.title) || window.wp.i18n.__('Pagopar', 'pagopar-for-woocommerce');

    const Label = () => {
        let description = "";
        let images = "";

        if (Array.isArray(value.datos_adicionales_agrupados)) {
            description = value.datos_adicionales_agrupados[0].descripcion_principal;
            if (value.datos_adicionales_agrupados[0].imagen_principal) {
                images = value.datos_adicionales_agrupados[0].imagen_principal.map(img => `
                    <span class="method_item">
                        <img src="${img.url}" alt="">
                    </span>
                `).join('');
            }
        } else {
            const firstKey = Object.keys(value.datos_adicionales_agrupados).find(key => value.datos_adicionales_agrupados[key] && value.datos_adicionales_agrupados[key].descripcion_principal);
            if (firstKey) {
                description = value.datos_adicionales_agrupados[firstKey].descripcion_principal;
                if (value.datos_adicionales_agrupados[firstKey].imagen_principal) {
                    images = value.datos_adicionales_agrupados[firstKey].imagen_principal.map(img => `
                        <span class="method_item">
                            <img src="${img.url}" alt="">
                        </span>
                    `).join('');
                }
            }
        }
        return `<div>
            ${window.wp.htmlEntities.decodeEntities(value.title || '')}
            <span class="sub">
                ${window.wp.htmlEntities.decodeEntities(description || '')}
            </span>
            <span class="methods_group methods_grupo_label">
                ${images}
            </span>
        </div>`;
    }
    
    const Content = () => {
        let additionalData = value.datos_adicionales || {};
        
        // Construir el contenido de los datos adicionales
        const additionalContent = Object.keys(additionalData).map((adKey) => {
            const additional = additionalData[adKey];
            let images = "";

            if ((additional.imagen_principal && additional.imagen && Object.keys(additional.imagen).length > Object.keys(additional.imagen_principal).length) || (additional.imagen && Object.keys(additional.imagen).length === 1 && additional.imagen[0].url)) {
                images = additional.imagen.map(img => `
                    <span class="method_item">
                        <img src="${img.url}" alt="">
                    </span>
                `).join('');
            } else if (additional.imagen_principal) {
                images = additional.imagen_principal.map(img => `
                    <span class="method_item">
                        <img src="${img.url}" alt="">
                    </span>
                `).join('');
            }

            return `
                <li>
                    <label class="sub_payment_method" for="sub_payment_method_${additional.forma_pago}">
                        <input id="sub_payment_method_${additional.forma_pago}" type="radio" class="input-radio" name="sub_payment_method_${key}" value="${additional.forma_pago}" data-order_button_text="">
                        ${additional.titulo}
                    </label>
                    <span class="methods_group">
                        ${images}
                    </span>
                </li>
            `;
        }).join('');

        return `<div class="payment_box payment_method_${key}" style="display: block;">
                    <ul class="pagopar_payments">
                        ${additionalContent}
                    </ul>
                    <p class="pagopar-copy">Procesado por Pagopar 
                        <img src="http://137.184.98.229/wp-content/plugins/pagopar-woocommerce-gateway/images/medios-pagos/iso-pagopar.png" alt="Pagopar">
                    </p>
                </div>`;
    };

    const handlePaymentMethodClick = () => {
    
        // Ocultar y mostrar las cajas de pago según la opción seleccionada
        jQuery(".wc-block-components-radio-control__option .payment_box").hide();
        jQuery(this).closest('.wc-block-components-radio-control__option').find('.payment_box').show();
    
        // Añadir la clase 'active' a los métodos de pago seleccionados
        jQuery('.pagopar_payments > li').find('input[type=radio]:checked').closest('li').addClass('active');
        jQuery(".pagopar_payments > li > label").click(function() {
            jQuery(this).closest('.pagopar_payments').children('li').removeClass('active');
            jQuery(this).closest('li').addClass('active');
        });
    };
    
    const paymentMethod = {
        name: key,
        label: Object(window.wp.element.createElement)('div', {
            dangerouslySetInnerHTML: { __html: Label() }
        }),
        content: Object(window.wp.element.createElement)('div', {
            dangerouslySetInnerHTML: { __html: Content() },
            onClick: handlePaymentMethodClick
        }),
        edit: Object(window.wp.element.createElement)('div', {
            dangerouslySetInnerHTML: { __html: Content() },
            onClick: handlePaymentMethodClick
        }),
        canMakePayment: () => true,
        ariaLabel: label,
        supports: {
            features: settingsPagopar.supports,
        },
    };

    window.wc.wcBlocksRegistry.registerPaymentMethod(paymentMethod);
}

// Recorrer el atributo `medios_pago` y registrar cada método de pago
for (let key in settingsPagopar.medios_pago) {
    if (settingsPagopar.medios_pago.hasOwnProperty(key)) {
        registerPaymentMethod(key, settingsPagopar.medios_pago[key]);
    }
}
