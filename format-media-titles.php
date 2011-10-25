<?php
/*
Plugin Name: Format Media Titles
Plugin URI: http://www.presscoders.com/plugins/format-media-titles/
Description: Automatically formats the title for new media uploads. No need to manually edit the title anymore every time you upload an image!
Version: 0.1
Author: David Gwyer
Author URI: http://www.presscoders.com
*/

/*  Copyright 2009 David Gwyer (email : d.v.gwyer@presscoders.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Plugin Prefix: 'fmt_' prefix is derived from [f]ormat [m]edia [t]itles. */

/* Set-up Hooks. */
register_activation_hook(__FILE__, 'fmt_add_defaults');
register_uninstall_hook(__FILE__, 'fmt_delete_plugin_options');
add_action('admin_init', 'fmt_init' );
add_action('admin_menu', 'fmt_add_options_page');

/* Delete options table entries ONLY when plugin deactivated AND deleted. */
function fmt_delete_plugin_options() {
	delete_option('fmt_options');
}

/* Define default option settings. */
function fmt_add_defaults() {
	$tmp = get_option('fmt_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('fmt_options');
		$arr = array(	"chk_hyphen" => "1",
						"chk_underscore" => "1",
						"chk_default_options_db" => "",
						"rdo_cap_options" => "cap_all"
		);
		update_option('fmt_options', $arr);
	}
}

/* Init plugin options to white list our options. */
function fmt_init(){
	register_setting( 'fmt_plugin_options', 'fmt_options', 'fmt_validate_options' );
}

/* Add menu page. */
function fmt_add_options_page() {
	add_options_page('Format Media Titles Options Page', 'Format Media Titles', 'manage_options', __FILE__, 'fmt_render_form');
}

/* Render Plugin options form. */
function fmt_render_form() {
	?>
	<div class="wrap">
		
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Format Media Titles</h2>
		<p>Select the characters you want to be removed from the title (replaced with spaces), for newly uploaded media library items. Then, choose how you want the title to be captialized.</p>

		<p>Note: Capitalization works on individual words separated by spaces. If the title contains no spaces after character removal then only the first letter will be capitalized (as the title is effectively ONE word).</p>

		<form method="post" action="options.php">
			<?php settings_fields('fmt_plugin_options'); ?>
			<?php $options = get_option('fmt_options'); ?>

			<table class="form-table">

				<tr valign="top">
					<th scope="row">Remove Characters</th>
					<td>
						<label><input name="fmt_options[chk_hyphen]" type="checkbox" value="1" <?php if (isset($options['chk_hyphen'])) { checked('1', $options['chk_hyphen']); } ?> /> Hyphen (-)</label><br />

						<label><input name="fmt_options[chk_underscore]" type="checkbox" value="1" <?php if (isset($options['chk_underscore'])) { checked('1', $options['chk_underscore']); } ?> /> Underscore (_)</label><br />

						<label><input name="fmt_options[chk_period]" type="checkbox" value="1" <?php if (isset($options['chk_period'])) { checked('1', $options['chk_period']); } ?> /> Period (.)</label><br />
 
						<label><input name="fmt_options[chk_tilde]" type="checkbox" value="1" <?php if (isset($options['chk_tilde'])) { checked('1', $options['chk_tilde']); } ?> /> Tilde (~)</label><br />

						<label><input name="fmt_options[chk_plus]" type="checkbox" value="1" <?php if (isset($options['chk_plus'])) { checked('1', $options['chk_plus']); } ?> /> Plus (+)</label>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Capitalization Method</th>
					<td>
						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="cap_all" <?php checked('cap_all', $options['rdo_cap_options']); ?> /> Capitalize All Words</label><br />

						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="cap_first" <?php checked('cap_first', $options['rdo_cap_options']); ?> /> Capitalize First Word Only</label><br />

						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="all_lower" <?php checked('all_lower', $options['rdo_cap_options']); ?> /> All Words Lower Case</label><br />

						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="all_upper" <?php checked('all_upper', $options['rdo_cap_options']); ?> /> All Words Upper Case</label><br />

						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="dont_alter" <?php checked('dont_alter', $options['rdo_cap_options']); ?> /> Don't Alter (title text isn't modified in any way).</label>
					</td>
				</tr>

				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="fmt_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

		<p style="margin-top:15px;">
			<p style="font-style: italic;color: #26779a;">If you have found this Plugin at all useful, please consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NF3DUA5PAYWQ8" target="_blank" style="color:#72a1c6;">donation</a>. Thanks.</p>
			<span><a href="http://www.facebook.com/PressCoders" title="Our Facebook page" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/format-media-titles/images/facebook-icon.png" /></a></span>
			&nbsp;&nbsp;<span><a href="http://www.twitter.com/dgwyer" title="Follow on Twitter" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/format-media-titles/images/twitter-icon.png" /></a></span>
			&nbsp;&nbsp;<span><a href="http://www.presscoders.com" title="PressCoders.com" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/format-media-titles/images/pc-icon.png" /></a></span>
		</p>

	</div>
	<?php	
}

/* Sanitize and validate input. Accepts an array, return a sanitized array. */
function fmt_validate_options($input) {
	 /* Strip html from textboxes. */
	$input['textarea_one'] =  wp_filter_nohtml_kses($input['textarea_one']); // Sanitize textarea input (strip html tags, and escape characters)
	$input['txt_one'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

add_filter( 'plugin_action_links', 'fmt_plugin_action_links', 10, 2 );
/* Display a Settings link on the main Plugins page. */
function fmt_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$fmt_links = '<a href="'.get_admin_url().'options-general.php?page=format-media-titles/format-media-titles.php">'.__('Settings').'</a>';
		/* Make the 'Settings' link appear first. */
		array_unshift( $links, $fmt_links );
	}

	return $links;
}

function fmt_update_media_title($id) {

	$options = get_option('fmt_options');
	$cap_options = $options['rdo_cap_options'];

	$uploaded_post_id = get_post( $id );
	$title = $uploaded_post_id->post_title;

	/* Update post. */
	$char_array = array();
	if( isset($options['chk_hyphen']) && $options['chk_hyphen'] ) $char_array[] = '-';
	if( isset($options['chk_underscore']) && $options['chk_underscore'] ) $char_array[] = '_';
	if( isset($options['chk_period']) && $options['chk_period'] ) $char_array[] = '.';
	if( isset($options['chk_tilde']) && $options['chk_tilde'] ) $char_array[] = '~';
	if( isset($options['chk_plus']) && $options['chk_plus'] ) $char_array[] = '+';

	/* Replace chars with spaces, if any selected. */
	if( !empty($char_array) )
		$title = str_replace($char_array, ' ', $title );

	/* Trim multiple spaces between words. */
	$title = preg_replace("/\s+/", " ", $title);

	/* Capitalize Title. */
	switch ($cap_options) {
    case 'cap_all':
		$title = ucwords( $title );
        break;
    case 'cap_first':
		$title = ucfirst( strtolower( $title ) );
        break;
    case 'all_lower':
		$title = strtolower( $title );
        break;
    case 'all_upper':
		$title = strtoupper( $title );
        break;
    case 'dont_alter':
		/* Leave title as it is. */
        break;
	}

	// Update the post into the database
	$uploaded_post = array();
	$uploaded_post['ID'] = $id;
	$uploaded_post['post_title'] = $title;
	wp_update_post( $uploaded_post );
}
add_action( 'add_attachment', 'fmt_update_media_title' );