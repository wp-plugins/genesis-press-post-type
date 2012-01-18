<?php
/*
Plugin Name: Genesis Press Post Type
Plugin URI: http://oc2.co
Description: This plugin creates a custom post type called Press and a Featured Press Widget that were specifically designed for the media bookmarks on the Series child themes for the Genesis Framework.  This plugin was designed for and tested with the Genesis Framework and will work with other Genesis child themes.
Version: 0.5.9.6
Author: Will Anderson, Derick Schaefer, Matt Lawrence
Author URI: http://wpthe.com/plugins/genesis-press-post-type/
*/
?>
<?php

// Re-used from Jenifer M. Dodd under the terms GNU General Public License
// Include custom post types in the loop for any theme.


// require Genesis 1.6 upon activation
register_activation_hook( __FILE__, 'gpp_activation_check' );
function gpp_activation_check() {

		$latest = '1.6';
		
		$theme_info = get_theme_data( TEMPLATEPATH.'/style.css' );
	
        if ( basename( TEMPLATEPATH ) != 'genesis' ) {
	        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself
            wp_die( 'Sorry, you can\'t activate unless you have installed <a href="http://www.studiopress.com/themes/genesis">Genesis</a>' );
		}

		if ( version_compare( $theme_info['Version'], $latest, '<' ) ) {
                deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself
                wp_die( 'Sorry, you can\'t activate without <a href="http://www.studiopress.com/support/showthread.php?t=19576">Genesis '.$latest.'</a> or greater' );
        }

}

add_filter( 'pre_get_posts' , 'ucc_include_custom_post_types' );
function ucc_include_custom_post_types( $query ) {
  global $wp_query;

  // Leave admin pages, including preview, in tact..
  if ( !is_preview() && !is_admin() && !is_singular() ) {
    $args = array(
      'public' => true ,
      '_builtin' => false
    );
    $output = 'names';
    $operator = 'and';

    $post_types = get_post_types( $args , $output , $operator );

    /* Add 'link' and/or 'page' to array() if you want these included:
     * array( 'post' , 'link' , 'page' ), etc.
     */
    $post_types = array_merge( $post_types , array( 'post' ) );

    if ($query->is_feed) {
      /* Do feed processing here if you did not exclude it previously. This if/else
       * is not necessary if you want custom post types included in your feed.
       */
    } else {
      $my_post_type = get_query_var( 'post_type' );
      if ( empty( $my_post_type ) )
        $query->set( 'post_type' , $post_types );
    }
  }

  return $query;
}

function gpp_mod_post_meta( $post_meta ) {
    global $post;
    if ( 'news' == $post->post_type ) {
        ob_start();
        include dirname( __FILE__ ) . '/views/output-post-meta.php';
        $post_meta = ob_get_clean();
    }
    return $post_meta;
}

add_action( 'genesis_loop', 'gpp_check_for_tags_and_cats' );
function gpp_check_for_tags_and_cats() {
    if ( is_tag() || is_category() ) {
        add_action( 'genesis_post_meta', 'gpp_mod_post_meta' );
    }
}

/*  Will Andersen - This is some initial code we started */        
// This is supposed to be removing the default info and printing out the meta info but it's also echoing the title and excerpt which is ugly

/*
add_filter('genesis_post_meta', 'nd_mod_post_meta');
function nd_mod_post_meta($post_meta) {


   $post_meta = get_post_meta( $post->ID, 'source', true );
		?><h2><a href="<?php echo empty( $source['sourceNameLink'] ) ? get_permalink() : esc_attr( $source['sourceNameLink'] ); ?>" target="_blank" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2><?php
		the_excerpt();
		$post_meta = '[post_categories] [post_tags]';
		return $post_meta; 
  		}
 }

*/

/*  Will Anderson - This is the end of this code */

// Created by Derick Schaefer and Scott Ellis

