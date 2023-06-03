<?php 
    require "search.php";
    require "weather.php";

    $serverName = "sql106.epizy.com"; 
    $userName = "epiz_34241103"; 
    $password = "KTVCNx3dsCZ"; 
    $dbname = "epiz_34241103_weather_db"; 
    $conn = mysqli_connect($serverName, $userName, $password, $dbname);
    $cityId = $_GET['cityId'];
    $timezone = $_GET['timezone'];
    if(empty($cityId)) {
        echo json_encode();
    }
    if(empty($timezone)) {
        $timezone = "Asia/Katmandu";
    }
    $dataSource = "";
    $prevDayRecords = checkIfPreviousDateDataExist($cityId, $timezone);
    $pastRecords = null;
    if ($prevDayRecords == null) {
        $pastRecords = null;
    } else {
        $pastRecords = getWeekWeatherDataFromDb($cityId, $timezone);
        $dataSource = "Data accessed from database";
    }
    if( $pastRecords == null) {
        $serverRecordEntry = fetchAndSaveWeekDataFromApi($cityId);
        if ($serverRecordEntry == true) {
            $pastRecords = getWeekWeatherDataFromDb($cityId, $timezone);
            $dataSource = "Data accessed from internet";
        } else {
            $dataSource = "API fetch failed";
        }
    }
    $myJson->source = $dataSource;
    $myJson->data = $pastRecords;
    $jsonRespone = json_encode($myJson);
    echo $jsonRespone;
?>