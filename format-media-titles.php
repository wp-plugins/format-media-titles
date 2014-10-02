<?php
/*
Plugin Name: Format Media Titles
Plugin URI: http://www.wpgothemes.com/plugins/format-media-titles/
Description: Automatically formats the title for new media uploads. No need to manually edit the title anymore every time you upload an image!
Version: 0.25
Author: David Gwyer
Author URI: http://www.wpgothemes.com
*/

/*  Copyright 2009 David Gwyer (email : david@wpgothemes.com)

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

// @todo
//
// 1. Be able to add a prefix or suffix to the title or filename etc.
// 2. See the Plugin support posts for more ideas.
// 3. Enable batch renaming of multiple images, all at once?

/* Set-up Hooks. */
register_activation_hook( __FILE__, 'fmt_add_defaults' );
register_uninstall_hook( __FILE__, 'fmt_delete_plugin_options' );
add_action( 'admin_menu', 'fmt_add_options_page' );
add_action('admin_init', 'fmt_init' );
add_action( 'add_attachment', 'fmt_update_media_title' );
add_filter( 'plugin_action_links', 'fmt_plugin_action_links', 10, 2 );
add_action( 'plugins_loaded', 'fmt_localize_plugin' );

function fmt_localize_plugin() {
	load_plugin_textdomain( 'format-media-titles', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

/* Delete options table entries ONLY when plugin deactivated AND deleted. */
function fmt_delete_plugin_options() {
	delete_option( 'fmt_options' );
}

/* Define default option settings. */
function fmt_add_defaults() {
	$tmp = get_option( 'fmt_options' );
	if ( ( $tmp['chk_default_options_db'] == '1' ) || ( ! is_array( $tmp ) ) ) {
		delete_option( 'fmt_options' );
		$arr = array( "chk_hyphen"             => "1",
					  "chk_underscore"         => "1",
					  "rdo_cap_options"        => "cap_all"
		);
		update_option( 'fmt_options', $arr );
	}
}

/* Init plugin options to white list our options. */
function fmt_init(){
    register_setting( 'fmt_plugin_options', 'fmt_options' );
}

/* Add menu page. */
function fmt_add_options_page() {
	add_options_page( 'Format Media Titles Options Page', 'Format Media Titles', 'manage_options', __FILE__, 'fmt_render_form' );
}

/* Render Plugin options form. */
function fmt_render_form() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Format Media Titles', 'format-media-titles' ); ?></h2>

		<p><?php _e( 'Select the characters you want to be removed from the media title (and replaced with spaces) for newly uploaded media. Then, choose how you want the title to be capitalized.', 'format-media-titles' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'fmt_plugin_options' ); ?>
			<?php $options = get_option( 'fmt_options' ); ?>

			<table class="form-table">

				<tr valign="top">
					<th scope="row"><?php _e( 'Remove Characters', 'format-media-titles' ); ?></th>
					<td>
						<label><input name="fmt_options[chk_hyphen]" type="checkbox" value="1" <?php if ( isset( $options['chk_hyphen'] ) ) {
								checked( '1', $options['chk_hyphen'] );
							} ?> /> <?php _e( 'Hyphen', 'format-media-titles' ); ?> (-)</label><br />

						<label><input name="fmt_options[chk_underscore]" type="checkbox" value="1" <?php if ( isset( $options['chk_underscore'] ) ) {
								checked( '1', $options['chk_underscore'] );
							} ?> /> <?php _e( 'Underscore', 'format-media-titles' ); ?> (_)</label><br />

						<label><input name="fmt_options[chk_period]" type="checkbox" value="1" <?php if ( isset( $options['chk_period'] ) ) {
								checked( '1', $options['chk_period'] );
							} ?> /> <?php _e( 'Period', 'format-media-titles' ); ?> (.)</label><br />

						<label><input name="fmt_options[chk_tilde]" type="checkbox" value="1" <?php if ( isset( $options['chk_tilde'] ) ) {
								checked( '1', $options['chk_tilde'] );
							} ?> /> <?php _e( 'Tilde', 'format-media-titles' ); ?> (~)</label><br />

						<label><input name="fmt_options[chk_plus]" type="checkbox" value="1" <?php if ( isset( $options['chk_plus'] ) ) {
								checked( '1', $options['chk_plus'] );
							} ?> /> <?php _e( 'Plus', 'format-media-titles' ); ?> (+)</label>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Capitalization Method', 'format-media-titles' ); ?></th>
					<td>
						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="cap_all" <?php checked( 'cap_all', $options['rdo_cap_options'] ); ?> /> <?php _e( 'Capitalize All Words', 'format-media-titles' ); ?></label><br />

						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="cap_first" <?php checked( 'cap_first', $options['rdo_cap_options'] ); ?> /> <?php _e( 'Capitalize First Word Only', 'format-media-titles' ); ?></label><br />

						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="all_lower" <?php checked( 'all_lower', $options['rdo_cap_options'] ); ?> /> <?php _e( 'All Words Lower Case', 'format-media-titles' ); ?></label><br />

						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="all_upper" <?php checked( 'all_upper', $options['rdo_cap_options'] ); ?> /> <?php _e( 'All Words Upper Case', 'format-media-titles' ); ?></label><br />

						<label><input name="fmt_options[rdo_cap_options]" type="radio" value="dont_alter" <?php checked( 'dont_alter', $options['rdo_cap_options'] ); ?> /> <?php _e( 'Don\'t Alter (title text isn\'t modified in any way)', 'format-media-titles' ); ?></label>

						<p class="description"><?php _e( 'Capitalization works on individual words separated by spaces. If the title contains NO spaces after formatting then only the first letter will be capitalized.', 'format-media-titles' ); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Misc. Options', 'format-media-titles' ); ?></th>
					<td>
						<label><input name="fmt_options[chk_alt]" type="checkbox" value="1" <?php if ( isset( $options['chk_alt'] ) ) {
								checked( '1', $options['chk_alt'] );
							} ?> /> <?php _e( 'Add Title to Alternative Text Field?', 'format-media-titles' ); ?></label>
						<p class="description"><?php _e( 'When checked this option copies the formatted media title to the \'Alternative Text\' field.', 'format-media-titles' ); ?></p>
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<div></div>
					</td>
				</tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row"><?php _e( 'Database Options', 'format-media-titles' ); ?></th>
					<td>
						<label><input name="fmt_options[chk_default_options_db]" type="checkbox" value="1" <?php if ( isset( $options['chk_default_options_db'] ) ) {
								checked( '1', $options['chk_default_options_db'] );
							} ?> /> <?php _e( 'Restore defaults upon plugin deactivation/reactivation', 'format-media-titles' ); ?></label>
						<p class="description"><?php _e( 'Only check this if you want to reset plugin settings upon Plugin reactivation', 'format-media-titles' ); ?></p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'format-media-titles' ); ?>" />
			</p>
		</form>

		<div style="clear:both;">
			<p>
				<a href="http://www.twitter.com/dgwyer" title="<?php _e( 'Come and join me on Twitter!', 'format-media-titles' ); ?> :-)" target="_blank"><img src="<?php echo plugins_url(); ?>/format-media-titles/images/twitter.png" /></a>&nbsp;&nbsp;
				<input class="button" style="vertical-align:12px;" type="button" value="<?php _e( 'Visit Our NEW Site!', 'format-media-titles' ); ?>" onClick="window.open('http://www.wpgothemes.com')">
				<input class="button" style="vertical-align:12px;" type="button" value="<?php _e( 'Minn, Our Latest Theme', 'format-media-titles' ); ?>" onClick="window.open('http://www.wpgothemes.com/themes/minn')">
			</p>
		</div>

		<?php
		$discount_date = "14th August 2014";
		if( strtotime($discount_date) > strtotime('now') ) {
			echo '<p style="background: #fff;border: 1px dashed #ccc;font-size: 13px;margin: 0 0 10px 0;padding: 5px 0 5px 8px;">For a limited time only! <strong>Get 50% OFF</strong> the price of our brand new mobile ready, fully responsive <a href="http://www.wpgothemes.com/themes/minn/" target="_blank"><strong>Minn WordPress theme</strong></a>. Simply enter the following code at checkout: <code>MINN50OFF</code></p>';
		} else {
			echo '<p style="background: #fff;border: 1px dashed #ccc;font-size: 13px;margin: 0 0 10px 0;padding: 5px 0 5px 8px;">As a user of our free plugins here\'s a bonus just for you! <a href="http://www.wpgothemes.com/themes/minn/" target="_blank"><strong>Get 30% OFF</strong></a> the price of our brand new mobile ready, fully responsive <a href="http://www.wpgothemes.com/themes/minn/" target="_blank"><strong>Minn WordPress theme</strong></a>. Simply enter the following code at checkout: <code>WPGO30OFF</code></p>';
		}
		?>
	</div>
<?php
}

