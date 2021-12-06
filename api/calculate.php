<?php
require '../vendor/autoload.php';
use Utils\Log;
use Utils\Local;
use Utils\FileCustom;
use Utils\Stock;

try{
    if (isset($_FILES["file"]) && $_FILES["file"]["size"] > 0) {
        /**
         * Send file data to get all the stock data.
         */
        $file_obj = new FileCustom();
        $filtered_stock_name_result_arr = $file_obj->readCsv($_FILES);
        
        if(count($filtered_stock_name_result_arr) == 0 ){
            $response_arr = [
                "success" => false,
                "message" => "Cannot process empty file."
            ];
            return Local::json_output($response_arr);
        }
    } else {
        $response_arr = [
            "success" => false,
            "message" => "Cannot process empty file."
        ];
        return Local::json_output($response_arr);
    }
    
    if (isset($_POST["stockName"]) && $_POST["stockName"] != '') {
        $input_stock_name = strip_tags($_POST["stockName"]);
    } else {
        $response_arr = [
            "success" => false,
            "message" => "Please provide valid stock name."
        ];
        return Local::json_output($response_arr);
    }
    
    if (isset($_POST["startDate"]) && $_POST["startDate"] != '') {
        $input_start_date = strip_tags($_POST["startDate"]);
    } else {
        $response_arr = [
            "success" => false,
            "message" => "Please provide start date."
        ];
        return Local::json_output($response_arr);
    }
    
    if (isset($_POST["endDate"]) && $_POST["endDate"] != '') {
        $input_end_date = strip_tags($_POST["endDate"]);
    } else {
        $response_arr = [
            "success" => false,
            "message" => "Please provide end date."
        ];
        return Local::json_output($response_arr);
    }
    
    if(strtotime($input_start_date) == strtotime($input_end_date)){
        $response_arr = [
            "success" => false,
            "message" => "Please select valid range."
        ];
        return Local::json_output($response_arr);
    }
    
    /**
     * Create local class object to be called for accessing its methods.  
     */ 
    $local = new Local();
    
    usort($filtered_stock_name_result_arr[$input_stock_name], array($local, 'date_compare'));

    log::info($filtered_stock_name_result_arr);

    /**
     * Send filtered data to stock calculation.
     */
    $stock = new Stock();
    $result = $stock->getAnalysis($filtered_stock_name_result_arr[$input_stock_name], $input_start_date, $input_end_date);
    
    return Local::json_output($result);
} catch(Exception $e){
    Log::info($e->getMessage());
    Local::json_output($e->getMessage());
}

?>