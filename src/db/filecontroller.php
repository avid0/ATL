<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * File Controller Class
 */
namespace ATL\DB;
use \ATL\ATLException;

class FileController {
    /**
     * @var string $path
     */
    public $path;

    /**
     * Constructor
     * 
     * @method __construct
     * @param string $path = '.'
     * @throws \ATL\ATLException
     */
    public function __construct(string $path = '.'){
        if(!is_dir($path)){
            throw new ATLException("Directory do not exists");
        }
        $path = realpath($path);
        $this->path = $path;
    }

    /**
     * @method mkdir
     * @param string $dir
     * @return bool
     */
    public function mkdir(string $dir){
        return mkdir("{$this->path}/$dir");
    }

    /**
     * mkdir if do not exists
     * 
     * @method nmkdir
     * @param string $dir
     * @return bool
     */
    public function nmkdir(string $dir){
        return is_dir("{$this->path}/$dir") ? true : mkdir("{$this->path}/$dir");
    }

    /**
     * @method rmdir
     * @param string $dir
     * @return bool
     */
    public function rmdir(string $dir){
        return rmdir("{$this->path}/$dir");
    }

    /**
     * @method hasdir
     * @param string $dir
     * @return bool
     */
    public function hasdir(string $dir){
        return is_dir("{$this->path}/$dir");
    }

    /**
     * @method hasfile
     * @param string $file
     * @return bool
     */
    public function hasfile(string $file){
        return is_file("{$this->path}/$file");
    }

    /**
     * @method exists
     * @param string $file
     * @return bool
     */
    public function exists(string $file){
        return file_exists("{$this->path}/$file");
    }

    /**
     * @method open
     * @param string $dir
     * @return FileController or false
     */
    public function open(string $dir){
        if(!is_dir("{$this->path}/$dir"))
            return false;
        return new FileController("{$this->path}/$dir");
    }

    /**
     * @method touch
     * @param string $file
     * @return bool
     */
    public function touch(string $file){
        return touch("{$this->path}/$file");
    }

    /**
     * @method rmfile
     * @param string $file
     * @return bool
     */
    public function rmfile(string $file){
        return unlink("{$this->path}/$file");
    }

    /**
     * @method rename
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function rename(string $from, string $to){
        return rename("{$this->path}/$file", "{$this->path}/$to");
    }

    /**
     * @method put
     * @param string $file
     * @param string $contents
     * @return int
     */
    public function put(string $file, string $contents){
        return file_put_contents("{$this->path}/$file", $contents);
    }

    /**
     * @method get
     * @param string $file
     * @return string
     */
    public function get(string $file){
        return file_get_contents("{$this->path}/$file");
    }

    /**
     * Reads entries file into an array
     * 
     * @method gets
     * @param string $file
     * @return string
     */
    public function gets(string $file){
        return file("{$this->path}/$file");
    }

    /**
     * @method append
     * @param string $file
     * @param string $contents
     * @return int
     */
    public function append(string $file, string $contents){
        return file_put_contents("{$this->path}/$file", $contents, FILE_APPEND);
    }

    /**
     * Files array inside of directory
     * 
     * @method files
     * @param string $dir
     * @return array or false
     */
    public function files(string $dir){
        if(!is_dir("{$this->path}/$dir"))
            return false;
        $files = scandir("{$this->path}/$dir");
        if($files[0] == '.')
            array_shift($files);
        if($files[0] == '..')
            array_shift($files);
        return $files;
    }

    /**
     * @method del
     * @param string $file
     * @return bool
     */
    public function del(string $file){
        if(is_file("{$this->path}/$file")){
            return unlink("{$this->path}/$file");
        }
        if(is_dir("{$this->path}/$file")){
            $files = scandir("{$this->path}/$file");
            foreach($files as $entity)
                if($entity == '.' || $entity == '..')continue;
                elseif(is_file("{$this->path}/$file/$entity"))
                    unlink("{$this->path}/$file/$entity");
                else
                    $this->del("$file/$entity");
            return rmdir("{$this->path}/$file");
        }
        return false;
    }

    /**
     * @method copy
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function copy(string $from, string $to){
        if(is_file("{$this->path}/$from")){
            if(is_dir("{$this->path}/$to"))
                return false;
            return copy("{$this->path}/$from", "{$this->path}/$to");
        }
        if(is_dir("{$this->path}/$from")){
            if(is_file("{$this->path}/$to"))
                return false;
            if(!file_exists("{$this->path}/$to"))
                mkdir("{$this->path}/$to");
            $files = scandir("{$this->path}/$from");
            foreach($files as $entity)
                if($entity == '.' || $entity == '..')continue;
                elseif(is_file("{$this->path}/$from/$entity")){
                    if(!is_dir("{$this->path}/$to/$entity"))
                        copy("{$this->path}/$from/$entity", "{$this->path}/$to/$entity");
                }else{
                    if(!is_file("{$this->path}/$to/$entity"))
                        $this->copy("$from/$entity", "$to/$entity");
                }
            return true;
        }
    }

    /**
     * @method map
     * @param string $dir
     * @param callable $callable
     * @return bool
     */
    public function map(string $dir, $callable){
        if(!is_callable($callable)){
            Logger::log("\ATL\DB\FileController::map(): Expects parameter 2 to be callable");
            return false;
        }
        if($handle = opendir("{$this->path}/$dir")) {
            while(($file = readdir($handle)) !== false){
                if($file != '.' && $file != '..' && !is_dir("{$this->path}/$dir/$file"))
                    $callable("$dir/$file");
            }
            return true;
        }else
            return false;
    }
    
    /**
     * @method count
     * @param string $dir
     * @return int or false
     */
    public function count(string $dir){
        $c = 0;
        if($handle = opendir("{$this->path}/$dir")) {
            while(($file = readdir($handle)) !== false){
                if($file != '.' && $file != '..' && !is_dir("{$this->path}/$dir/$file"))
                    ++$c;
            }
            return $c;
        }else
            return false;
    }

    /**
     * @method mapfile
     * @param string $file
     * @param callable $callable
     * @return bool
     */
    public function mapfile(string $file, $callable){
        if(!is_callable($callable)){
            Logger::log("\ATL\DB\FileController::mapfile(): Expects parameter 2 to be callable");
            return false;
        }
        $fs = fopen($file, 'rb');
        if(!$fs)
            return false;
        while($line = fgets($fs)){
            if(substr($line, -2) == "\r\n")
                $line = substr($line, 0, -2);
            else
                $line = substr($line, 0, -1);
            $callable($line);
        }
        fclose($fs);
        return true;
    }
}

?>