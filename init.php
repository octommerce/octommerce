<?php

/**
 * Octommerce.octommerce
 **/
Event::listen('cart.beforeAddItem', 'Octommerce\Octommerce\Listeners\TriggerProductType@beforeAddToCart');
Event::listen('cart.afterAddItem', 'Octommerce\Octommerce\Listeners\TriggerProductType@afterAddToCart');
Event::listen('octommerce.octommerce.productType.invoicePaidProcessed', 'Octommerce\Octommerce\Listeners\SendVoucherToCustomer');
Event::listen('octommerce.octommerce.productType.invoicePaidProcessed', 'Octommerce\Octommerce\Listeners\PaidToCompletedOnEvoucher');

/**
 * Resonsiv.pay
 **/
Event::listen('responsiv.pay.invoicePaid', 'Octommerce\Octommerce\Listeners\TriggerProductType@invoicePaid');
