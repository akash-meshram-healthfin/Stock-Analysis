<?php
namespace Utils;

use Utils\Local;

/**
 * This class will be used to log the exception in seperate log file.
 */
class FileCustom extends Local{
    private $messages;
    private $failed;
    private $filtered_stock_name_result_arr = [];

    public function readCsv($input_file_obj) {
        
        $fileName = $input_file_obj["file"]["tmp_name"];

        if ($input_file_obj["file"]["size"] > 0) {
            $file = fopen($fileName, "r");
	
            $result = [];
            $date_range_arr = [];
            $stock_name_arr = [];
            
            $i = 0;
            
            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($i !=0){
                    $id_no = "";
                    if (isset($column[0])) {
                        $id_no = strip_tags($column[0]);
                    }
                    $date = "";
                    if (isset($column[1])) {
                        $date = strip_tags($column[1]);
                    }
                    $stock_name = "";
                    if (isset($column[2])) {
                        $stock_name = strtoupper(strip_tags($column[2]));
                        
                        if(!in_array($stock_name, $stock_name_arr)){
                            array_push($stock_name_arr, $stock_name);
                        }
                    }
                    $price = "";
                    if (isset($column[3])) {
                        $price = strip_tags($column[3]);
                    }
                    
                    $paramArray = array(
                        'id_no' => $id_no,
                        'date' => $date,
                        'stock_name' => $stock_name,
                        'price' => $price,
                    );
                    
                    $this->filtered_stock_name_result_arr[$stock_name][] = $paramArray;
                    array_push($result, $paramArray);
                }
                $i++;
            }
            return $this->filtered_stock_name_result_arr;   
        } else {
            return $this->filtered_stock_name_result_arr;
        }
    }
}