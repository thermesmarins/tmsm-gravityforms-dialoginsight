<?php

/**
 * @link              https://github.com/nicomollet
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       TMSM Gravity Forms DialogInsight Add-On
 * Plugin URI:        https://github.com/thermesmarins/tmsm-gravityforms-dialoginsight
 * Description:       Integrates Gravity Forms with DialogInsight, allowing form submissions to be automatically sent to your DialogInsight account
 * Version:           1.0.1
 * Author:            Nicolas Mollet
 * Author URI:        https://github.com/nicomollet
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tmsm-gravityforms-dialoginsight
 * Domain Path:       /languages
 * Github Plugin URI: https://github.com/thermesmarins/tmsm-gravityforms-dialoginsight
 * Github Branch:     master
 * Requires PHP:      5.6
 */

define( 'GF_DIALOGINSIGHT_VERSION', '1.0.1' );

// If Gravity Forms is loaded, bootstrap the DialogInsight Add-On.
add_action( 'gform_loaded', array( 'GF_DialogInsight_Bootstrap', 'load' ), 5 );

/**
 * Class GF_DialogInsight_Bootstrap
 *
 * Handles the loading of the DialogInsight Add-On and registers with the Add-On Framework.
 */
class GF_DialogInsight_Bootstrap {

	/**
	 * If the Feed Add-On Framework exists, DialogInsight Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-dialoginsight.php' );

		GFAddOn::register( 'GFDialogInsight' );
	}
}

/**
 * Returns an instance of the GFDialogInsight class
 *
 * @see    GFDialogInsight::get_instance()
 *
 * @return object GFDialogInsight
 */
function gf_dialoginsight() {
	return GFDialogInsight::get_instance();
}
