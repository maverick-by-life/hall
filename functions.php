<?php

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'main-style', get_stylesheet_uri(), [], filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_script( 'main-script', get_template_directory_uri() . '/main.js', [ 'jquery' ],
		filemtime( get_stylesheet_directory() . '/main.js' ) );
	wp_enqueue_script( 'jquery-ui-datepicker' );
} );
add_action( 'wp_head', function () {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
} );

add_theme_support( 'post-thumbnails' );
add_theme_support( 'custom-logo' );

//добавляем текст к логотипу
function my_custom_customize_register( $wp_customize ) {
	$wp_customize->add_setting( 'my_logo_text', [
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	] );

	$wp_customize->add_control( 'my_logo_text', [
		'label'    => __( 'Текст логотипа', 'textdomain' ),
		'section'  => 'title_tagline',
		'settings' => 'my_logo_text',
		'type'     => 'text',
	] );
}

add_action( 'customize_register', 'my_custom_customize_register' );

// Добавляем атрибуты rel к ссылке
function add_nofollow_to_custom_logo( $html ) {
	$html = str_replace( '<a ', '<a rel="noindex nofollow" ', $html );

	return $html;
}

add_filter( 'get_custom_logo', 'add_nofollow_to_custom_logo' );

// Валидация форм Contact Form 7
add_filter( 'wpcf7_validate', 'my_form_validate', 10, 2 );
function my_form_validate( $result, $tags ) {
	// Получим данные об отправляемой форме
	$form = WPCF7_Submission::get_instance();

	// Получаем данные полей
	$callbackPhone = $form->get_posted_data( 'callback-phone' );
	$callbackDate  = $form->get_posted_data( 'callback-date' );

	// Проверяем результат
	if ( empty( $callbackPhone ) ) {
		$result->invalidate( 'callback-phone', 'Это поле обязательно' );
	}
	if ( empty( $callbackDate ) ) {
		$result->invalidate( 'callback-date', 'Это поле обязательно' );
	}

	return $result;
}

/** Выводит чистый номер телефона
 *
 * @param  string  $phone  - Номер телефона
 */
function clearPhone( string $phone ): string {
	$to_replace = [
		' ',
		'-',
		'(',
		')',
		'+',
		'&nbsp;',
		chr( 0xC2 ) . chr( 0xA0 ),
	];

	return str_replace( $to_replace, '', $phone );
}

//занятые даты из acf для календаря
add_action( 'wp_footer', 'output_blocked_dates' );
function output_blocked_dates() {
	$dates_data = get_field( 'blocked_dates', 2 );
	$dates = array_column($dates_data, 'date');
	echo '<input type="hidden" id="blocked-dates" value="' . esc_attr( json_encode( $dates ) ) . '">';
}