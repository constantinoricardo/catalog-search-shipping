define([
    'jquery',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor'
], function ($,storage, errorProcessor) {
    'use strict';
    $.widget('mage.estimateRate', {

        _create: function () {
            var self = this;
            $('#getrate').on('click', function (e) {

                let serviceUrl = 'rest/default/V1/search/';
                let cep = $('input#cep').val();
                let payload = JSON.stringify({
                        address: {
                            'city': 'São Paulo',
                            'country_id': 'BR',
                            'postcode': cep,
                        },
                        productId: $("input[name='product']").val()
                    }
                );

                self.getRate(payload, serviceUrl);

            });
        },

        getRate: function (payload,serviceUrl) {
            let $shippings = $('#shippings');

            $shippings.trigger('processStart');

            storage.post(
                serviceUrl, payload, false
            ).done(
                function (result) {
                    let table = "<table>";
                    $.each(result, function(idx, data) {
                        let price = data.amount;
                        let amount = price.toLocaleString('pt-br', {minimumFractionDigits: 2});
                        table += "<tr><td>"+data.carrier_title+"</td><td>"+data.method_title+"</td><td>R$ "+amount+"</td></tr>";
                        table += "<tr><td colspan='3'></td></tr>";
                    });

                    table += "</table>";
                    $shippings.html(table);

                    $shippings.trigger('processStop');
                }
            ).fail(
                function (response) {
                    $shippings.html("");
                    $shippings.trigger('processStop');
                    errorProcessor.process(response);
                }
            );
        },
    });
    return $.mage.estimateRate;
});
