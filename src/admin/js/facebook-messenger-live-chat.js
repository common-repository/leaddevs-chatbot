(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(function () {

		/* ===============================================
			||										    ||
			||		 Messenger bot settings option      ||
			||										    ||
		    ===============================================
		*/
        $(".wpfbmb-fb-messenger-bot-manage-settings").on("click", function(e){
            e.preventDefault();

            var formData = $('#wpfbmb-fb_messenger_bot-manage-form').serialize();

            $(this).html('Processing...');
            $.post(wpfbmb_ajax_obj.wpfbmb_ajax_url, {     //POST request
                _ajax_nonce: wpfbmb_ajax_obj.nonce, //nonce
                action: "wpfbmb_facebook_messenger_bot_manage_setting",        //action
                formData: formData,
                //form requested data
            }, function (response) {                //callback
				//console.log(response);
                $('.wpfbmb-fb-messenger-bot-manage-settings').html('Save');
				if(response.data.status === 'success'){
                    $('.wpfbmb-save-success-alert-message').fadeIn( 200 ).delay( 1000 ).fadeOut( 400 );
                }else{
                    $('.wpfbmb-save-error-alert-message').fadeIn( 200 ).delay( 1000 ).fadeOut( 400 );

                }

            });
        });

		// switch button action
        $('#wpfbmb_switch_input_id').change(function(){
            var switchValue = $(this);
            switchValue.val(switchValue.prop('checked'));
        });


		// Tooltip setup only for Text
		$('.wpfbmbMasterTooltip').hover(function () {
			// Hover over code
			var title = $(this).attr('wpfbmbTitle');
			$(this).data('tipText', title).removeAttr('wpfbmbTitle');
			$('<p class="wpfbmbTooltip"></p>')
				.text(title)
				.appendTo('body')
				.fadeIn('slow');
		}, function () {
			// Hover out code
			$(this).attr('wpfbmbTitle', $(this).data('tipText'));
			$('.wpfbmbTooltip').remove();
		}).mousemove(function (e) {
			var mousex = e.pageX + 20; //Get X coordinates
			var mousey = e.pageY + 10; //Get Y coordinates
			$('.wpfbmbTooltip')
				.css({top: mousey, left: mousex})
		});

	});

})( jQuery );
