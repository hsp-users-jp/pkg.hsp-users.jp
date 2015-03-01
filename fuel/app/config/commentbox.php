<?php

return array(
	'guest' => false,
	'avatar' => array(
		'service' => 'gravatar',
		'gravatar' => array(
			'd' => 'identicon',
		),
	),

	'active' => 'default',

	'default' => array(

		// form wrap template
		//   availabled tags
		//     {form}   : form area
		//     {errors} : submit error area
		//     {recaptcha_script} : reCAPTCHA init script
		'form_wrap' => <<<EOD
{form}
{errors}
EOD
, // Limitation of heredoc

		// comment form template
		//   availabled tags
		//     {open}          : form open
		//     {close}         : form close
		//     {body_field}    : comment body input field
		//     {name_field}    : username input field (optional)
		//     {email_field}   : email input field (optional)
		//     {website_field} : website input field (optional)
		//     {submit}        : submit button
		//     {comment_key}   : comment key value
		'form' => <<<EOD
{open}
<div class="form-group">{body_field}</div>
<div id="commentbox_recaptcha_{comment_key}"></div>
<div class="form-inline">
	<div class="form-group">{name_field}</div>
	<div class="form-group">{email_field}</div>
	<div class="form-group pull-right">{submit}</div>
</div>
{close}
EOD
, // Limitation of heredoc

		// form errors wrap template
		//   availabled tags
		//     {errors} : submit errors
		'form_errors_wrap' => <<<EOD
<div class="alert alert-danger" role="alert">
  <ul class="list-unstyled">
{errors}
  </ul>
</div>
EOD
, // Limitation of heredoc

		// form error item template
		//   availabled tags
		//     {error} : submit error
		'form_error_item' => <<<EOD
<li>{error}</li>
EOD
, // Limitation of heredoc

		// comment tree wrap template
		//   availabled tags
		//     {comments} : comment tree area
		'comments_wrap' => <<<EOD
{comments}
EOD
, // Limitation of heredoc

		// comment tree template
		//   availabled tags
		//     {avatar} : user avatar area
		//     {name}   : username input area
		//     {email}  : email input area
		//     {time}   : post time
		//     {body}   : comment body input area
		//     {reply_button} : reply button, see 'reply_button' template
		//     {reply_form}   : reply form area, see 'form' template
		'comments' =>  <<<EOD
<hr>
<div class="media">
	<div class="media-left">
		<span class="media-object">{avatar}</span>
	</div>
	<div class="media-body" style="width: 100%">
		<h4 class="media-heading">{name} <small>{time}</small></h4>
		{body}</br>
		{reply_button}
<div class="panel panel-default hidden">
	<div class="panel-body">
		{reply_form}
	</div>
</div>
{child}
	</div>
</div>
EOD
, // Limitation of heredoc

		// comment reply button template
		//   availabled tags
		//     {comment_key} : comment key value
		'comment_reply_button' => <<<EOD
<a href="#" id="commentbox_reply_button_{comment_key}" onclick="$(this).next().toggleClass('hidden');return false;">返信</a>
EOD
, // Limitation of heredoc

		// reCAPTCHA script code
		//   availabled tags
		//     {recaptcha_site_key} : reCAPTCHA site key value
		'recaptcha_script' => <<<EOD
<script type="text/javascript">
	var cbRecaptchaRender = function(id) {
		grecaptcha.render(id, {
			'sitekey': '{recaptcha_site_key}',
			'callback': function(){
				$('[id="'+id.replace('commentbox_recaptcha_', 'commentbox_submit_')+'"]')
					.removeAttr('disabled');
			}
		});
	};
	var cbRecaptchaOnload = function() {
		$('[id^="commentbox_recaptcha_"]:first')
			.each(function(){
				cbRecaptchaRender($(this).attr('id'));
			});
		$('[id^="commentbox_reply_button_"]')
			.on('click', function(){
				var id = $(this).attr('id').replace('commentbox_reply_button_', 'commentbox_recaptcha_');
				$('[id="'+id+'"]:empty')
					.each(function(){
						cbRecaptchaRender(id);
					});
			});
	};
</script>
<script src="//www.google.com/recaptcha/api.js?onload=cbRecaptchaOnload&render=explicit" async defer></script>
EOD
, // Limitation of heredoc
	),
);
