<?php
namespace Utils;

/**
 * This class will be used to log the exception in seperate log file.
 */
class Log {
    private $messages;
    private $failed;

    public static function info($exception) {

        if(gettype($exception) == 'array'){
            $exception = json_encode($exception);
        }
        $directory = "../storage/logs/";
        $log_file = $directory. date('d-m-Y', time()). '-' .'log.txt';
        if(!file_exists($log_file)){
            file_put_contents($log_file, '');
        }

        $log_content = file_get_contents($log_file);
        $log_content .= "$exception \n";
        file_put_contents($log_file, $log_content);
    }
}