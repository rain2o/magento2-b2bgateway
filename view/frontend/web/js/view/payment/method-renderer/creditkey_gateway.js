/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
      'jquery',
      'Magento_Checkout/js/view/payment/default',
      'Magento_Checkout/js/action/place-order',
      'Magento_Checkout/js/action/select-payment-method',
      'Magento_Checkout/js/checkout-data',
      'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function ($, Component, placeOrderAction, selectPaymentMethodAction, checkoutData, additionalValidators) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Creditkey_B2BGateway/payment/form',
                transactionResult: ''
            },

            getCode: function() {
                return 'creditkey_gateway';
            },

            redirectAfterPlaceOrder: false,
            placeOrder: function(data, event) {
              if (event) {
                event.preventDefault();
              }

              var loginForm = $('.form-login').validation();
              var ignore = null;

              // run both form validations
              if (!loginForm.validation('isValid')) {
                return false;
              }

              var self = this,
                placeOrder;
              
              if (this.validate() && additionalValidators.validate()) {
                this.isPlaceOrderActionAllowed(false);
                placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                $.when(placeOrder)
                  .fail(function() {
                    self.isPlaceOrderActionAllowed(true);
                  }).done(this.afterPlaceOrder.bind(this));
                return true;
              }
              return false;
            },

            selectPaymentMethod: function() {
              selectPaymentMethodAction(this.getData());
              checkoutData.setSelectedPaymentMethod(this.item.method);
              return true;
            },

            afterPlaceOrder: function() {
              var data = window.checkoutConfig.payment.creditkey_gateway;
              var billingData = checkoutData.getBillingAddressFromData();
              var returnParams = '?ref=' + data.quoteId + '&key=%CKKEY%&secure=true';

              var payload = {
                first_name: billingData.firstname,
                last_name: billingData.last_name,
                address1: billingData.street[0],
                address2: billingData.street[1],
                city: billingData.city,
                state: billingData.region,
                zip: billingData.postcode,
                phone: billingData.telephone,
                amount: window.checkoutConfig.totalsData.subtotal,
                return_url: data.returnUrl + returnParams,
                cancel_url: data.cancelUrl + returnParams,
                merchant: data.publicKey,
                email: checkoutData.getInputFieldEmailValue()
              };

              window.location = data.redirectUrl + $.param(payload); 
            }
        });
    }
);
