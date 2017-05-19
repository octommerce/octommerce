<?php

/**
 * Octommerce.octommerce
 **/
Event::listen('cart.beforeAddItem', 'Octommerce\Octommerce\Listeners\TriggerProductType@beforeAddToCart');
Event::listen('cart.afterAddItem', 'Octommerce\Octommerce\Listeners\TriggerProductType@afterAddToCart');

/**
 * Resonsiv.pay
 **/
Event::listen('responsiv.pay.invoicePaid', 'Octommerce\Octommerce\Listeners\TriggerProductType@invoicePaid');
