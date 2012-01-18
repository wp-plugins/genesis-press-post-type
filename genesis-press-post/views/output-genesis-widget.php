<?php

echo $before_widget;

if ( !empty( $instance['title'] ) ) {
	echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;
}

if($featured_posts->have_posts()) : while($featured_posts->have_posts()) : $featured_posts->the_post(); ?>

<div <?php post_class(); ?>>
	<?php if ( !empty( $instance['show_image'] ) ) { ?>
	<a href="<?php echo esc_attr( $source['sourceNameLink'] ); ?>" title="<?php the_title_attribute(); ?>" class="<?php echo esc_attr( $instance['image_alignment'] ); ?>"><?php echo genesis_get_image( array( 'format' => 'html', 'size' => $instance['image_size'] ) ); ?></a>
	<?php } ?>
	<?php if( !empty( $instance['show_title'] ) ) { ?>
		<h2>
			<a href="<?php echo empty( $source['sourceNameLink'] ) ? get_permalink() : esc_attr( $source[sourceNameLink] ); ?>" target="_blank" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
		</h2>
	<?php } ?>
	<?php if( !empty( $instance['show_content'] ) ) {
		the_excerpt();
        if ( $instance['show_source'] ) {
		    include dirname( __FILE__ ) . '/output-post-meta.php';
        }
	}
?> </div><!--end <?php post_class(); ?> -->
<?php endwhile; endif;

if( !empty( $instance['more_from_category']) && !empty( $instance['posts_cat'] ) && $extra_posts->have_posts() ) { ?> 
<p class="more-from-category">
	<a href="<?php echo get_category_link( $instance['posts_cat'] ); ?>" title="<?php echo get_cat_name( $instance['posts_cat'] ); ?>"><?php echo esc_html( $instance['more_from_category_text'] ); ?></a>
</p></div></div>
<?php
}

if ( !empty( $instance['extra_num'] ) && $extra_posts->have_posts() ) {
		if ( !empty( $instance['extra_title'] ) ) {
			echo $before_title . esc_html( $instance['extra_title'] ) . $after_title;
		}
		if ( $extra_posts->have_posts() ) :
			echo '<ul>';
			while ( $extra_posts->have_posts() ) :
				$extra_posts->the_post(); ?>
				<li>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
				</li>
			<?php endwhile;
			echo '</ul>';
		endif;

}
echo $after_widget;