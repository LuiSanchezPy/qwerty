medio_pago = 'pagopar_billeteras';

if (settingsPagopar.medios_pago.hasOwnProperty(medio_pago)) {
    const value = settingsPagopar.medios_pago[medio_pago];

    const Label = () => {
        let description = "";
        let images = [];

        if (Array.isArray(value.datos_adicionales_agrupados)) {
            description = value.datos_adicionales_agrupados[0].descripcion_principal;
            if (value.datos_adicionales_agrupados[0].imagen_principal) {
                images = value.datos_adicionales_agrupados[0].imagen_principal.map(img => (
                    createElement('span', { className: 'method_item' },
                        createElement('img', { src: img.url, alt: '' })
                    )
                ));
            }
        } else {
            const firstKey = Object.keys(value.datos_adicionales_agrupados).find(key => value.datos_adicionales_agrupados[key] && value.datos_adicionales_agrupados[key].descripcion_principal);
            if (firstKey) {
                description = value.datos_adicionales_agrupados[firstKey].descripcion_principal;
                if (value.datos_adicionales_agrupados[firstKey].imagen_principal) {
                    images = value.datos_adicionales_agrupados[firstKey].imagen_principal.map(img => (
                        createElement('span', { className: 'method_item' },
                            createElement('img', { src: img.url, alt: '' })
                        )
                    ));
                }
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
                                "pagopar_tipo_pago": selectedValue
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
        let additionalData = value.datos_adicionales || {};

        $("#radio-control-wc-payment-method-options-pagopar_billeteras").val("pagopar");
        const additionalContent = Object.keys(additionalData).map((adKey) => {
            const additional = additionalData[adKey];
            let images = [];

            if ((additional.imagen_principal && additional.imagen && Object.keys(additional.imagen).length > Object.keys(additional.imagen_principal).length) || (additional.imagen && Object.keys(additional.imagen).length === 1 && additional.imagen[0].url)) {
                images = additional.imagen.map(img => (
                    createElement('span', { className: 'method_item' },
                        createElement('img', { src: img.url, alt: '' })
                    )
                ));
            } else if (additional.imagen_principal) {
                images = additional.imagen_principal.map(img => (
                    createElement('span', { className: 'method_item' },
                        createElement('img', { src: img.url, alt: '' })
                    )
                ));
            }

            return createElement('li', { key: adKey },
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
                        'data-order_button_text': ''
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
        canMakePayment: (props) => true,
        ariaLabel: value.title,
        supports: {
            features: settingsPagopar.supports,
        },
    };

    window.registerPaymentMethod(paymentMethod);
}
