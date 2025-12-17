jQuery(document).ready(function ($) {
    function applyCheckoutModifications() {
        var usarFormularioMinimizado = cartSettings.usarFormularioMinimizado;
        $('.wc-block-components-address-form__state .wc-blocks-components-select__label').text('Ciudad');
        $('.wc-block-components-address-form__city').hide();
        if(usarFormularioMinimizado) {
            $('.wc-block-components-address-form__postcode').hide();
        }
    }

    function modifyAddressFieldsWithDelay() {
        setTimeout(applyCheckoutModifications, 1);
    }

    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            const changeAddressButton = document.querySelector('#wc-block-components-totals-shipping__change-address__link');
            if (changeAddressButton) {
                $(changeAddressButton).on('click', modifyAddressFieldsWithDelay);
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
