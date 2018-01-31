<?php
/**
 * Created by PhpStorm.
 * User: Avi Levkovich
 * Date: 21/06/2017
 * Time: 09:46
 */

namespace filters;

class filters
{
    protected $buffer;
    protected $params;

	/**
	 * ad_buffer constructor.
	 *
	 * @param $array
	 * @param int $params
	 *
	 * @throws \Exception
	 * @internal param $buffer
	 */
    public function __construct( $array,$params=0 )
    {
        if(!is_array($array)){
            throw new \Exception('$array must be an array');
        }

        if($params && !is_numeric($params)){
            throw new \Exception('$params must be an numeric');
        }

        $this->buffer = $array;
        $this->params=$params;
    }

    public function generate_filter( $name )
    {
        foreach ( $this->buffer as $function=>$priority ) {
            if(is_numeric($function)){
                $function=$priority;
                $priority=10;
            }
            
            if(strpos($function,'::')!==false){
            	$function=explode('::',$function);
            	if(isset($function[0]) && isset($function[1])) {
		            $condition = method_exists( $function[ 0 ], $function[ 1 ] );
	            } else {
            		$condition=false;
	            }
            } else {
            	$condition=function_exists($function);
            }

            global $wp_filter;
            $isset=isset($wp_filter[$name]);
            if($condition && !$isset) {
                if ( $this->params ) {
                    add_filter( $name, $function, $priority, $this->params );
                } else {
                    add_filter( $name, $function, $priority );
                }
            }
        }
    }
}