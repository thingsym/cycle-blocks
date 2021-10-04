<?php
/**
 * Plugin Name: Cycle Blocks
 * Plugin URI: https://github.com/thingsym/cycle-blocks
 * Description: Extented blocks collection.
 * Version: 0.1.0
 * Author: thingsym
 * Author URI:  https://www.thingslabo.com/
 * License:     GPL2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cycle-blocks
 * Domain Path: /languages
 *
 * @package Cycle_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CYCLE_BLOCKS', __FILE__ );
define( 'CYCLE_BLOCKS_PATH', plugin_dir_path( CYCLE_BLOCKS ) );

require_once CYCLE_BLOCKS_PATH . 'inc/autoload.php';

if ( class_exists( 'Cycle_Blocks\Cycle_Blocks' ) ) {
	new \Cycle_Blocks\Cycle_Blocks();
};
