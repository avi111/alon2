<?php
/*
Plugin Name: Pure CSS Components
Plugin URI: https://www.felipefialho.com/css-components/index.html
*/


add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('componentes-css', plugins_url('/css-components/assets/css/style.css', __FILE__ ));
});