// Add an action for creating news posts
add_action('init', 'create_press_posts');
function create_press_posts() {
	$newspost_args = array(
		'labels' => array(
						'name' => __( 'Press' ),
						'singular_name' => __( 'Press' ),
						'add_new' => __( 'Add New' ),
						'add_new_item' => __( 'Add New Press' ),
						'edit' => __( 'Edit' ),
						'edit_item' => __( 'Edit Press' ),
						'new_item' => __( 'New Press' ),
						'view' => __( 'View Press' ),
						'view_item' => __( 'View Press' ),
						'search_items' => __( 'Search Press' ),
						'not_found' => __( 'No super Press' ),
						'not_found_in_trash' => __( 'No Press found in Trash' ),
						'parent' => __( 'Parent Press' ),
					),

		'public' => true,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'menu_position' => 20,
		'capability_type' => 'post',
		'hierarchical' => true,
		'rewrite' => array('slug'=>'press',  'with_front' => true ),
		'can_export' => true,
		'supports' => array('title', 'excerpt', 'thumbnail'),
		'has_archive' => true,
		'taxonomies' => array('post_tag', 'category'),
		'menu_icon' =>  plugins_url() . '/genesis-press-post/images/presspost16.png'
		
		
		// removed from supports , 'editor'
	);
	register_post_type('news',$newspost_args);
}

function gpp_showMessage($message, $errormsg = true)
{
	if ($errormsg) {
		echo '<div id="message" class="error">';
	}
	else {
		echo '<div id="message" class="updated fade">';
	}

	echo "<p><strong>$message</strong></p></div>";
}

function gfpp_admin_head() {

		global $post_type;
		
		if ( (isset( $_GET['post_type'] ) && $_GET['post_type'] == 'news') || $post_type == 'news') :
		//echo "Hello!";
		// echo '#icon-edit { background:transparent url(press-post.png) no-repeat; }'	; 
		?><style>
		#icon-edit { background:transparent url('<?php echo get_option('siteurl') . '/wp-content/plugins/genesis-press-post/images/presspost32.png';?>') no-repeat; }
		
		</style>
		<?php endif;
		
		

			
        // echo '<link rel="stylesheet" type="text/css" href="' .plugins_url('wp-admin.css', __FILE__). '">';
		}

add_action('admin_head', 'gfpp_admin_head');

//Update External Link
add_action('save_post', 'gpp_update_externalLink');
function gpp_update_externalLink(){
	global $post;
	
	if ( !$post )
		return;

	$sourceName = array(
		'sourceName' => isset( $_POST['sourceName'] ) ? $_POST['sourceName'] : '',
		'sourceNameLink' => isset( $_POST['sourceNameLink'] ) ? esc_url_raw( $_POST['sourceNameLink'] ) : ''
	);	
	update_post_meta($post->ID, 'source', $sourceName);
	
	// if ( empty ( $_POST['sourceName'] ) ) : gpp_showMessage('Your Press Post has an empty Source Name.');  endif;
	// echo $_POST['sourceName'];
	
	$article1 = array(
		'article1' => isset( $_POST['article1'] ) ? $_POST['article1'] : '',
		'articleLink1' => isset( $_POST['articleLink1'] ) ?  esc_url_raw( $_POST['articleLink1'] ) : ''
	);	
	update_post_meta($post->ID, 'article1', $article1);
	
	$article2 = array(
		'article2' => $_POST['article2'],
		'articleLink2' => esc_url_raw( $_POST['articleLink2'] )
	);
	update_post_meta($post->ID, 'article2', $article2);
}

// Add Source and Related Articles
add_action("admin_init","add_PressPost");
function add_PressPost() {
	add_meta_box("newspost_article","Press Sources","PressPost","news","normal","low");
	
	
	$current_theme = get_current_theme();
	$current_wp_version = get_bloginfo('version');
		
		
		if ( !defined( 'PARENT_THEME_NAME' ) ) :
				gpp_showMessage('The Genesis Press Post Plugin is running on an unsupported theme - ' . $current_theme);			
		elseif ( defined( 'PARENT_THEME_NAME' ) && 'Genesis' <> PARENT_THEME_NAME ) :
				gpp_showMessage("The Genesis Press Post Plugin is running on an unsupported theme framework " . PARENT_THEME_NAME . "and child theme " . $current_theme . ".");			
		endif;
		
		if ( $current_wp_version < 3.1 ) :
			gpp_showMessage('The Genesis Press Post Plugin requires WordPress 3.1 or higher.');
		endif;
		
}

