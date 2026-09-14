define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Demo_PickupDelivery/js/model/pickup-point'
], function (Component, ko, quote, pickupPoint) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Demo_PickupDelivery/pickup-point-summary'
        },

        initialize: function () {
            var config,
                self = this;

            this._super();

            config = window.checkoutConfig.demoPickupDelivery || {};
            this.points = config.points || [];

            this.selectedPoint = ko.pureComputed(function () {
                return ko.utils.arrayFirst(self.points, function (point) {
                    return Number(point.id) === Number(pickupPoint.selectedPointId());
                });
            });

            this.isVisible = ko.pureComputed(function () {
                var shippingMethod = quote.shippingMethod();

                return shippingMethod
                    && shippingMethod.carrier_code === 'pickupdelivery'
                    && self.selectedPoint();
            });

            return this;
        }
    });
});
