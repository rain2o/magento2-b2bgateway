/*browser:true*/
/*global define*/
define(
    [
      'jquery',
      'Magento_Checkout/js/view/payment/default',
      'Magento_Checkout/js/action/place-order',
      'Magento_Checkout/js/action/select-payment-method',
      'Magento_Checkout/js/checkout-data',
      'Magento_Checkout/js/model/payment/additional-validators',
      'Magento_Checkout/js/action/set-payment-information',
      'Magento_Checkout/js/model/quote',
      'Magento_SalesRule/js/model/payment/discount-messages',
      'creditkeysdk'
    ],
    function ($, Component, placeOrderAction, selectPaymentMethodAction, checkoutData, additionalValidators, setPaymentInformation, quote, messageContainer, creditKey) {
        'use strict';

        /*var data = window.checkoutConfig.payment.creditkey_gateway;*/
        //var ckClient = new creditKey.Client(data.publicKey, 'development');
        /*ckClient.is_displayed_in_checkout();*/

        return Component.extend({
            defaults: {
                template: 'CreditKey_B2BGateway/payment/form',
                transactionResult: ''
            },

            getPaymentAcceptanceMarkSrc: function () {
              return window.checkoutConfig.payment.creditkey_gateway.assetSrc;
            },

            getCode: function() {
                return 'creditkey_gateway';
            },

            getData: function() {
              return {
                'method': this.item.method,
                'additional_data': {
                  'transaction-result': ''
                }
              }
            },
            
            isDisplayed: function() {
              var data = window.checkoutConfig.payment.creditkey_gateway;
              return data.isCreditKeyDisplayed;
            },
            
            redirectToPayment: function() {
              var data = window.checkoutConfig.payment.creditkey_gateway;

              heap.track('Magento Redirect to Credit Key', { data: data.redirectUrl });
              
              setPaymentInformation(messageContainer, { method: quote.paymentMethod().method })
                .then(res => creditKey.checkout(data.redirectUrl, 'modal'));
                //.then(res => window.location = data.redirectUrl);
            },

            redirectAfterPlaceOrder: false,

            selectPaymentMethod: function() {
              heap.track('Magento Payment Selection', { data: this.getData() });

              selectPaymentMethodAction(this.getData());
              checkoutData.setSelectedPaymentMethod(this.item.method);
              return true;
            }
        });
    }
);
