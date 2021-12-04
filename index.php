<?php
require __DIR__.'/vendor/autoload.php';
use Utils\Logger;

// Logger::info('Test');

?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stock Analytics</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/styles.css">
    <link href="css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script>
    $( function() {
        $( "#startDate" ).datepicker({
            changeYear: true,
            changeMonth: true
        });
        // $( "#startDate" ).datepicker({
        //     changeYear: true,
        //     changeMonth: true,
        //     minDate: new Date(2020, 00, 01),
        //     maxDate: new Date(),
        // });
        $( "#endDate" ).datepicker({
            changeYear: true,
            changeMonth: true
        });
    } );
    </script>

<script src="js/chart.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
    <div id="overlay">
        <div class='center'>
        <div class="spinner-border" role="status"></div>
        </div>
    </div>
    

    <div class="container main-container">
        <?php
            include'includes/header.php';
        ?>

        
        <div class="col-12 col-sm-12">
            
            <form method="post" name="formCSVImport" id="formCSVImport" enctype="multipart/form-data">
                <div class="row">    
                    <div class="col-md-6">
                        <label for="formFile" class="form-label">Upload csv file</label>
                        <input class="form-control" type="file" name='formFile' id="formFile" onChange="upload()">
                    </div>

                    <div class="col-md-6">
                        <label for="stockName" class="form-label">Enter Stock Name</label>
                        <input class="form-control" list="datalistOptions" name="stockName" id="stockName" placeholder="Type to search..." onChange="searchStock(this.value)">
                        <datalist id="datalistOptions">
                        </datalist>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="formGroupExampleInput" class="form-label">Start Date:</label>
                        <input type="text" class="form-control" name="startDate" id="startDate" placeholder="Example input placeholder">
                    </div>

                    <div class="col-md-6">
                        <label for="formGroupExampleInput2" class="form-label">End Date:</label>
                        <input type="text" class="form-control" name="endDate" id="endDate" placeholder="Another input placeholder">
                    </div>
                </div>

                <div class="row btn-success-custom">
                    <div class="col-md-12">
                        <button type="submit" id="submit" name="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 col-sm-12 ">
            <div class="row">
                <div class="col-sm-3" id="res-div">
                    <!-- <div id='buy-date'>Buy Date <span></span> </div>
                    <div id='buy-price'>Buy Price <span></span> </div>
                    <div id='sell-date'>Sell Date <span></span> </div>
                    <div id='sell-price'>Sell Price <span></span> </div>
                    <div id='devitaion'>Deviation <span></span> </div>
                    <div id='mean-price'>Mean Stock Price <span></span> </div> -->
                </div>
                <div class="col-sm-9">
                <canvas id="myChart" width="100%" height="30%"></canvas>
                </div>
            </div>
        </div>

    </div>

    <script src="js/main.js"></script>
</body>
</html>