/* Display a Settings link on the main Plugins page. */
function fmt_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$fmt_links = '<a href="' . get_admin_url() . 'options-general.php?page=format-media-titles/format-media-titles.php">' . __( 'Settings' ) . '</a>';
		/* Make the 'Settings' link appear first. */
		array_unshift( $links, $fmt_links );
	}

	return $links;
}

function fmt_update_media_title( $id ) {

	$options     = get_option( 'fmt_options' );
	$cap_options = $options['rdo_cap_options'];

	$uploaded_post_id = get_post( $id );
	$title            = $uploaded_post_id->post_title;

	/* Update post. */
	$char_array = array();
	if ( isset( $options['chk_hyphen'] ) && $options['chk_hyphen'] ) {
		$char_array[] = '-';
	}
	if ( isset( $options['chk_underscore'] ) && $options['chk_underscore'] ) {
		$char_array[] = '_';
	}
	if ( isset( $options['chk_period'] ) && $options['chk_period'] ) {
		$char_array[] = '.';
	}
	if ( isset( $options['chk_tilde'] ) && $options['chk_tilde'] ) {
		$char_array[] = '~';
	}
	if ( isset( $options['chk_plus'] ) && $options['chk_plus'] ) {
		$char_array[] = '+';
	}

	/* Replace chars with spaces, if any selected. */
	if ( ! empty( $char_array ) ) {
		$title = str_replace( $char_array, ' ', $title );
	}

	/* Trim multiple spaces between words. */
	$title = preg_replace( "/\s+/", " ", $title );

	/* Capitalize Title. */
	switch ( $cap_options ) {
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

	// add formatted title to the alt meta field too
	if ( isset( $options['chk_alt'] ) && $options['chk_alt'] ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $title );
	}

	// Update the post into the database
	$uploaded_post               = array();
	$uploaded_post['ID']         = $id;
	$uploaded_post['post_title'] = $title;
	wp_update_post( $uploaded_post );
}