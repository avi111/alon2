<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <meta http-equiv="Content-Type"
          content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo ( new \head\title() )->html_title; ?></title>

    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php echo get_component( 'header' ); ?>
<div id="page">