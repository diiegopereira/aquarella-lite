<?php
	/**
	 * @package     Freemius
	 * @copyright   Copyright (c) 2015, Freemius, Inc.
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       1.2.0
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * @var Freemius $fs
	 */
	$fs   = freemius( $VARS['id'] );
	$slug = $fs->get_slug();

	$message_header  = sprintf(
		__fs( 'start-trial-prompt-header', $slug ),
		'<span class="var-trial_period"></span>',
		'<span class="var-plan_title"></span>'
	);
	$message_content = sprintf(
		__fs( 'start-trial-prompt-message', $slug ),
		$fs->get_module_type(),
		sprintf(
			'<a href="%s" target="_blank">%s</a>',
			'https://freemius.com',
			'freemius.com'
		)
	);

	$modal_content_html = <<< HTML
	<div class="notice notice-error inline"><p></p></div>
	<h3>{$message_header}</h3>
	<p>{$message_content}</p>
HTML;

	fs_enqueue_local_style( 'dialog-boxes', '/admin/dialog-boxes.css' );
?>
<script type="text/javascript">
	(function ($) {
		$(document).ready(function () {
			var modalContentHtml = <?php echo json_encode( $modal_content_html ); ?>,
			    modalHtml        =
				    '<div class="fs-modal fs-modal-license-key-resend">'
				    + '	<div class="fs-modal-dialog">'
				    + '		<div class="fs-modal-header">'
				    + '		    <h4><?php _efs( 'start-free-trial' ) ?></h4>'
				    + '		</div>'
				    + '		<div class="fs-modal-body">' + modalContentHtml + '</div>'
				    + '		<div class="fs-modal-footer">'
				    + '			<button class="button button-secondary button-close">' + <?php echo json_encode( __fs( 'cancel', $slug ) ) ?> +'</button>'
				    + '			<button class="button button-primary button-connect">' + <?php echo json_encode( __fs( 'approve-start-trial', $slug ) ) ?> +'</button>'
				    + '		</div>'
				    + '	</div>'
				    + '</div>',
			    $modal = $( modalHtml ),
			    trialData;

			$modal.appendTo($('body'));

			var $errorNotice = $modal.find('.notice-error');

			registerEventHandlers();

			function registerEventHandlers() {
				$modal.on('click', '.button-close', function () {
					closeModal();
					return false;
				});

				$modal.on('click', '.button-connect', function (evt) {
					evt.preventDefault();

					var $button = $(this);

					$.ajax({
						url       : ajaxurl,
						method    : 'POST',
						data      : {
							action   : '<?php echo $fs->get_action_tag( 'start_trial' ) ?>',
							security : '<?php echo wp_create_nonce( $fs->get_action_tag( 'start_trial' ) ) ?>',
							module_id: '<?php echo $fs->get_id() ?>',
							trial    : trialData
						},
						beforeSend: function () {
							// Disable all buttons during trial activation.
							$modal.find('.button').prop('disabled', true);

							$button.text('<?php _efs( 'starting-trial', $slug ) ?>...');

							setLoadingMode();
						},
						success   : function (resultObj) {
							if (resultObj.success) {
								$button.text('<?php _efs( 'please-wait', $slug ) ?>...');

								// Redirect to the "Account" page and sync the license.
								window.location.href = resultObj.data.next_page;
							} else {
								$button.text(<?php json_encode( __fs( 'approve-start-trial', $slug ) ) ?>);

								resetLoadingMode();
								showError(resultObj.error);
							}
						}
					});
				});
			}

			window.openTrialConfirmationModal = function showModal(data) {
				resetModal();

				// Display the dialog box.
				$modal.addClass('active');

				trialData = data;

				var $modalBody = $modal.find('.fs-modal-body'),
				    $var;

				for (var key in data) {
					if (data.hasOwnProperty(key)) {
						$var = $modalBody.find('.var-' + key);

						if ($var.length > 0)
							$var.html(data[key]);
					}
				}

				$('body').addClass('has-fs-modal');
			};

			function closeModal() {
				$modal.removeClass('active');

				$('body').removeClass('has-fs-modal');
			}

			function resetModal() {
				hideError();
			}

			function hideError() {
				$errorNotice.hide();
			}

			function setLoadingMode() {
				$modal.find('.button')
				// Re-enable all buttons.
					.prop('disabled', trialData)
					// Stop loading cursor.
					.css({'cursor': 'wait'});

				// Stop loading cursor.
				$(document.body).css({'cursor': 'wait'});
			}

			function resetLoadingMode() {
				$modal.find('.button')
				// Re-enable all buttons.
					.prop('disabled', false)
					// Stop loading cursor.
					.css({'cursor': 'initial'});

				// Stop loading cursor.
				$(document.body).css({'cursor': 'initial'});
			}

			function showError(msg) {
				$errorNotice.find(' > p').html(msg);
				$errorNotice.show();
			}
		});
	})(jQuery);
</script>
