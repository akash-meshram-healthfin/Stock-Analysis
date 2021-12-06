<?php
namespace Utils;

use Utils\Log;
use Utils\Local;

class Stock {
    protected $current_obj_price = 0;
    protected $buy_share_price = 0;
    protected $buy_share_date = 0;
    protected $sell_share_date = 0;
    protected $sell_share_price = 0;
    protected $profit_price = 0;
    protected $first_largest_price = 0;
    protected $first_largest_price_date = 0;
    protected $second_largest_price = 0;
    protected $second_largest_price_date = 0;
    
    protected $processing_arr = [];
    protected $stock_price_arr = [];
    protected $stock_date_arr = [];

    /**
     * @param $stock_data
     * @return array
     * This function takes the array of objects of diff data and price of the particular stock 
     * and generates the analysis of best purchase and selling date with price to maximize profit or minimize loss.
     * It requires sorted stock_data based on date as parameter for calculation.
     */
    public function getAnalysis($stock_data, $input_start_date, $input_end_date){
        log::info($input_start_date);
        try{
            foreach($stock_data as $key => $data_arr){
                $stock_date = str_replace('-','/',$data_arr['date']);

                $input_start_date = str_replace('-','/',$input_start_date);

                $input_end_date = str_replace('-','/',$input_end_date);
                
                if( (strtotime($stock_date) >= strtotime($input_start_date)) && (strtotime($stock_date) <= strtotime($input_end_date)) ){
                    if( empty($data_arr['price']) && $data_arr['price'] != 0){
                        /**
                         * Below logic is for getting the previous date value if price is not present for current date.
                         */
                        for($i=($key-1); $i >= 0; $i--){
                            if(!empty($filtered_stock_name_result_arr[$input_stock_name][$i]['price'])){
                                $this->current_obj_price = $filtered_stock_name_result_arr[$input_stock_name][$key-1]['price'];
                                break;
                            }
                        }
                    }else {
                        $this->current_obj_price = $data_arr['price'];
                    }
                    
                    if($this->current_obj_price > $this->first_largest_price){
                        $this->first_largest_price = $this->current_obj_price;
                        $this->first_largest_price_date = $data_arr['date'];
                    } else {
                        if($this->current_obj_price > $this->second_largest_price){
                            $this->second_largest_price = $this->current_obj_price;
                            $this->second_largest_price_date = $data_arr['date'];						
                        }
                    }
                    
                    if( $this->buy_share_price == 0 || $this->current_obj_price < $this->buy_share_price){
                        $this->buy_share_price = $this->current_obj_price;
                        $this->buy_share_date = $data_arr['date'];
                    } else {
                        
                        $this->sell_share_price = $this->current_obj_price;
                        $this->sell_share_date = $data_arr['date'];
                        
                        if($this->profit_price < ($this->current_obj_price - $this->buy_share_price) ){
                            $this->profit_price = $this->current_obj_price - $this->buy_share_price;
                            $buy_arr['date'] = $this->buy_share_date;
                            $buy_arr['price'] = number_format($this->buy_share_price, 2,'.', '');
                            $sell_arr['date'] = $this->sell_share_date;
                            $sell_arr['price'] = number_format($this->sell_share_price, 2,'.', '');
                            $this->processing_arr['buy'] = $buy_arr;
                            $this->processing_arr['sell'] = $sell_arr;
                        }
                    }
                    
                    array_push($this->stock_price_arr, $this->current_obj_price);
                    array_push($this->stock_date_arr, $data_arr['date']);
            
                }
            }
            
            if(count($this->stock_price_arr) > 0){
                $mean_stock_price = number_format(array_sum($this->stock_price_arr)/count($this->stock_price_arr),2,'.', '');
            
                if(count($this->processing_arr)>0){
                    $deviation = number_format($this->processing_arr['sell']['price'] - $this->processing_arr['buy']['price'],2,'.', '');
                } else {
                    $this->processing_arr['buy'] = ['date' => $this->first_largest_price_date, 'price' => number_format($this->first_largest_price,2,'.', '')];
                    $this->processing_arr['sell'] = ['date' => $this->second_largest_price_date, 'price' => number_format($this->second_largest_price,2,'.', '')];
                    $deviation = number_format($this->processing_arr['sell']['price'] - $this->processing_arr['buy']['price'],2,'.', '');
                }
            
                $this->processing_arr['deviation'] = $deviation;
                $this->processing_arr['mean_stock_price'] = $mean_stock_price;
                $this->processing_arr['stock_date_arr'] = $this->stock_date_arr;
                $this->processing_arr['stock_price_arr'] = $this->stock_price_arr;
            
                return $response_arr = [
                    "success" => true,
                    "message" => "Success",
                    "data" => $this->processing_arr,
                ];
                // return Local::json_output($response_arr);
            } else {
                return $response_arr = [
                    "success" => false,
                    "message" => "No data matched to your search."
                ];
                // return Local::json_output($response_arr);
            }
        } catch (Exception $e){
            return $e->getMessage();
            Log::info($e->getMessage());
            Local::json_output($e->getMessage());
        }
    }
}
?>