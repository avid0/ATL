<?php
/**
 * @author Avid
 * 
 * SPLAutoload
 */
namespace ATL;

function autoload($name){
    $name = strtolower($name);
    switch($name){
        case 'atl':
            require_once "atl.php";
            return;
        case 'medoo\medoo':
            require_once "db/medoo.php";
            return;
    }
    $name = explode('\\', $name, 2);
    if($name[0] != 'atl')return;
    $name = '\\'.$name[1];
    if(DIRECTORY_SEPARATOR == '/')
        $name = str_replace('\\', '/', $name);
    $file = __DIR__.$name.'.php';
    if(file_exists($file))
        require_once $file;
}

// register ATL Autoload
spl_autoload_register('ATL\autoload');
?>