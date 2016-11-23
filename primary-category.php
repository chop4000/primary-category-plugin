<?php
/**
 * Build custom field meta box
 *
 * @param post $post The post object
 */
/*
Plugin Name: Primary Category Plugin
Description: This plugin allows you to select a Primary Category for your posts.
Author: Chris Hopkins
Version: 1.0
Author URI: http://chris-hopkins.org/
*/

	// Define constant for plugin path.
	define('script_url', plugins_url( '', __FILE__) );

	// Include the CSS file.
	wp_enqueue_style('script_url', script_url . '/assets/primary-category.css');	

	// Add actions to run the meta box functions at appropriate hooks.
	add_action( 'load-post.php', 'primary_category_meta_box_setup' );
	add_action( 'load-post-new.php', 'primary_category_meta_box_setup' );

	// Meta box setup function.
	function primary_category_meta_box_setup() {

		// Add meta boxes on the 'add_meta_boxes' hook.
		add_action( 'add_meta_boxes', 'primary_category_add_post_meta_boxes' );

		// Save post meta on the 'save_post' hook.
		add_action( 'save_post', 'primary_category_save_post_class_meta', 10, 2 );
	
	}

	// Create a meta box to be displayed on the post editor screen.
	function primary_category_add_post_meta_boxes() {

		add_meta_box(
			'primary_category_post_class',      			// Unique ID
			esc_html__( 'Primary Category', 'example' ),    // Title
			'primary_category_post_class_meta_box',   		// Callback function
			'post',         								// Admin page (or post type)
			'side',         								// Context
			'default'         								// Priority
		);

	}

	// Save the meta box's post metadata.
	function primary_category_save_post_class_meta( $post_id, $post ) {

  		// Verify the nonce before proceeding.
		if ( !isset( $_POST['primary_category_post_class_nonce'] ) || !wp_verify_nonce( $_POST['primary_category_post_class_nonce'], basename( __FILE__ ) ) )
		return $post_id;

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post.
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

		// Get the posted data and sanitize it for use as an HTML class.
		//$new_meta_value = ( isset( $_POST['primary_category_dropdown'] ) ? sanitize_html_class( $_POST['primary_category_dropdown'] ) : '' );
		//$new_meta_value = 'b27';
		$new_meta_value = $_POST['primary_category_dropdown'];

		// Get the meta key.
		$meta_key = 'primary_category_dropdown_meta_key';

		// Get the meta value of the custom field key.
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		// If a new meta value was added and there was no previous value, add it.
		if ( $new_meta_value && '' == $meta_value ) {
			
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		}
		// If the new meta value does not match the old value, update it.
		elseif ( $new_meta_value && $new_meta_value != $meta_value ) {

			update_post_meta( $post_id, $meta_key, $new_meta_value );

		}
		// If there is no new meta value but an old value exists, delete it.
		elseif ( '' == $new_meta_value && $meta_value ) {
		
			delete_post_meta( $post_id, $meta_key, $meta_value );

		}
	}

	// Display the post meta box.
	function primary_category_post_class_meta_box( $object, $box ) { ?>

	<?php wp_nonce_field( basename( __FILE__ ), 'primary_category_post_class_nonce' ); ?>

	<p>
	    
	    <?php

	    	// Get this post's current meta key value for selecte primary category.
	    	$selected_key_value = get_post_meta( get_the_ID(), 'primary_category_dropdown_meta_key', true );

	    	// Define options/arguments for the categories dropdown.
			$arguments = array(
				'hide_empty' => 0,
				'id' => 'primary-category-dropdown',
				'name' => 'primary_category_dropdown',
				'option_none_value' => '',
				'selected' => $selected_key_value,
				'show_option_none' => '(None)',
				'value_field' => 'name'
			);

			// Generate the categories dropdown using the $arguments.
			wp_dropdown_categories ( $arguments );

		?>

	</p>

<?php }