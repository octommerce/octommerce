<?php

Event::listen('cart.beforeAddItem', 'Octommerce\Octommerce\Listeners\TriggerProductType@beforeAddToCart');
Event::listen('cart.afterAddItem', 'Octommerce\Octommerce\Listeners\TriggerProductType@afterAddToCart');