function PressPost() {
	global $post;
	
	$source = get_post_meta($post->ID, 'source', true);
	$sourceName = isset( $source["sourceName"] ) ? $source["sourceName"] : '';
	$sourceLink = isset( $source["sourceNameLink"] ) ? $source["sourceNameLink"] : '';
	
	$article1 = get_post_meta($post->ID, 'article1', true);
	$articleName1 = isset( $article1["article1"] ) ? $article1["article1"] : '';
	$articleLink1 = isset( $article1["articleLink1"] ) ? $article1["articleLink1"] : '';
	
	$article2 = get_post_meta($post->ID, 'article2', true);
	$articleName2 = isset( $article1["articleLink1"] ) ? $article2["article2"] : '';
	$articleLink2 = isset( $article1["articleLink1"] ) ? $article2["articleLink2"] : '';
	
	include( dirname( __FILE__ ) . '/views/meta-custom-post.php');
}

// Update news posts
add_filter("manage_edit-newspost_columns", "newspost_edit_columns");

function newspost_edit_columns($newspost_columns){
	$newspost_columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => "Project Title",
		"description" => "Description",
		"author" => "Author",
		"date" => "Date",
	);	
	return $newspost_columns;
}

// Display news posts
add_action("manage_posts_custom_column",  "newspost_columns_display");

function newspost_columns_display($newspost_columns){
	global $post;	
	switch ($newspost_columns)
	{
		case "description":
			the_excerpt();
			break;
	}
 	
}

// Modified implementation of the Genesis Featured Post under the terms of the GNU General Public License

add_action('widgets_init', create_function('', "register_widget('Genesis_Featured_Press');"));
class Genesis_Featured_Press extends WP_Widget {

	function Genesis_Featured_Press() {
		$widget_ops = array( 'classname' => 'featuredpress', 'description' => __('Displays featured press custom post type with thumbnails', 'genesis') );
		$control_ops = array( 'width' => 505, 'height' => 350, 'id_base' => 'featured-news' );
		$this->WP_Widget( 'featured-news', __('Genesis - Featured Press', 'genesis'), $widget_ops, $control_ops );
	}
	

	function widget($args, $instance) {
		extract($args);
		global $post;
		// defaults
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'posts_cat' => '',
			'posts_num' => 1,
			'posts_offset' => 0,
			'orderby' => '',
			'order' => '',
			'show_image' => 0,
			'image_alignment' => '',
			'image_size' => '',
			'show_gravatar' => 0,
			'gravatar_alignment' => '',
			'gravatar_size' => '',
			'show_title' => 0,
			'show_source' => 0,
			'post_info' => '[post_date] ' . __('By', 'genesis') . ' [post_author_posts_link] [post_comments]',
			'show_content' => 'excerpt',
			'content_limit' => '',
			'more_text' => __('[Read More...]', 'genesis'),
			'extra_num' => '',
			'extra_title' => '',
			'more_from_category' => '',
			'more_from_category_text' => __('More Posts from this Category', 'genesis')
		) );
		
		$featured_posts = new WP_Query(array(
											'post_type' => 'news',
											'cat' => $instance['posts_cat'],
											'showposts' => $instance['posts_num'],
											'offset' => $instance['posts_offset'],
											'orderby' => $instance['orderby'],
											'order' => $instance['order']));

		if ( !empty( $instance['extra_num'] ) ) {
			$offset = $featured_posts->post_count;	
			$extra_posts = new WP_Query( array(
											  'post_type' => 'news',
											  'cat' => $instance['posts_cat'],
											  'showposts' => $instance['extra_num'],
											  'offset' => $offset ) );
		}
		
		// include( dirname( __FILE__ ) . '/views/output-genesis-widget.php' );

	
		echo $before_widget;

		if ( !empty( $instance['title'] ) ) {
			echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;
		}

		if($featured_posts->have_posts()) : while($featured_posts->have_posts()) : $featured_posts->the_post(); ?>

			<?php $source = get_post_meta( $post->ID, 'source', true ); ?>
			
			<div <?php post_class(); ?>>
			<?php if ( !empty( $instance['show_image'] ) ) { ?>
				<a href="<?php echo esc_attr( $source['sourceNameLink'] ); ?>" title="<?php the_title_attribute(); ?>" class="<?php echo esc_attr( $instance['image_alignment'] ); ?>"><?php echo genesis_get_image( array( 'format' => 'html', 'size' => $instance['image_size'] ) ); ?></a>
			<?php } ?>
	
			<?php if( !empty( $instance['show_title'] ) ) { ?>
				<h2>
					<a href="<?php echo empty( $source['sourceNameLink'] ) ? get_permalink() : esc_attr( $source['sourceNameLink'] ); ?>" target="_blank" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
				</h2>
			<?php } ?>
	
			<?php if( !empty( $instance['show_content'] ) ) {
				the_excerpt();
                if ( $instance['show_source'] ) {
				    include dirname( __FILE__ ) . '/views/output-post-meta.php';
                }
	        } ?> </div><!--end <?php post_class(); ?> -->
