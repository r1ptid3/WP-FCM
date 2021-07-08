<?php
/**
 * Functions and definitions
 *
 * @package Theme
 *
 * @since   1.0.0
 */

// Disable direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! defined( 'R1__VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'R1__VERSION', '1.0.0' );
}

/**
 * Register the theme assets
 *
 * @since   1.0.0
 *
 * @return  void
 */
function assets(): void {

	// Theme's scripts.
	wp_register_script(
		'push-script',
		get_template_directory_uri() . '/assets/js/push-notification.js',
		array( 'jquery' ),
		R1__VERSION,
		true
	);

	// Localize ajax scripts.
	wp_localize_script(
		'push-script',
		'ajax_obj',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);
	wp_enqueue_script( 'push-script' );

	// Firebase scripts.
	wp_enqueue_script(
		'firebase-app',
		'https://www.gstatic.com/firebasejs/8.7.0/firebase-app.js',
		array(),
		'8.7.0',
		false
	);

	wp_enqueue_script(
		'firebase-messaging',
		'https://www.gstatic.com/firebasejs/8.7.0/firebase-messaging.js',
		array( 'firebase-app' ),
		'8.7.0',
		false
	);

}
add_action( 'wp_enqueue_scripts', 'assets' );


/**
 * Save user's token into tokens array.
 *
 * @since   1.0.0
 *
 * @return  void
 */
function update_notification_tokens() {
	$tokens = array();

	if ( ! empty( $_POST['token'] ) ) {

		// Option creation if it's not set.
		if ( false === get_option( 'notification_tokens' ) ) {
			update_option( 'notification_tokens', array() );
		}

		$tokens = get_option( 'notification_tokens' );

		// Sanitize input token.
		$token = sanitize_text_field( wp_unslash( $_POST['token'] ) );

		if ( ! in_array( $token, $tokens, true ) ) {
			$tokens[] = $token;
		}

		update_option( 'notification_tokens', $tokens );

		// TODO: Remove ajax debug information.
		echo wp_json_encode( $tokens );
		echo $_POST['token'];

		die();
	}
}

add_action( 'wp_ajax_update_notification_tokens', 'update_notification_tokens' );
add_action( 'wp_ajax_nopriv_update_notification_tokens', 'update_notification_tokens' );

/**
 * This is the code, which send push notification.
 * As an example:
 * To show notification after post has been published
 * attach this js onto 'transition_post_status' hook.
 *
 * @param   string $title notification title.
 * @param   string $message notification message.
 * @param   string $icon url to notification image.
 * @param   string $click the url to which the user will be redirected after clicking on the notification.
 *
 * @since   1.0.0
 *
 * @return  void
 */
function send_notification( $title, $message, $icon, $click ) {

	// Keep this URL!
	$url = 'https://fcm.googleapis.com/fcm/send';

	$notification_tokens = get_option( 'notification_tokens' );

	if (
		! empty( $title ) &&
		! empty( $message ) &&
		! empty( $notification_tokens )
	) {

		// Sanitize all $_REQUEST data.
		$message = sanitize_text_field( wp_unslash( $message ) );
		$title   = sanitize_text_field( wp_unslash( $title ) );
		$icon    = ! empty( $icon ) ? esc_url_raw( wp_unslash( $icon ) ) : '';
		$click   = ! empty( $click ) ? esc_url_raw( wp_unslash( $click ) ) : '/';

		$fields = array(
			'registration_ids' => $notification_tokens,
			'notification'     => array(
				'body'         => $message,
				'title'        => $title,
				'icon'         => $icon,
				'click_action' => $click,
			),
		);

		/*
		 * Change this to your Server Key from ( Firebase -> Project settings -> Cloud Messaging -> Server key ).
		 * keep 'key='
		 */
		$headers = array(
			'Authorization: key=<YOUR KEY>',
			'Content-Type: application/json',
		);

		$ch = curl_init();

		// Set the url, number of POST vars, POST data.
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, wp_json_encode( $fields ) );

		// Execute post.
		$result = curl_exec( $ch );

		// Close connection.
		curl_close( $ch );

	}
}

/**
 * This is function code, which send push notification on publish new post
 *
 * @param   string $new_status post status after click action post button.
 * @param   string $old_status post status before post update.
 * @param   string $post post post object.
 *
 * @since   1.0.0
 *
 * @return  void
 */
function notification_on_publish( $new_status, $old_status, $post ) {
	if (
		'publish' === $new_status &&
		'publish' !== $old_status &&
		'post' === $post->post_type
	) {

		// Here you can change all notification content.
		$title   = 'Your favorite website published new post!';
		$message = 'New ' . $post->post_title . ' has been added';
		$icon    = 'https://cdn2.iconfinder.com/data/icons/lucid-generic/24/new_artboard_file_create_post-512.png';
		$click   = 'https://www.youtube.com/watch?v=3WAOxKOmR90';

		send_notification( $title, $message, $icon, $click );
	}
}
add_action( 'transition_post_status', 'notification_on_publish', 10, 3 );
