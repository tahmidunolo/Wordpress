<?php

	add_action( 'wp_enqueue_scripts', 'gdlrs_include_stripe_payment_script' );
	if( !function_exists('gdlrs_include_stripe_payment_script') ){
		function gdlrs_include_stripe_payment_script(){
			global $hostel_option;
			if( isset($_GET[$hostel_option['booking-slug']]) ){
				wp_enqueue_script('stripe', 'https://js.stripe.com/v3/');
			}
		}
	}
	
	if( !function_exists('gdlrs_get_stripe_form') ){
		function gdlrs_get_stripe_form($option){
			global $hostel_option, $wpdb;
			
			// get amount
			$temp_sql  = "SELECT * FROM " . $wpdb->prefix . "gdlr_hostel_payment ";
			$temp_sql .= "WHERE id = " . $option['invoice'];	
			$result = $wpdb->get_row($temp_sql);
			$price = intval(floatval($result->pay_amount) * 100);

			// set payment intent
			\Stripe\Stripe::setApiKey(trim($hostel_option['stripe-secret-key']));
			$intent = \Stripe\PaymentIntent::create([
			    'amount' => $price,
			    'currency' => trim($hostel_option['stripe-currency-code']),
			    'metadata' => array(
			    	'invoice' => $option['invoice']
			    )
			]);

			ob_start();
?>
<form action="" method="POST" class="gdlr-payment-form" id="payment-form" data-ajax="<?php echo AJAX_URL; ?>" data-invoice="<?php echo $option['invoice']; ?>" >
	<p class="gdlr-form-half-left">
		<label><span><?php _e('Card Holder Name', 'gdlr-hostel'); ?></span></label>
		<input id="cardholder-name" type="text">
	</p>
	<div class="clear" ></div>
	
	<div class="gdlr-form-half-left">
		<label><span><?php _e('Card Information', 'gdlr-hostel'); ?></span></label>
		<div id="card-element"></div>
	</div>
	<div class="clear" ></div>
	<div class="gdlr-form-error payment-errors" style="display: none;"></div>
	<div class="gdlr-form-loading gdlr-form-instant-payment-loading"><?php _e('loading', 'gdlr-hostel'); ?></div>
	<div class="gdlr-form-notice gdlr-form-instant-payment-notice"></div>
	<input id="card-button" type="submit" class="gdlr-form-button cyan" data-secret="<?= $intent->client_secret ?>" value="<?php _e('Submit Payment', 'gdlr-hostel'); ?>" >
</form>
<script type="text/javascript">
jQuery(function($){
	var form = $('#payment-form');

	var stripe = Stripe('<?php echo esc_js(trim($hostel_option['stripe-publishable-key'])); ?>');
	var elements = stripe.elements();
	var cardElement = elements.create('card');
	cardElement.mount('#card-element');

	var cardholderName = document.getElementById('cardholder-name');
	var cardButton = document.getElementById('card-button');
	var clientSecret = cardButton.dataset.secret;
	cardButton.addEventListener('click', function(ev){
		
		// validate empty input field
		if( !form.find('#cardholder-name').val() ){
			var req = true;
		}else{
			var req = false;
		}

		// make the payment
		if( req ){
			form.find('.payment-errors').text('<?php _e('Please fill the card holder name', 'gdlr-hostel'); ?>').slideDown();
		}else{

			// Disable the submit button to prevent repeated clicks
			form.find('input[type="submit"]').prop('disabled', true);
			form.find('.payment-errors, .gdlr-form-notice').slideUp();
			form.find('.gdlr-form-loading').slideDown();
			
			// make the payment
			stripe.handleCardPayment(
				clientSecret, cardElement, {
					payment_method_data: {
						billing_details: {name: cardholderName.value}
					}
				}
			).then(function(result){
				if( result.error ){

					form.find('.payment-errors').text(result.error.message).slideDown();
					form.find('.gdlr-form-loading').slideUp();
					$(cardButton).prop('disabled', false);
	
				}else{

					$.ajax({
						type: 'POST',
						url: form.attr('data-ajax'),
						data: {'action':'gdlr_hostel_stripe_payment', 'paymentIntent': result.paymentIntent.id, 'invoice': form.attr('data-invoice')},
						dataType: 'json',
						error: function(a, b, c){ 
							console.log(a, b, c); 
							form.find('.gdlr-form-loading').slideUp(); 
							$(cardButton).prop('disabled', false);
						},
						success: function(data){
							if( data.content ){
								$('#gdlr-booking-content-inner').fadeOut(function(){
									$(this).html(data.content).fadeIn();
								});
								$('#gdlr-booking-process-bar').children('[data-process=4]').addClass('gdlr-active').siblings().removeClass('gdlr-active');
							}else{
								form.find('.gdlr-form-loading').slideUp();
								form.find('.gdlr-form-notice').removeClass('success failed')
									.addClass(data.status).html(data.message).slideDown();
								
								if( data.status == 'failed' ){
									form.find('input[type="submit"]').prop('disabled', false);
								}
							}
						}
					});	

				}
			});

		}
	});

	$(cardButton).on('click', function(){
		return false;
	});

});
</script>
<?php	
			$stripe_form = ob_get_contents();
			ob_end_clean();
			return $stripe_form;
		}
	}
	
	add_action( 'wp_ajax_gdlr_hostel_stripe_payment', 'gdlr_hostel_stripe_payment' );
	add_action( 'wp_ajax_nopriv_gdlr_hostel_stripe_payment', 'gdlr_hostel_stripe_payment' );
	if( !function_exists('gdlr_hostel_stripe_payment') ){
		function gdlr_hostel_stripe_payment(){
			global $hostel_option, $wpdb;
		
			$ret = array();
			if( !empty($_POST['paymentIntent']) && !empty($_POST['invoice']) ){

				\Stripe\Stripe::setApiKey(trim($hostel_option['stripe-secret-key']));
				$pi = \Stripe\PaymentIntent::retrieve(trim($_POST['paymentIntent']));

				if( $pi['status'] == 'succeeded' && $pi['metadata']->invoice == trim($_POST['invoice']) ){

					$temp_sql  = "SELECT * FROM " . $wpdb->prefix . "gdlr_hostel_payment ";
					$temp_sql .= "WHERE id = " . $_POST['invoice'];	
					$result = $wpdb->get_row($temp_sql);
					$contact_info = unserialize($result->contact_info);
				
					// update database
					$charge = array(
						'balance_transaction' => $_POST['paymentIntent']
					);
					$wpdb->update( $wpdb->prefix . 'gdlr_hostel_payment', 
						array('payment_status'=>'paid', 'payment_info'=>serialize($charge), 'payment_date'=>date('Y-m-d H:i:s')), 
						array('id'=>$_POST['invoice']), 
						array('%s', '%s', '%s'), 
						array('%d')
					);				

					do_action('gdlrs_update_transaction_availability', $_POST['invoice']);
					
					// send the mail
					$data = unserialize($result->booking_data);
					$mail_content = gdlr_hostel_mail_content($contact_info, $data, $charge, array(
						'total_price'=>$result->total_price, 'pay_amount'=>$result->pay_amount, 'booking_code'=>$result->customer_code)
					);
					gdlr_hostel_mail($contact_info['email'], __('Thank you for booking the room with us.', 'gdlr-hostel'), $mail_content);
					gdlr_hostel_mail($hostel_option['recipient-mail'], __('New room booking received', 'gdlr-hostel'), $mail_content);

					$ret['status'] = 'success';
					$ret['message'] = __('Payment complete.', 'gdlr-hostel');
					$ret['content'] = gdlrs_booking_complete_message();
				}else{
					$ret['status'] = 'failed';
					$ret['message'] = __('Failed to proceed, please try again.', 'gdlr-hostel');
				}
			}else{
				$ret['status'] = 'failed';
				$ret['message'] = __('Failed to proceed, please try again.', 'gdlr-hostel');	
			}
			
			die(json_encode($ret));
		}
	}
	
?>