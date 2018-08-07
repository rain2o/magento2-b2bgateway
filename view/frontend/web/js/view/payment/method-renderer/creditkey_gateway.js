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
      'Magento_Checkout/js/model/payment/additional-validators',
      'Magento_Checkout/js/action/set-payment-information',
      'Magento_Checkout/js/model/quote',
      'Magento_SalesRule/js/model/payment/discount-messages'

    ],
    function ($, Component, placeOrderAction, selectPaymentMethodAction, checkoutData, additionalValidators, setPaymentInformation, quote, messageContainer) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Creditkey_B2BGateway/payment/form',
                transactionResult: ''
            },

            getPaymentAcceptanceMarkSrc: function () {
              return window.checkoutConfig.payment.creditkey_gateway.assetSrc;
            },

            getCode: function() {
                return 'creditkey_gateway';
            },

            getData: function() {
              var customData = window.checkoutConfig.payment.creditkey_gateway;
              return {
                'method': this.item.method,
                'additional_data': {
                  'ck_public_key': customData.publicKey,
                  'transaction-result': ''
                }
              }
            },

            redirectToPayment: function() {
              var data = window.checkoutConfig.payment.creditkey_gateway;
              var items = window.checkoutConfig.quoteItemData;
              var billingData = checkoutData.getBillingAddressFromData();
              var returnParams = '?ref=' + data.quoteId + '&key=%CKKEY%&secure=true';
              var email = checkoutData.getInputFieldEmailValue();

              items = items.map(function(i) {
                return {
                  merchant_id: i.store_id,
                  name: i.name,
                  price: i.price,
                  quantity: i.qty,
                  sku: i.sku
                }
              });

              if (!email || email === '') {
                email = window.checkoutConfig.customerData.email;
              }

              var payload = {
                first_name: billingData.firstname,
                last_name: billingData.lastname,
                company_name: billingData.company,
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
                email: email,
                cart_items: JSON.stringify(items)
              };

              heap.track('Magento Redirect to Credit Key', { data: payload });
              
              setPaymentInformation(messageContainer, { method: quote.paymentMethod().method })
                .then(res => window.location = data.redirectUrl + $.param(payload));
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
