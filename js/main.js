// create initial empty chart
var ctx = document.getElementById('myChart').getContext('2d');
                
barData = {
    labels: [],
    datasets: [{
        label: '',
        data: [],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)'
        ],
        borderWidth: 1
    }]
}

var myChart = new Chart(ctx, {
    type: 'line',
    data: barData
});

document.getElementById("myChart").style.display = "none";

var stockNameArr = [];
var stockNameArrObj = {};
var minDate = '';
var maxDate = '';

function upload(){
    $('#overlay').show();
    // Reset the data
    stockNameArr = [];
    stockNameArrObj = {};

    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.csv)$/;
    if (regex.test($("#formFile").val().toLowerCase())) {
        if (typeof (FileReader) != "undefined") {
            var reader = new FileReader();
            reader.onload = function (e) {
                var rows = e.target.result.split("\n");
                for (var i = 0; i < rows.length; i++) {
                    var cells = rows[i].split(",");
                    // console.log(cells);
                    if(cells.length > 1){
                        if(i > 0){
                            if(cells[2] != '' || cells[2] != undefined){
                                var tempStockName = cells[2].toUpperCase();
                            }
                            if(!stockNameArr.includes(tempStockName)){
                                stockNameArr.push(tempStockName);
                                $("#datalistOptions").append('<option value=' + tempStockName +'>');
                            }
                            var temp_obj = {"id": cells[0], "date": cells[1], "price": cells[3]};
                            if((cells[2] in stockNameArrObj)){
                                stockNameArrObj[cells[2]].push(temp_obj);
                            } else {
                                stockNameArrObj[cells[2]] = [temp_obj];
                            }
                        }
                    }
                }
            }
            reader.readAsText($("#formFile")[0].files[0]);
        } else {
            alert("This browser does not support HTML5.");
            $('#formFile').val('');
        }
    } else {
        alert("Please upload a valid CSV file.");
        $('#formFile').val('');
    }
    $('#overlay').hide()
}

function searchStock(stockName){
    stockName = stockName.toUpperCase();
    
    if(!stockNameArr.includes(stockName)){
        alert("Please select stock name from the list !");
        return false;
    } else {
        var dates = stockNameArrObj[stockName].map(function(x) { return new Date(x['date']); })
        var range_end = new Date(Math.max.apply(null,dates));
        var range_start = new Date(Math.min.apply(null,dates));

        $("#startDate").datepicker('option', 'minDate', range_start);
        $("#startDate").datepicker('option', 'maxDate', range_end);
        $("#endDate").datepicker('option', 'minDate', range_start);
        $("#endDate").datepicker('option', 'maxDate', range_end);
    }
}

$("form#formCSVImport").submit(function(e) {
    $('.overlay').show();
    e.preventDefault();
    var formData = new FormData(this);
    var files = $('#formFile')[0].files;
    if(files.length > 0 ){
        formData.append('file',files[0]);
    } else {
        alert("Please provide valid csv file.");
        return false;
    }


    var stockName = $('#stockName').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    if(stockName == '' || stockName == undefined){
        alert("Please provide stock Name.");
        return false;
    } else if(!stockNameArr.includes(stockName)){
        alert("Please select stock name from the list !");
        return false;
    }
    if(startDate == '' || startDate == undefined){
        alert("Please provide start date.");
        return false;
    }
    if(endDate == '' || endDate == undefined){
        alert("Please provides end date.");
        return false;
    }

    if(startDate == endDate){
        alert("Please select valid date range.");
        return false;
    }

    $.ajax({
        url: 'api/calculate.php',
        type: 'POST',
        data: formData,
        success: function (data) {
            res_obj = JSON.parse(data);
            
            if(res_obj.success){
                var deviation_color = (res_obj.data.deviation > 0) ? '#00FF00' : '#FF0000';

                $('#res-div').children().remove();
                var res_div = "<div id='main-res-div'><div id='buy-date'>Purchase Date : <span>"+res_obj.data.buy.date+"</span> </div>"+
                "<div id='buy-price'>Purchase Price : <span> ₹ "+res_obj.data.buy.price+"</span> </div>"+
                "<div id='sell-date'>Selling Date : <span>"+res_obj.data.sell.date+"</span> </div>"+
                "<div id='sell-price'>Selling Price : <span> ₹ "+res_obj.data.sell.price+"</span> </div>"+
                "<div id='devitaion'>Deviation : <span style='color:"+deviation_color+"'> ₹ "+res_obj.data.deviation+"</span> </div>"+
                "<div id='profit'>Profit/Loss : <span style='color:"+deviation_color+"'> ₹ "+res_obj.data.deviation * 200+"</span> </div>"+
                "<div id='mean-price'>Mean Stock Price : <span> ₹ "+res_obj.data.mean_stock_price+"</span> </div></div>";
                $('#res-div').append(res_div);
                
                // add updated data to the chart.
                myChart.data.labels = res_obj.data['stock_date_arr'];
                myChart.data.datasets[0].data = res_obj.data['stock_price_arr'];
                myChart.data.datasets[0].label = stockName+ " Stock Price";
                // re-render the chart
                myChart.update();

                document.getElementById("myChart").style.display = "block";

            } else if(!res_obj.success){
                alert(res_obj.message);
            } else {
                alert("Something went wrong!");
            }
        },
        complete:function(){
            $('.overlay').hide();
        },
        cache: false,
        contentType: false,
        processData: false
    });
});