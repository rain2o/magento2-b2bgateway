/*browser:true*/
/*global define*/
define(
    [
      'jquery',
      'creditkeysdk'
    ],
    function ($, creditKey) {
        'use strict';

        var globalOptions = {
            ckConfig: null
        };

        $.widget('creditkey.marketing', {
            options: globalOptions,

            _init: function initMarketing() {
                var elem = this.element;
                var ckClient = new creditKey.Client(
                    this.options.ckConfig.publicKey,
                    this.options.ckConfig.endpoint
                );
                var charges = new creditKey.Charges(...this.options.ckConfig.charges);

                return ckClient.get_marketing_display(charges, "pdp", "text")
                    .then(function(res) {
                        elem.html(res);
                    });
            }
        });

        return $.creditkey.marketing;
    }
);
