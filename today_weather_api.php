<?php 
    require "search.php";
    require "weather.php";

    $defaultCity = "Opelika";
    $serverName = "sql106.epizy.com"; 
    $userName = "epiz_34241103"; 
    $password = "KTVCNx3dsCZ"; 
    $dbname = "epiz_34241103_weather_db"; 
    $conn = mysqli_connect($serverName, $userName, $password, $dbname);
    $val = null;
    $cityName = $_GET['cityName'];
    $timezone = $_GET['timezone'];
    if(empty($cityName)) {
        $cityName = $defaultCity;
    }
    if(empty($timezone)) {
        $timezone = "Asia/Kathmandu";
    }
    $dataSource = "";
    
    $val = getCurrentWeatherDataFromDb($cityName, $timezone);
    
    if ($val == null) {
        $val = getAndSaveCurrentWeatherDataFromApi($cityName, $timezone);
        $dataSource = "Data accessed from internet";

    } else {
        $dataSource = "Data accessed from database";
    }
    $myJson->source = $dataSource;
    $myJson->data = $val;
    $jsonVal = json_encode($myJson);
    echo $jsonVal;
?>