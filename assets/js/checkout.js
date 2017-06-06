/*
 * Scripts for checkout page.
 */
+function ($) { "use strict";

	$(document).ready(function() {
		$('input[name=is_same_address]').change(function() {
			if ($(this).is(':checked')) {
				$('#shippingAddressForm').hide();
			} else {
				$('#shippingAddressForm').show();
			}
		})
	})

}(window.jQuery);