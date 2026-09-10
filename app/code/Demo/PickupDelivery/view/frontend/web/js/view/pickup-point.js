define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Demo_PickupDelivery/js/model/pickup-point'
], function (Component, ko, quote, pickupPoint) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Demo_PickupDelivery/pickup-point'
        },

        initialize: function () {
            this._super();

            var config = window.checkoutConfig.demoPickupDelivery || {};

            this.points = config.points || [];
            this.selectedPoint = pickupPoint.selectedPointId;

            this.isVisible = ko.pureComputed(function () {
                var shippingMethod = quote.shippingMethod();

                return shippingMethod
                    && shippingMethod.carrier_code === 'pickupdelivery';
            });

            return this;
        }
    });
});
