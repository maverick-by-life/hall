<?php

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'main-style', get_stylesheet_uri(), [], filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_script( 'main-script', get_template_directory_uri() . '/main.js', [ 'jquery' ],
		filemtime( get_stylesheet_directory() . '/main.js' ) );
} );
add_action( 'wp_head', function () {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
} );

add_theme_support( 'post-thumbnails' );
add_theme_support( 'custom-logo' );

// Добавляем атрибуты rel к ссылке
function add_nofollow_to_custom_logo( $html ) {
	$html = str_replace( '<a ', '<a rel="noindex nofollow" ', $html );

	return $html;
}

add_filter( 'get_custom_logo', 'add_nofollow_to_custom_logo' );


/**
 * Обертка "Кота" для адимнки
 */
add_action( 'admin_head', function () {
	wp_enqueue_script( 'cat-script', get_template_directory_uri() . '/cat.js' );
} );

add_filter( 'login_headerurl', function () {
	return 'https://01cat.ru';
} );

add_action( 'login_header', function () { ?>
	<style>
        #login h1 a {
            background: url("logo.png") center top no-repeat !important;
            width: 111px !important;
            height: 180px !important;
        }
	</style>
<?php } );
add_filter( 'admin_footer_text', function () {
	return '<b>Сделано:</b>
			<a href="https://01cat.ru/" target="_blank">Двоичный кот</a>
			<br>
			<b>Техническая поддержка:</b> тел. <a href="tel:+79145416354">+7 (914) 541-63-54</a>, email: <a href="mailto:hello@01cat.ru">hello@01cat.ru</a>';
} );

/**
 * Валидация форм Contact Form 7
 */
add_filter( 'wpcf7_validate', 'my_form_validate', 10, 2 );
function my_form_validate( $result, $tags ) {
	// Получим данные об отправляемой форме
	$form = WPCF7_Submission::get_instance();

	// Получаем данные полей
	$callbackPhone   = $form->get_posted_data( 'callback-phone' );

	// Проверяем результат
		if ( empty( $callbackPhone ) ) {
		$result->invalidate( 'callback-phone', 'Это поле обязательно' );
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
		functions . phpchr( 0xC2 ) . chr( 0xA0 ),
	];

	return str_replace( $to_replace, '', $phone );
}
