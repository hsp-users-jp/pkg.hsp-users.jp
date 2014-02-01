<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */


return array(
	// regular form definitions
	'prep_value'                 => true,
	'auto_id'                    => true,
	'auto_id_prefix'             => 'form_',
	'form_method'                => 'post',
	'form_template'              => <<<EOD
		{open}
		{fields}
		{close}
EOD
,
	'fieldset_template'          => <<<EOD
		<tr><td colspan=\"2\">{open}<table>{fields}</table></td></tr>
		{close}
EOD
,
	'field_template'             => <<<EOD
		<div class="form-group {error_class}">
			{label}
			<div class="col-sm-10">
				{field}
				<span>{description}</span> {error_msg}
			</div>
		</div>
EOD
,
	'multi_field_template'       => <<<EOD
		<tr>
			<td class=\"{error_class}\">{group_label}{required}</td>
			<td class=\"{error_class}\">{fields}
				{field} {label}<br />
{fields}<span>{description}</span>			{error_msg}
			</td>
		</tr>
EOD
,
	'error_template'             => '<span>{error_msg}</span>',
	'group_label'	             => '<span>{label}</span>',
	'required_mark'              => '<small><span class="fa fa-asterisk fa-fw text-danger"></span></small>',
	'inline_errors'              => true,
	'error_class'                => 'has-error',
	'label_class'                => 'col-sm-2 control-label',

	// tabular form definitions
	'tabular_form_template'      => "<table>{fields}</table>\n",
	'tabular_field_template'     => "{field}",
	'tabular_row_template'       => "<tr>{fields}</tr>\n",
	'tabular_row_field_template' => "\t\t\t<td>{label}{required}&nbsp;{field} {error_msg}</td>\n",
	'tabular_delete_label'       => "Delete?",
);
