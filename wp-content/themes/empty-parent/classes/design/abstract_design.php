<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich
 * Date: 09/11/2017
 * Time: 10:56
 */

namespace design;


abstract class abstract_design
{
    protected $name;
    
    /**
     * abstract_design constructor.
     * @param $name
     */
    public function __construct()
    {
        $name = explode( '\\', static::class );
        $this->name = $name[ count( $name ) - 1 ];
    }
    
    
    abstract function header();
    
    abstract function footer();
    
    abstract function body();
    
    abstract static public function css();
    
    abstract static public function js();
}