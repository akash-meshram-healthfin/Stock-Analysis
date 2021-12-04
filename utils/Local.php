<?php
namespace Utils;

/**
 * This class will be used to log the exception in seperate log file.
 */
class Local {
    private $messages;
    private $failed;

    /**
     * This is a local function to be used for sorting array in asc order based on date sorting only.
     */
    public static function json_output($input_arr) {
        echo json_encode($input_arr);
		return false;
    }
    
    /**
     * This is a local function to be used for sorting array in asc order based on date sorting only.
     */
    public static function date_compare($a, $b) {
        $t1 = strtotime($a['date']);
		$t2 = strtotime($b['date']);
		return $t1 - $t2;
    }
}