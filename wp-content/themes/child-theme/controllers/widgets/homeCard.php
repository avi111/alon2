<?php
$translations = $widget->translations();

$image = $widget->getImage();
list( $title, $content ) = array_values( $translations );

$content = stripslashes ( apply_filters( 'the_content', $content ) );
$url     = $widget->getUrl();
