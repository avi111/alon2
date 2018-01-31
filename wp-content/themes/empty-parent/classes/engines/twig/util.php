<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich
 * Date: 21/11/2017
 * Time: 18:09
 */

namespace engines\twig;


class util
{
    protected $twig;
    static protected $instance;
    
    /**
     * filters constructor.
     */
    public function __construct()
    {
        $this->twig=twig::getInstance();
    }
    
    static public function getInstance(){
        if(!self::$instance){
            self::$instance=new static();
        }
        
        return self::$instance;
    }
    
    public function getEnv(){
        return $this->twig->getEnv();
    }
    
    public function filter($name,$function){
        $env=$this->getEnv();
        $filter=new \Twig_SimpleFilter($name,$function);
        $env->addFilter($filter);
    }
    
    public function func($name,$function){
        $env=$this->getEnv();
        $filter=new \Twig_SimpleFunction($name,$function);
        $env->addFunction($filter);
    }
}