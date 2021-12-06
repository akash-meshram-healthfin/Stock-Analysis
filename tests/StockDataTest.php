<?php
namespace UnitTest\Test;
use PHPUnit\Framework\TestCase;
use Utils\Stock;

class StockTest extends TestCase {

    private $test_stock_arr = [
        [
          "id_no"=> "21",
          "date"=> "23-02-2020",
          "stock_name"=> "TEST",
          "price"=> ""
        ],
        [
          "id_no"=> "22",
          "date"=> "24-02-2020",
          "stock_name"=> "TEST",
          "price"=> "324"
        ],
        [
          "id_no"=> "23",
          "date"=> "25-02-2020",
          "stock_name"=> "TEST",
          "price"=> "323"
        ]
    ];

    private $test_stock_asc_arr = [
      [
        "id_no"=> "21",
        "date"=> "23-02-2020",
        "stock_name"=> "TEST",
        "price"=> "325"
      ],
      [
        "id_no"=> "22",
        "date"=> "24-02-2020",
        "stock_name"=> "TEST",
        "price"=> "324"
      ],
      [
        "id_no"=> "23",
        "date"=> "25-02-2020",
        "stock_name"=> "TEST",
        "price"=> "323"
      ]
  ];

    /**
     * Below test is for testing against empty price for stock.
     */
    public function test_for_empty_price() {

        $expected_deviation = -1;
        $input_start_date = $this->test_stock_arr[0]['date'];
        $input_end_date = $this->test_stock_arr[count($this->test_stock_arr)-1]['date'];

        $stock = new Stock();
        $result = $stock->getAnalysis($this->test_stock_arr, $input_start_date, $input_end_date);
        $deviation = $result['data']['deviation'];
        $this->assertEquals($expected_deviation, $deviation);
    }

    /**
     * Below test is for testing against stock only in ascending order.
     */
    public function test_for_ascending_price() {

      $expected_deviation = -1;
      $input_start_date = $this->test_stock_asc_arr[0]['date'];
      $input_end_date = $this->test_stock_asc_arr[count($this->test_stock_asc_arr)-1]['date'];

      $stock = new Stock();
      $result = $stock->getAnalysis($this->test_stock_asc_arr, $input_start_date, $input_end_date);
      $deviation = $result['data']['deviation'];
      $this->assertEquals($expected_deviation, $deviation);
    }
}
?>