/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    map: {
        '*': {
            'stripe_payments': 'StripeIntegration_Payments/js/stripe_payments',
            'stripe_payments_express': 'StripeIntegration_Payments/js/stripe_payments_express'
        }
    },
    config: {
        mixins: {
            'Magento_Ui/js/view/messages': {
                'StripeIntegration_Payments/js/messages-mixin': true
            },
            'MSP_ReCaptcha/js/ui-messages-mixin': {
                'StripeIntegration_Payments/js/messages-mixin': true
            }
        }
    }
};
