<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich
 * Date: 22/11/2017
 * Time: 12:27
 */

namespace engines\twig;


abstract class extend
{
    protected $twig;
    protected $util;
    protected $self;
    protected $namespace;
    
    /**
     * extend constructor.
     */
    public function __construct()
    {
        $this->namespace = $this->set_namespace();
        if ( !$this->namespace ) {
            $this->namespace = $this->self;
        }
        $class = explode( '\\', static::class );
        $this->self = array_pop( $class );
        $this->util = \engines\twig\util::getInstance();
        $this->twig = \engines\twig\twig::getInstance();
    }
    
    abstract public function set_namespace();
    
    static public function load()
    {
        $dir = get_stylesheet_directory() . '\classes\engines\twig';
        foreach ( glob( $dir . '/extensions/*.php' ) as $file ) {
            $info = pathinfo( $file );
            $filename = isset( $info[ 'filename' ] ) ? $info[ 'filename' ] : false;
            $class = sprintf( '\engines\twig\extensions\%s', $filename );
            if ( class_exists( $class ) ) {
                $instance = new $class();
                $instance->add_view()->add_function();
            }
        }
    }
    
    public function add_view()
    {
        $this->twig->add_path( sprintf( '%s/views/%s/', get_stylesheet_directory(), $this->namespace ), $this->namespace );
        
        return $this;
    }
    
    public function add_function()
    {
        $this->util->func( $this->self, array( $this, 'func' ) );
        
        return $this;
    }
    
    abstract public function func();
    
    protected function get_template( $str )
    {
        if ( $str ) {
            $factory = \factory\site::get_instance();
            $design = $factory->getDesign();
            $folder = locate_template( sprintf( 'views/%s/%s', $this->namespace, $design ) );
            if ( $folder ) {
                return sprintf( '@%s/%s/%s.twig', $this->namespace, $design, $str );
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }
}