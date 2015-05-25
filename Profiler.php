<?php

class Profiler
{

    private $_data_array = array();

    function __construct()
    {
        register_tick_function( array( $this, "tick" ) );
        declare(ticks = 1);
    }

    function __destruct()
    {
        unregister_tick_function( array( $this, "tick" ) );
    }

    function tick()
    {
        $this->_data_array[] = array(
            "memory" => memory_get_usage(),
            "time" => microtime( TRUE ),
            //if you need a backtrace you can uncomment this next line
            //"backtrace" => debug_backtrace( FALSE ),
        );
    }

    function getDataArray()
    {
        return $this->_data_array;
    }
}