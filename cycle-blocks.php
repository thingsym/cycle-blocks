<?php
/**
 * Plugin Name: Cycle Blocks
 * Plugin URI: https://github.com/thingsym/cycle-blocks
 * Description: Cycle Blocks plugin is a collection of block for block themes (Full Site Editing).
 * Version: 1.0.0
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

require_once plugin_dir_path( __FILE__ ) . 'inc/autoload.php';

if ( class_exists( 'Cycle_Blocks\Cycle_Blocks' ) ) {
	new \Cycle_Blocks\Cycle_Blocks();
};
