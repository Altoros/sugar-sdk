<?php

namespace Altoros\Sugar\Traits;

trait Singleton
{
    
    private static $instance = null;
    
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    private function __construct()
    {
    
    }
    
    private function __clone()
    {
    
    }
    
    private function __wakeup()
    {
    
    }
    
}
