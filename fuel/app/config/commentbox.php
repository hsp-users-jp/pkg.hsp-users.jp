<?php

return array(
	'guest' => false,
	'use_fullname' => false,
	'avatar' => array(
		'service' => 'gravatar',
		'gravatar' => array(
			'd' => 'identicon',
		),
	),

	'active' => 'default',
	'user_page' => 'user/{user_name}',
	'default' => array(
		'commentbox' => <<<EOD
{form}
{comments}
EOD
, // Limitation of heredoc

		'form' => <<<EOD
{open}
<div class="form-group">{body_field}</div>
<div id="commentbox_recaptcha_{comment_key}"></div>
<div class="form-inline">
	<div class="form-group" style="height: 34px;">{name_field}</div>
	<div class="form-group pull-right">{submit}</div>
</div>
{close}
EOD
, // Limitation of heredoc

		'comments' =>  <<<EOD
<hr style="margin-top:10px; margin-bottom:10px;">
<div class="media" style="width: 100%;">
	<div class="media-left">
		<span class="media-object">{avatar_userpage}</span>
	</div>
	<div class="media-body" style="width: 100%;">
		<h4 class="media-heading">{name_userpage} &nbsp;<small>{time}</small></h4>
		{body}</br>
		{reply_button}
<div class="panel panel-default hidden" style="margin-top: 10px">
	<div class="panel-body">
		{reply_form}
	</div>
</div>
{child}
	</div>
</div>
EOD
, // Limitation of heredoc

	),
);