<?php endwhile; endif;

		if( !empty( $instance['more_from_category']) && !empty( $instance['posts_cat'] )  /* && $extra_posts->have_posts() */ ) { ?> 
			<p class="more-from-category">
			<div class="post-meta"> <a href="<?php echo get_category_link( $instance['posts_cat'] ); ?>" title="<?php echo get_cat_name( $instance['posts_cat'] ); ?>"><?php echo esc_html( $instance['more_from_category_text'] ); ?></a>
			</p></div>
<?php
}


			// The EXTRA Posts (list)
			if ( !empty( $instance['extra_num'] ) ) :

					if ( !empty($instance['extra_title'] ) )
						echo $before_title . esc_html( $instance['extra_title'] ) . $after_title;

					$offset = intval($instance['posts_num']) + intval($instance['posts_offset']);
					$extra_posts = new WP_Query( array( 'cat' => $instance['posts_cat'], 'showposts' => $instance['extra_num'], 'offset' => $offset ) );

					$listitems = '';
					if ( $extra_posts->have_posts() ) :

						while ( $extra_posts->have_posts() ) :

							$extra_posts->the_post();
							$listitems .= sprintf( '<li><a href="%s" title="%s">%s</a></li>', get_permalink(), the_title_attribute('echo=0'), get_the_title() );

						endwhile;

						if ( strlen($listitems) > 0 ) {
							printf( '<ul>%s</ul>', $listitems );
						}

					endif;

			endif;
echo $after_widget;
		
		wp_reset_query();
	}

/* End Include Output Code */

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {

		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
			'posts_cat' => '',
			'posts_num' => 0,
			'posts_offset' => 0,
			'orderby' => '',
			'order' => '',
			'show_image' => 0,
			'image_alignment' => '',
			'image_size' => '',
			'show_gravatar' => 0,
			'gravatar_alignment' => '',
			'gravatar_size' => '',
			'show_title' => 0,
			'show_source' => 0,
			'post_info' => '[post_date] ' . __('By', 'genesis') . ' [post_author_posts_link] [post_comments]',
			'show_content' => 'excerpt',
			'content_limit' => '',
			'more_text' => __('[Read More...]', 'genesis'),
			'extra_num' => '',
			'extra_title' => '',
			'more_from_category' => '',
			'more_from_category_text' => __('More Press from this Category', 'genesis')
		) );
		 
			include( dirname( __FILE__ ) . '/views/form-genesis-widget.php');
		
		
	}
}

	
	/// DISPLAY

function displayCustomPostMeta() {
	include('views/meta-custom-post.php');}
