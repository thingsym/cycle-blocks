<?php
/**
 * Autoloader
 *
 * @package Cycle_Blocks
 * @since 1.0.0
 */

/**
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt
 * to load the \Cycle_Blocks\Blocks\Cycle_Blocks_Foo class
 * from /dist/php/class-foo.php:
 *     new \Cycle_Blocks\Blocks\Cycle_Blocks_Foo;
 *
 * @param string|mixed
 * @return void
 */
spl_autoload_register( // @phpstan-ignore-line
	/**
	 * Registed autoload function
	 *
	 * @param string $class The fully-qualified class name.
	 * @return void
	 */
	function( $class ) {
		/* plugin-specific namespace prefix */
		$prefix = 'Cycle_Blocks\\';
		$len    = strlen( $prefix );

		if ( 0 !== strncmp( $prefix, $class, $len ) ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$relative_class = str_replace( '\\', '/', $relative_class );

		/**
		 * WordPress Naming Conventions
		 * See https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
		 */
		$relative_class = strtolower( $relative_class );
		$relative_class = str_replace( '_', '-', $relative_class );

		if ( preg_match( '/^cycle-blocks$/', $relative_class ) ) {
			// load main class from /inc.
			$relative_class = preg_replace( '/^(.*)$/', 'inc/class-$1', $relative_class );
		}
		elseif ( preg_match( '/^blocks/', $relative_class ) ) {
			// load blocks class from /dist/php.
			$relative_class = preg_replace( '/^blocks\/(.*?)$/', 'dist/php/class-$1', $relative_class );
		}

		$path = CYCLE_BLOCKS_PATH . $relative_class . '.php';

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
);
