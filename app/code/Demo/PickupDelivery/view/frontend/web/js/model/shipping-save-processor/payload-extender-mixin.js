define([
    'Demo_PickupDelivery/js/model/pickup-point'
], function (pickupPoint) {
    'use strict';

    return function (originalPayloadExtender) {
        return function (payload) {
            payload = originalPayloadExtender(payload);

            if (pickupPoint.selectedPointId()) {
                payload.addressInformation.extension_attributes.pickup_point_id =
                    Number(pickupPoint.selectedPointId());
            }

            return payload;
        };
    };
});
