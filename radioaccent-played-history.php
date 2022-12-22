<?php
/*
  Plugin Name: Radio Accent - Playlist
  Plugin URI: http://www.radioaccent.be
  Description: Haal de songs op voor een specifiek moment
  Version: 2.0
  Author: Fabian Tack
  Author URI: https://www.matfix.be
  License: GPLv2+
  Text Domain: radioaccent-played-history
*/

// Set locale for text
setlocale(LC_ALL, 'nl_BE');

// Autoload included classes
spl_autoload_register( function( $class_name ) {
	$file_name = __DIR__ . '/includes/' . $class_name . '.php';
	if( file_exists( $file_name ) ) {
		require $file_name;
	}
} );

// Start the main class
$pl = new Playlist;