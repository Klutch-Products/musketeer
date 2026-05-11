<?php
/**
* Image processing with Intervention Image
*/
function musketeer_resize_and_watermark( $image_path, $width, $height, $watermark_text ) {
if ( ! class_exists( 'Intervention\Image\ImageManager' ) ) {
return false;
}

$manager = new \Intervention\Image\ImageManager( [ 'driver' => 'gd' ] );
$image   = $manager->make( $image_path );

// Resize image
$image->resize( $width, $height, function ( $constraint ) {
$constraint->aspectRatio();
$constraint->upsize();
} );

// Add watermark
$image->text( $watermark_text, $width - 10, $height - 10, function ( $font ) {
$font->file( get_template_directory() . '/assets/fonts/arial.ttf' );
$font->size( 24 );
$font->color( '#ffffff' );
$font->align( 'right' );
$font->valign( 'bottom' );
} );

$upload_dir     = wp_upload_dir();
$processed_path = $upload_dir['path'] . '/processed_' . basename( $image_path );
$image->save( $processed_path );

return $upload_dir['url'] . '/processed_' . basename( $image_path );
}
<?php
/**
 * Musketeer Theme Functions
 *
 * @package Musketeer
 * @since   1.0.0
 */
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Autoload Composer dependencies
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Setup block editor features
 */
function musketeer_setup_block_editor() {
	// Block editor support
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor-style.css' );

	// Add support for core block patterns
	add_theme_support( 'core-block-patterns' );

	// Custom color palette
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Primary', 'musketeer' ),
				'slug'  => 'primary',
				'color' => '#007cba',
			),
			array(
				'name'  => __( 'Secondary', 'musketeer' ),
				'slug'  => 'secondary',
				'color' => '#6c757d',
			),
		)
	);
}

/**
 * Theme setup
 */
function musketeer_setup() {
	// Add theme support for various features
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support( 'custom-logo' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );

	// Register navigation menus
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'musketeer' ),
			'footer'  => esc_html__( 'Footer Menu', 'musketeer' ),
		)
	);

	// Setup block editor features
	musketeer_setup_block_editor();
}

add_action( 'after_setup_theme', 'musketeer_setup' );

/**
 * Enqueue scripts and styles
 */
function musketeer_scripts(): void {
	// Enqueue main stylesheet
	wp_enqueue_style( 'musketeer-style', get_stylesheet_uri(), array(), '1.0.0' );

	// Enqueue Bootstrap CSS
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/node_modules/bootstrap/dist/css/bootstrap.min.css', array(), '5.3.0' );

	// Enqueue custom CSS
	wp_enqueue_style( 'musketeer-custom', get_template_directory_uri() . '/assets/css/style.css', array( 'bootstrap' ), '1.0.0' );

	// Enqueue Bootstrap JS
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', array(), '5.3.0', true );

	// Enqueue custom JS
	wp_enqueue_script(
		'musketeer-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(
			'jquery',
			'bootstrap',
		),
		'1.0.0',
		true
	);

	// Localize script for AJAX
	wp_localize_script(
		'musketeer-main',
		'musketeer_ajax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'musketeer_nonce' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'musketeer_scripts' );

/**
 * Image processing with Intervention Image
 *
 * @param string $image_path     Path to the image file.
 * @param int    $width          Width of the output image.
 * @param int    $height         Height of the output image.
 * @param string $watermark_text Text to use as watermark.
 *
 * @return false|void Returns false if ImageManager class doesn't exist.
 */
function musketeer_resize_and_watermark( $image_path, $width, $height, $watermark_text ) {
	if ( ! class_exists( 'Intervention\Image\ImageManager' ) ) {
		return false;
	}

	$manager = new \Intervention\Image\ImageManager( array( 'driver' => 'gd' ) );
	$image   = $manager->make( $image_path );

	// Resize image
	$image->resize(
		$width,
		$height,
		function ( $constraint ) {
			$constraint->aspectRatio();
			$constraint->upsize();
		}
	);

	// Add watermark
	$image->text(
		$watermark_text,
		$width - 10,
		$height - 10,
		function ( $font ) {
			$font->file( get_template_directory() . '/assets/fonts/arial.ttf' );
			$font->size( 24 );
			$font->color( '#ffffff' );
			$font->align( 'right' );
			$font->valign( 'bottom' );
		}
	);
}

