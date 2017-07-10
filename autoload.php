<?php

spl_autoload_register('autoload');
        
function autoload( $class_name ){
  
    $filename = __DIR__."/.." . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
    
    if( is_file($filename) ){        
        require($filename);
    }

}
    
  




