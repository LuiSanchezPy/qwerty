medio_pago = 'pagopar_tarjetas';

if (settingsPagopar.medios_pago.hasOwnProperty(medio_pago)) {
    const value = settingsPagopar.medios_pago[medio_pago];

    const Label = () => {
        const firstKey = Object.keys(value.datos_adicionales)[0];
        const firstAdditional = value.datos_adicionales[firstKey];
        const description = value.datos_adicionales_agrupados[firstKey]?.descripcion_principal || '';
        const images = firstAdditional.imagen.map(img => (
            createElement('span', { className: 'method_item' },
                createElement('img', { src: img.url, alt: '' })
            )
        ));

        const promo = createElement('span', { className: 'promo' },
            'Hasta ',
            createElement('strong', null, '12 cuotas'),
            ' sin intereses con tarjetas de crédito Banco Familiar.'
        );

        return createElement('div', null,
            createElement('div', null, window.wp.htmlEntities.decodeEntities(value.title || '')),
            createElement('span', { className: 'sub' }, window.wp.htmlEntities.decodeEntities(description || '')),
            promo,
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
                    message: 'Por favor, selecciona un método de pago válido.',
                };
            });

            return () => {
                unsubscribe();
            };
        }, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup]);

        const additionalData = value.datos_adicionales || {};
        const additionalKeys = Object.keys(additionalData); // Obtener las claves de los datos adicionales
    
        // Verificar si hay solo un elemento
        const isSingleItem = additionalKeys.length === 1;
    
        const additionalContent = additionalKeys.map(key => {
            const additional = additionalData[key];
            const images = additional.imagen.map(img => (
                createElement('span', { className: 'method_item' },
                    createElement('img', { src: img.url, alt: '' })
                )
            ));

            return createElement('li', { key: key, style: { display: isSingleItem ? 'none' : '' } },
                createElement('label', {
                    className: 'sub_payment_method',
                    htmlFor: `sub_payment_method_${additional.forma_pago}`
                },
                    createElement('input', {
                        id: `sub_payment_method_${additional.forma_pago}`,
                        type: 'radio',
                        className: 'input-radio',
                        name: `sub_payment_method_${medio_pago}`,
                        value: additional.forma_pago,
                        'data-order_button_text': '',
                        checked: isSingleItem
                    }),
                    additional.titulo
                ),
                createElement('span', { className: 'methods_group' }, images)
            );
        });

        return createElement('div', { className: `payment_box payment_method_${medio_pago}`, style: { display: 'block' } },
            createElement('ul', { className: 'pagopar_payments' }, additionalContent),
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
