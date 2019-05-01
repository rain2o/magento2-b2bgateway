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

        var data = window.checkoutConfig.payment.creditkey_gateway;
        var ckClient = new creditKey.Client(data.publicKey, data.endpoint);

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

            getCustomTitle: function() {
              if (this.getTitle() && this.getTitle().trim() !== '') return $('#ck-payment-title').html(this.getTitle());

              var totals = quote.getTotals()();
              var charges = new creditKey.Charges(
                totals.subtotal, 
                totals.base_shipping_amount, 
                totals.base_tax_amount, 
                totals.base_discount_amount, 
                totals.base_grand_total
              );

              // set a default display while loading
              $('#ck-payment-title').html('loading Credit Key...');

              return ckClient.get_marketing_display(charges, "checkout", data.type)
                .then(function(res) {
                  $('#ck-payment-title').html(res);
                });
            },
            
            isDisplayed: function() {
              var data = window.checkoutConfig.payment.creditkey_gateway;
              return data.isCreditKeyDisplayed;
            },
            
            redirectToPayment: function() {
              heap.track('Magento Redirect to Credit Key', { data: data.redirectUrl });
              
              setPaymentInformation(messageContainer, { method: quote.paymentMethod().method })
                .then(function () {
                  creditKey.checkout(data.redirectUrl, 'modal')
                });
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
