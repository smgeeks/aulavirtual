<?php

/**
 * Provide a admin-facing view for the plugin
 *
 * This file contains view for feedback form.
 *
 * @link       https://miniorange.com
 * @since      1.0.0
 *
 * @package    Miniorange_Oauth_20_Server
 * @subpackage Miniorange_Oauth_20_Server/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<body>
	<style>
		/* Feedback form css */
		.mo-modal-oauth-server {
			display: none;
			position: fixed;
			z-index: 1;
			padding-top: 50px;
			left: 100px;
			top: 0;
			margin-left: 13%;
			width: 70%;
			text-align: center;
		}

		.mo-modal-oauth-server-content {
			background-color: #fefefe;
			margin: auto;
			padding: 30px;
			border: 1px solid #5F6062;
			width: 55%;
		}

		.mo-oauth-server-feedback-block {
			box-shadow: 0 6px 10px rgba(0, 0, 0, .08), 0 0 6px rgba(0, 0, 0, .05);
			padding: 10px;
		}

		.mo_close {
			float: right;
			font-size: 21px;
			font-weight: bold;
			line-height: 1;
			color: #5F6062;
			text-shadow: 0 1px 0 #212121;
			opacity: 0.5;
			filter: alpha(opacity=50);
		}

		.mo_close:hover,
		.mo_close:focus {
			color: #000000;
			text-decoration: none;
			cursor: pointer;
			opacity: 0.8;
			filter: alpha(opacity=80);
		}

		#oauth_server_feedback_modal .mo_oauth_server_rating_div {
			font-size: 3rem;
			box-shadow: 2px 3px 5px 2px #b3c1e4;
			padding: 1rem;
		}

		#oauth_server_feedback_modal .mo-oauth-server-ratings label span {
			font-size: 1rem;
			top: 10px;
			position: relative;
		}

		#mo-oauth-server-reason-feedback form .radio {
			padding-left: 20%;
			text-align: left;
		}

		.mo_oauth_server_rating {
			display: none !important;
		}

		.mo-oauth-server-ratings {
			display: flex;
			justify-content: space-around;
		}

		.mo_oauth_server_rating_div:hover {
			transform: scale(1.2);
			transition: transform 0.3s ease-in-out;
			background-color: #b3c1e4;
		}


		.mo-modal-oauth-server h2 {
			font-size: 1.5rem;
		}

		.mo-modal-oauth-server h3 {
			padding-bottom: 10px;
		}

		.mo-modal-oauth-server hr {
			border-top: none;
			padding-top: 10px;
		}

		.is-mo-modal-oauth-server-blue {
			color: #374875;
		}

		.button.is-mo-modal-oauth-server-blue {
			border: none;
			color: #ffffff;
			background-color: #374875;
		}

		.button.is-mo-modal-oauth-server-blue.is-outlined {
			border: 1px solid #374875;
			color: #374875;
			background-color: #ffffff;
		}

		#mo-oauth-server-deactivate-reason {
			text-align: left;
			margin-left: 7rem;
		}

		@media screen and (max-width: 1520px) {

			.mo-modal-oauth-server {
				padding: 10px;
				top: 0px;
			}

			#oauth_server_feedback_modal .mo-oauth-server-ratings label {
				padding: 1rem;
			}
		}

		@media screen and (max-width: 1369px) {

			#oauth_server_feedback_modal .mo-oauth-server-ratings label {
				box-shadow: none;
			}
		}

		@media screen and (max-width: 1200px) {

			#oauth_server_feedback_modal .mo-oauth-server-ratings label {
				font-size: 2rem;
			}
		}
	</style>

	<div id="oauth_server_feedback_modal" class="mo-modal-oauth-server">
		<div class="mo-modal-oauth-server-content">
			<span class="mo_close" id="mo_oauth_server_close">&times;</span>
			<h2 class="is-mo-modal-oauth-server-blue">Feedback Form</h2>
			<div class="mo-oauth-server-feedback-block">
				<form name="f" method="post" action="" id="mo_oauth_server_feedback">
					<?php wp_nonce_field('mo_oauth_server_feedback_form', 'mo_oauth_server_feedback_form_field'); ?>
					<input type="hidden" name="mo_oauth_server_feedback" value="true" />
					<div id="mo-oauth-server-product-services-feedback">
						<h3>How satisfied are you with our product/services?</h3>
						<div class="mo-oauth-server-ratings">
							<input type="hidden" value="" name="rating">
							<div class="mo_oauth_server_rating_div">
								<input class="mo_oauth_server_rating" type="radio" value="1 - Terrible" id="mo_oauth_server_terrible">
								<label for="mo_oauth_server_terrible">😠
									<br><span>Terrible</span>
								</label>
							</div>
							<div class="mo_oauth_server_rating_div">
								<input class="mo_oauth_server_rating" type="radio" value="2 - Bad" id="mo_oauth_server_bad">
								<label for="mo_oauth_server_bad">🙁
									<br><span>Bad</span>
								</label>
							</div>
							<div class="mo_oauth_server_rating_div">

								<input class="mo_oauth_server_rating" type="radio" value="3 - Okay" id="mo_oauth_server_okay">
								<label for="mo_oauth_server_okay">😐
									<br><span>Okay</span>
								</label>
							</div>
							<div class="mo_oauth_server_rating_div">

								<input class="mo_oauth_server_rating" type="radio" value="4 - Good" id="mo_oauth_server_good">
								<label for="mo_oauth_server_good">😊
									<br><span>Good</span>
								</label>
							</div>
							<div class="mo_oauth_server_rating_div">

								<input class="mo_oauth_server_rating" type="radio" value="5 - Awesome" id="mo_oauth_server_awesome">
								<label for="mo_oauth_server_awesome">😁
									<br><span>Awesome</span>
								</label>
							</div>
						</div>
					</div>
					<hr />
					<div id="mo-oauth-server-reason-feedback">
						<h3>Tell us what happened?</h3>
						<div id="mo-oauth-server-deactivate-reason">
							<?php
							foreach ($deactivate_reasons as $deactivate_reason) {
							?>
								<div class="radio">
									<label for="<?php echo esc_attr($deactivate_reason); ?>">
										<input type="radio" name="deactivate_reason_radio" value="<?php echo esc_attr($deactivate_reason); ?>" required id="<?php echo esc_attr($deactivate_reason); ?>">
										<?php echo esc_attr($deactivate_reason); ?></label>
								</div>
							<?php } ?>
						</div>
						<br>
						<textarea id="query_feedback" name="query_feedback" rows="4" cols="50" placeholder="Write your query here"></textarea>
						<br><br>
						<div class="mo-modal-oauth-server-footer">
							<input type="submit" name="miniorange_feedback_submit" class="button button-primary button-large is-mo-modal-oauth-server-blue" value="Submit" />
							<input id="mo_skip_oauth_server" type="submit" name="miniorange_feedback_skip" class="button button-primary button-large is-mo-modal-oauth-server-blue is-outlined" value="Skip" />
						</div>
					</div>
				</form>
			</div>
		</div>
		<form name="f" method="post" action="" id="mo_oauth_server_feedback_form_close">
			<?php wp_nonce_field('mo_oauth_server_skip_feedback_form', 'mo_oauth_server_skip_feedback_form_field'); ?>
			<input type="hidden" name="option" value="mo_oauth_server_skip_feedback" />
		</form>
	</div>
	</div>

	<script>
		// feedback form.
		document.addEventListener("DOMContentLoaded", function() {
			const ratingLabels = document.querySelectorAll('.mo-oauth-server-ratings label');
			const ratingInputs = document.querySelectorAll('.mo_oauth_server_rating');
			const hiddenInput = document.querySelector('.mo-oauth-server-ratings input[type="hidden"]');

			ratingLabels.forEach((label, index) => {
				label.addEventListener('click', () => {
					ratingInputs[index].checked = true;
					hiddenInput.value = ratingInputs[index].value;
					if (ratingInputs[index].checked) {
						var ratingDivs = document.querySelectorAll('.mo_oauth_server_rating_div');
						ratingDivs.forEach(changeBackgroundColor);

						function changeBackgroundColor(item, index, div) {
							div[index].style.backgroundColor = '#fff';
						}
						ratingInputs[index].parentElement.style.backgroundColor = '#b3c1e4';
					}
				});
			});


		});

		jQuery('#deactivate-miniorange-oauth-20-server').click(function() {
			var mo_oauth_server_modal = document.getElementById('oauth_server_feedback_modal');
			var mo_skip_oauth_server = document.getElementById('mo_skip_oauth_server');
			var mo_oauth_server_close = document.getElementById("mo_oauth_server_close");
			mo_oauth_server_modal.style.display = "block";
			jQuery('input:radio[name="deactivate_reason_radio"]').click(function() {
				var reason = jQuery(this).val();
				var query_feedback = jQuery('#query_feedback');
				query_feedback.removeAttr('required')

				if (reason === "Does not have the features I'm looking for") {
					query_feedback.attr("placeholder", "Let us know what feature are you looking for");
				} else if (reason === "Other Reasons:") {
					query_feedback.attr("placeholder", "Can you let us know the reason for deactivation");
					query_feedback.prop('required', true);

				} else if (reason === "Bugs in the plugin") {
					query_feedback.attr("placeholder", "Can you please let us know about the bug in detail?");

				} else if (reason === "Confusing Interface") {
					query_feedback.attr("placeholder", "Finding it confusing? let us know so that we can improve the interface");
				} else if (reason === "Unable to register") {
					query_feedback.attr("placeholder", "Can you please write us about the error you are getting or send a screenshot? If you want we can create a account for you");
				}


			});

			mo_oauth_server_close.onclick = function() {
				mo_oauth_server_modal.style.display = "none";
				jQuery('#mo_oauth_server_feedback_form_close').submit();
			}

			mo_skip_oauth_server.onclick = function() {
				mo_oauth_server_modal.style.display = "none";
				jQuery('#mo_oauth_server_feedback_form_close').submit();
			}

			window.onclick = function(event) {
				if (event.target == mo_oauth_server_modal) {
					mo_oauth_server_modal.style.display = "none";
				}
			}
			return false;
		});
	</script>