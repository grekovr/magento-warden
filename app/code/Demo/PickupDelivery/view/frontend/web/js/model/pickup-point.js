define([
    'ko'
], function (ko) {
    'use strict';

    var config = window.checkoutConfig.demoPickupDelivery || {};

    return {
        selectedPointId: ko.observable(config.selectedPointId || null)
    };
});
