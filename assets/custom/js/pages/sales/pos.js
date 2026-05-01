"use strict";

// Class definition
var TKAppPos = function() {
    // Private functions
    var initDialer = function() {
        // Initialize dialers for QTY input
        const dialerElements = document.querySelectorAll('[data-kt-dialer="true"]');
        dialerElements.forEach(el => {
            KTDialer.getInstance(el);
        });
    }

    var handlePaymentMethod = function() {
        // Simple toggle for payment method buttons
        const buttons = document.querySelectorAll('[data-kt-pos-payment-method="true"]');
        buttons.forEach(btn => {
            btn.addEventListener('click', e => {
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
    }

    // Public methods
    return {
        init: function() {
            initDialer();
            handlePaymentMethod();
            console.log("POS Initialized");
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    TKAppPos.init();
});
