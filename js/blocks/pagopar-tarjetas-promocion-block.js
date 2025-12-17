// Verificar si hay promociones en settingsPagopar
if (settingsPagopar.promociones && settingsPagopar.promociones.length > 0) {
    const medio_pago = 'pagopar_tarjetas_promocion';

    if (settingsPagopar.medios_pago.hasOwnProperty(medio_pago)) {
        const value = settingsPagopar.medios_pago[medio_pago];
        const promociones = settingsPagopar.promociones || [];
        const isSinglePromotion = promociones.length === 1;
        
        const Label = () => {
            let description = '';
            let images = [];

            if (Array.isArray(value.datos_adicionales_agrupados)) {
                const firstAdditional = value.datos_adicionales_agrupados[0];
                description = firstAdditional.descripcion_principal;

                if (firstAdditional.imagen_principal) {
                    images = firstAdditional.imagen_principal.map(img => (
                        createElement('span', { className: 'method_item' },
                            createElement('img', { src: img.url, alt: '' })
                        )
                    ));
                }
            }

            // Cambiar el título si hay más de una promoción
            const title = (settingsPagopar.promociones.length > 1) ? 'Promociones Tarjeta/QR' : value.title;

            return createElement('div', null,
                createElement('div', null, window.wp.htmlEntities.decodeEntities(title || '')),
                createElement('span', { className: 'sub' }, window.wp.htmlEntities.decodeEntities(isSinglePromotion ? description : "" || '')),
                createElement('span', { className: 'methods_group methods_grupo_label' }, images)
            );
        };

        const Content = (props) => {
            handlePaymentMethodClick();
            const { eventRegistration, emitResponse } = props;
            const { onPaymentSetup } = eventRegistration;

            useEffect(() => {
                const unsubscribe = onPaymentSetup(async () => {
                    const selectedPaymentMethod = document.querySelector(`input[name="sub_payment_method_${medio_pago}"]:checked`);

                    if (selectedPaymentMethod) {
                        const selectedValue = selectedPaymentMethod.value;

                        return {
                            type: emitResponse.responseTypes.SUCCESS,
                            meta: {
                                paymentMethodData: {
                                    "id_promocion": selectedValue
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

            // Crear subinputs para las promociones
            const promocionesContent = promociones.map((promocion, index) => {
                return createElement('li', {
                    key: promocion.id_promocion,
                    style: { display: isSinglePromotion ? 'none' : '' }
                },
                    createElement('label', {
                        className: 'sub_payment_method',
                        htmlFor: `sub_payment_method_${promocion.id_promocion}`
                    },
                        createElement('input', {
                            id: `sub_payment_method_${promocion.id_promocion}`,
                            type: 'radio',
                            className: 'input-radio',
                            name: `sub_payment_method_${medio_pago}`,
                            value: promocion.id_promocion,
                            'data-order_button_text': '',
                            defaultChecked: isSinglePromotion // Check if there's only one promotion
                        }),
                        `${promocion.titulo} - (${promocion.porcentaje}%)`
                    )
                );
            });

            return createElement('div', { className: `payment_box payment_method_${medio_pago}`, style: { display: 'block' } },
                createElement('ul', { className: 'pagopar_payments' }, promocionesContent),
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
            ariaLabel: (settingsPagopar.promociones.length > 1) ? 'Promociones Tarjeta/QR' : value.title,
            supports: {
                features: settingsPagopar.supports,
            },
        };

        // Registrar el método de pago específico
        window.registerPaymentMethod(paymentMethod);
    }
}
