<?php
require '../vendor/autoload.php';
use Utils\Log;
use Utils\Local;
use Utils\FileCustom;

$response_arr = [
	"success" => false,
	"message" => ""
];

try{
    if (isset($_FILES["file"]) && $_FILES["file"]["size"] > 0) {
        $filtered_stock_name_result_arr = FileCustom::readCsv($_FILES);
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
        $input_start = strip_tags($_POST["startDate"]);
    } else {
        $response_arr = [
            "success" => false,
            "message" => "Please provide start date."
        ];
        return Local::json_output($response_arr);
    }
    
    if (isset($_POST["endDate"]) && $_POST["endDate"] != '') {
        $input_end = strip_tags($_POST["endDate"]);
    } else {
        $response_arr = [
            "success" => false,
            "message" => "Please provide end date."
        ];
        return Local::json_output($response_arr);
    }
    
    if(strtotime($input_start) == strtotime($input_end)){
        $response_arr = [
            "success" => false,
            "message" => "Please select valid range."
        ];
        return Local::json_output($response_arr);
    }
    
    //Logic For getting the final Statistics for selected range.
    $current_obj_price = 0;
    $buy_share_price = 0;
    $buy_share_date = 0;
    $sell_share_date = 0;
    $sell_share_price = 0;
    $profit_price = 0;
    $first_largest_price = 0;
    $first_largest_price_date = 0;
    $second_largest_price = 0;
    $secode_largest_price_date = 0;
    
    $processing_arr = [];
    $stock_price_arr = [];
    $stock_date_arr = [];
    
    // Create local class object to be called for accessing its methods.
    $local = new Local();
    
    usort($filtered_stock_name_result_arr[$input_stock_name], array($local, 'date_compare'));
    
    foreach($filtered_stock_name_result_arr[$input_stock_name] as $key => $data_arr){
        if( (strtotime($data_arr['date']) >= strtotime($input_start)) && (strtotime($data_arr['date']) <= strtotime($input_end)) ){
            if( empty($data_arr['price']) && $data_arr['price'] != 0){
                for($i=($key-1); $i >= 0; $i--){
                    if(!empty($filtered_stock_name_result_arr[$input_stock_name][$i]['price'])){
                        $current_obj_price = $filtered_stock_name_result_arr[$input_stock_name][$key-1]['price'];
                        break;
                    }
                }
            }else {
                $current_obj_price = $data_arr['price'];
            }
            
            if($current_obj_price > $first_largest_price){
                $first_largest_price = $current_obj_price;
                $first_largest_price_date = $data_arr['date'];
            } else {
                if($current_obj_price > $second_largest_price){
                    $second_largest_price = $current_obj_price;
                    $secode_largest_price_date = $data_arr['date'];						
                }
            }
            
            if( $buy_share_price == 0 || $current_obj_price < $buy_share_price){
                $buy_share_price = $current_obj_price;
                $buy_share_date = $data_arr['date'];
            } else {
                
                $sell_share_price = $current_obj_price;
                $sell_share_date = $data_arr['date'];
                
                if($profit_price < ($current_obj_price - $buy_share_price) ){
                    $profit_price = $current_obj_price - $buy_share_price;
                    //$buy_arr = [$buy_share_date, $buy_share_price];
                    $buy_arr['date'] = $buy_share_date;
                    $buy_arr['price'] = number_format($buy_share_price, 2);
                    //$sell_arr = [$sell_share_date, $sell_share_price];
                    $sell_arr['date'] = $sell_share_date;
                    $sell_arr['price'] = number_format($sell_share_price, 2);
                    $processing_arr['buy'] = $buy_arr;
                    $processing_arr['sell'] = $sell_arr;
                }
            }
            
            array_push($stock_price_arr, $current_obj_price);
            array_push($stock_date_arr, $data_arr['date']);
    
        }
    }
    
    if(count($stock_price_arr) > 0){
        $mean_stock_price = number_format(array_sum($stock_price_arr)/count($stock_price_arr),2);
    
        if(count($processing_arr)>0){
            $deviation = number_format($processing_arr['sell']['price'] - $processing_arr['buy']['price'],2);
        } else {
            $processing_arr['buy'] = ['date' => $first_largest_price_date, 'price' => number_format($first_largest_price,2)];
            $processing_arr['sell'] = ['date' => $secode_largest_price_date, 'price' => number_format($second_largest_price,2)];
            $deviation = number_format($processing_arr['sell']['price'] - $processing_arr['buy']['price'],2);
        }
    
        $processing_arr['deviation'] = $deviation;
        $processing_arr['mean_stock_price'] = $mean_stock_price;
        $processing_arr['stock_date_arr'] = $stock_date_arr;
        $processing_arr['stock_price_arr'] = $stock_price_arr;
    
        $response_arr = [
            "success" => true,
            "message" => "Success",
            "data" => $processing_arr,
        ];
        return Local::json_output($response_arr);
    } else {
        $response_arr = [
            "success" => false,
            "message" => "No data matched to your search."
        ];
        return Local::json_output($response_arr);
    }
} catch(Exception $e){
    Log::info($e->getMessage());
    Local::json_output($e->getMessage());
}

?>