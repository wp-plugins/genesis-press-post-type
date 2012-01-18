<?php

global $post;

echo '<div class="post-meta">';
$source = get_post_meta( $post->ID, 'source', true );
$sourceName = $source['sourceName'];
$sourceLink = $source['sourceNameLink'];
if ( !empty( $sourceName ) ) {
    echo 'Source: ';
    printf(
        empty( $sourceLink ) ? '%1$s ' : '<a href="%2$s" target="_blank">%1$s</a> ',
        $sourceName, esc_url( $sourceLink )
    );
}

$output = '';
for ( $i = 1; $i <= 2; $i++ ) {
    $article = get_post_meta( $post->ID, "article$i", true );
    $articleName = $article["article$i"];
    $articleLink = $article["articleLink$i"];
    if ( !empty( $articleName ) ) {
        $output .= sprintf(
            empty( $articleLink ) ? '%1$s ' : '<a href="%2$s" target="_blank">%1$s</a> ',
            $articleName, esc_url( $articleLink )
        );
    }
}
if ( !empty( $output ) ) {
    echo "Related Articles: $output";
}
echo '</div>';
