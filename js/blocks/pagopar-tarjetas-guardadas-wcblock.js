medio_pago = 'pagopar_tarjetas_guardadas';

if (settingsPagopar.medios_pago.hasOwnProperty(medio_pago) && settingsPagopar.logged) {
    const value = settingsPagopar.medios_pago[medio_pago];

    const Label = () => {
        let description = "";
        let images = [];

        if (value.datos_adicionales_agrupados && value.datos_adicionales_agrupados.length > 0) {
            description = value.datos_adicionales_agrupados[0].descripcion_principal;
            if (value.datos_adicionales_agrupados[0].imagen_principal) {
                images = value.datos_adicionales_agrupados[0].imagen_principal.map(img => (
                    createElement('span', { className: 'method_item' },
                        createElement('img', { src: img.url, alt: '' })
                    )
                ));
            }
        }
        
        return createElement('div', null,
            createElement('div', null, window.wp.htmlEntities.decodeEntities(value.title || '')),
            createElement('span', { className: 'sub' }, window.wp.htmlEntities.decodeEntities(description || '')),
            createElement('span', { className: 'methods_group methods_grupo_label' }, images)
        );
    };

    const Content = (props) => {
        handlePaymentMethodClick();
        const { eventRegistration, emitResponse } = props;
        const { onPaymentSetup } = eventRegistration;

        useEffect(() => {
            const unsubscribe = onPaymentSetup(async () => {
                // Captura el valor del input seleccionado
                const selectedPaymentMethod = document.querySelector(`input[name="sub_payment_method_${medio_pago}"]:checked`);

                if (selectedPaymentMethod) {
                    const selectedValue = selectedPaymentMethod.value;

                    return {
                        type: emitResponse.responseTypes.SUCCESS,
                        meta: {
                            paymentMethodData: {
                                "pagopar_tipo_pago": selectedValue // Enviar el valor del input seleccionado
                            },
                        },
                    };
                }

                return {
                    type: emitResponse.responseTypes.ERROR,
                    message: 'Por favor, selecciona una tarjeta guardada válida.',
                };
            });

            return () => {
                unsubscribe();
            };
        }, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup]);

        let additionalData = value.datos_adicionales || [];

        // Obtener el título del botón del primer objeto de value.datos_adicionales
        const firstAdditional = additionalData[0];
        const buttonTitle = firstAdditional ? firstAdditional.titulo : 'Agregar Tarjeta';

        // Recorrer las tarjetas registradas y generar HTML
        const tarjetasRegistradas = (settingsPagopar.tarjetasCatastradas && settingsPagopar.tarjetasCatastradas.respuesta) ? settingsPagopar.tarjetasCatastradas.resultado.map(tarjeta => (
            createElement('li', { key: tarjeta.alias_token },
                createElement('label', {
                    className: 'sub_payment_method',
                    htmlFor: `sub_payment_method_${tarjeta.alias_token}`
                },
                    createElement('input', {
                        id: `sub_payment_method_${tarjeta.alias_token}`,
                        type: 'radio',
                        className: 'input-radio',
                        name: `sub_payment_method_${medio_pago}`,
                        value: tarjeta.alias_token,
                        'data-order_button_text': ''
                    }),
                    `${tarjeta.descripcion} (${tarjeta.tarjeta_numero})`
                ),
                createElement('span', { className: 'methods_group' },
                    createElement('span', { className: 'method_item' },
                        createElement('img', { src: tarjeta.url_logo, alt: '' })
                    )
                )
            )
        )) : '';

        return createElement('div', { className: `payment_box payment_method_${medio_pago}`, style: { display: 'block' } },
            createElement('ul', { className: 'pagopar_payments' }, tarjetasRegistradas),
            createElement('button', {
                id: 'pagoparAddCard',
                type: 'button',
                className: 'wc-block-components-button wp-element-button contained',
                onClick: () => window.agregarTarjeta() // Asegúrate de que la función agregarTarjeta esté definida en el contexto global
            }, buttonTitle),
            createElement('p', { className: 'pagopar-copy' }, 'Procesado por Pagopar ',
                createElement('img', { src: settingsPagopar.icon, alt: 'Pagopar' })
            )
        );
    };

    const paymentMethod = {
        name: medio_pago,
        label: createElement(Label),
        content: createElement(Content),
        edit: createElement(Content),
        canMakePayment: () => true,
        ariaLabel: value.title,
        supports: {
            features: settingsPagopar.supports,
        },
    };

    window.registerPaymentMethod(paymentMethod);
}
