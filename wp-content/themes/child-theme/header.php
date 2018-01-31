<?php
/**
 * @package WordPress
 * @subpackage Theme_Compat
 * @deprecated 3.0.0
 *
 * This file is here for backward compatibility with old themes and will be removed in a future version.
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <meta http-equiv="Content-Type"
          content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo wp_get_document_title(); ?></title>

    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>

	<?php if ( file_exists( get_stylesheet_directory() . '/images/kubrickbgwide.jpg' ) ) { ?>
        <style type="text/css" media="screen">

            <?php
			// Checks to see whether it needs a sidebar
			if ( empty($withcomments) && !is_single() ) {
			?>
            #page {
                background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbg-<?php bloginfo('text_direction'); ?>.jpg") repeat-y top;
                border: none;
            }

            <?php } else { // No sidebar ?>
            #page {
                background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbgwide.jpg") repeat-y top;
                border: none;
            }

            <?php } ?>

        </style>
	<?php } ?>

	<?php if ( is_singular() ) {
		wp_enqueue_script( 'comment-reply' );
	} ?>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="page">
    <header id="header" role="banner">
        <header class="sticky">
            <div class="container">
                <div class="row">
                    <div class="col-sm-4">
						<?php echo get_component( 'logo' ); ?>
                    </div>
                    <div class="col-sm-8">
                        <div class="block">
							<?php echo get_component( 'header_contact_details' ); ?>
							<?php echo get_component( 'language_selector' ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </header>
