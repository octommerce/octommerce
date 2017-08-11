<?php

/**
 * Octommerce.octommerce
 **/
Event::listen('cart.beforeAddItem', 'Octommerce\Octommerce\Listeners\TriggerProductType@beforeAddToCart');
Event::listen('cart.afterAddItem', 'Octommerce\Octommerce\Listeners\TriggerProductType@afterAddToCart');
Event::listen('order.afterUpdateStatus', 'Octommerce\Octommerce\Listeners\UpdateInvoiceStatus@filterStatus');

/**
 * Resonsiv.pay
 **/
Event::listen('responsiv.pay.invoicePaid', 'Octommerce\Octommerce\Listeners\TriggerProductType@invoicePaid');
Event::listen('responsiv.pay.beforeUpdateInvoiceStatus', 'Octommerce\Octommerce\Listeners\UpdateOrderStatus@filterStatus');
