jQuery(document).ready(function ($) {
    // Función para aplicar modificaciones a los campos de envío (shipping)
    function applyShippingModifications() {
        var usarFormularioMinimizado = checkoutSettings.usarFormularioMinimizado;
        var eliminarCampoPais = checkoutSettings.eliminarCampoPais;

        if (usarFormularioMinimizado) {
            // Ocultar campos de shipping
            $('#shipping-country').closest('.wc-block-components-country-input').hide();
            $('#shipping-city').closest('.wc-block-components-text-input').hide();
            $('#shipping-address_2').closest('.wc-block-components-text-input').hide();
            $('#shipping-postcode').closest('.wc-block-components-text-input').hide();
        }

        if (eliminarCampoPais) {
            $('#shipping-country').closest('.wc-block-components-country-input').hide();
        }

        // Cambiar etiqueta de 'Estado' a 'Ciudad'
        $('#shipping-state').closest('.wc-block-components-state-input').find('label').text('Ciudad');
    }

    // Función para aplicar modificaciones a los campos de facturación (billing)
    function applyBillingModifications() {
        var usarFormularioMinimizado = checkoutSettings.usarFormularioMinimizado;
        var eliminarCampoPais = checkoutSettings.eliminarCampoPais;

        if (usarFormularioMinimizado) {
            // Ocultar campos de billing
            $('#billing-country').closest('.wc-block-components-country-input').hide();
            $('#billing-city').closest('.wc-block-components-text-input').hide();
            $('#billing-address_2').closest('.wc-block-components-text-input').hide();
            $('#billing-postcode').closest('.wc-block-components-text-input').hide();
        }

        if (eliminarCampoPais) {
            $('#billing-country').closest('.wc-block-components-country-input').hide();
        }

        // Cambiar etiqueta de 'Estado' a 'Ciudad'
        $('#billing-state').closest('.wc-block-components-state-input').find('label').text('Ciudad');
    }

    // Observer para detectar la carga de los campos de envío (shipping)
    function observeShippingForm() {
        if ($('#shipping').length) {
            applyShippingModifications();
        } else {
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if ($(mutation.target).find('#shipping').length) {
                        applyShippingModifications();
                    }
                });
            });

            // Observar el cuerpo del documento para detectar cambios en el DOM
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    // Observer para detectar la carga de los campos de facturación (billing)
    function observeBillingForm() {
        if ($('#billing').length) {
            applyBillingModifications();
        } else {
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if ($(mutation.target).find('#billing').length) {
                        applyBillingModifications();
                    }
                });
            });

            // Observar el cuerpo del documento para detectar cambios en el DOM
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    // Llamar a los observers de shipping y billing
    observeShippingForm();
    observeBillingForm();
});
