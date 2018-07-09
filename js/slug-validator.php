<?php
/* 	
	Plugin Name: WP-Slug-Checker
	Version: 1.1
*/
	
//////////////////////////this is duplicate title check///////////////////////////////////

	//jQuery to send AJAX request - only available on the post editing page
	function duplicate_titles_enqueue_scripts( $hook ) {

		if( !in_array( $hook, array( 'post.php', 'post-new.php' ) ) )
			return;

		wp_enqueue_script( 'duptitles',wp_enqueue_script( 'duptitles',plugins_url().'/wp-slug-validator/js/slug-validator.js',array( 'jquery' )), array( 'jquery' )  );
	}
	add_action( 'admin_enqueue_scripts', 'duplicate_titles_enqueue_scripts', 2000 );

	add_action('wp_ajax_title_check', 'duplicate_title_check_callback');

	function duplicate_title_check_callback() {

		function title_check() {

			$title = $_POST['post_title'];
			$post_id = $_POST['post_id'];
			$post_type = $_POST['post_type'];

			global $wpdb;

			$sim_titles = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = '".$post_type."' 
						AND post_title = '{$title}' AND ID != {$post_id} ";

			$sim_results = $wpdb->get_results($sim_titles);

			if($sim_results) {
				echo "
            <script type=\"text/javascript\">
			document.getElementById('save-post').disabled = true;
			document.getElementById('snippet-editor-slug').readOnly = true; 
			document.getElementById('snippet-editor-slug').placeholder = \"Please change the title/slug\";
            </script>
        ";
				$titles = '<ul>';
				foreach ( $sim_results as $the_title ) 
				{
					$titles .= '<li>'.$the_title->post_title.'</li>';
				}
				$titles .= '</ul>';
			   
				//return $titles;
				$slug_message = "<div style='color:red'>Please enter another title it's already exists</div>";
				//$save_button = "$('#save-post').append('<span id=\"check-title-btn\"><a class=\"button\" href=\"#\">Check Title</a></span>')";
				return $slug_message ;//. " " . $save_button;
			} else {
				echo "
            <script type=\"text/javascript\">
			document.getElementById('save-post').disabled = false; 
			document.getElementById('snippet-editor-slug').readOnly = false; 
			document.getElementById('snippet-editor-slug').placeholder = \"Please put a unique slug here\";

            </script>
        ";
				return '<div style="color:green">This title is unique</div>';
			}
		}		
		echo title_check();
		die();
	}

	function disable_autosave() {
		wp_deregister_script('autosave');
	}
	add_action( 'wp_print_scripts', 'disable_autosave' );


?>
