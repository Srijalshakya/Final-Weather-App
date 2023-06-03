<?php

    function getCurrentWeatherDataFromDb($cityName, $timezone) {
        $serverName = "sql106.epizy.com"; 
        $userName = "epiz_34241103"; 
        $password = "KTVCNx3dsCZ"; 
        $dbname = "epiz_34241103_weather_db"; 
        $conn = mysqli_connect($serverName, $userName, $password, $dbname);
        $searchText = $cityName;
        if(empty($searchText)) {
            $searchText = "Opelika";
        }
        date_default_timezone_set($timezone);
        $date = date('Y-m-d', time());
        $sql="SELECT * FROM weather_data  where city = '{$searchText}' AND date_format(datetime,'%Y-%m-%d') = '$date'";
        $res=$conn->query($sql);
        $resultCount = mysqli_num_rows($res);
        if ($resultCount > 0) {
            $val=$res->fetch_assoc();
            return $val;
        } else {
            return null;
        }

    }

    function checkIfPreviousDateDataExist($cityId, $timezone) {
        $serverName = "sql106.epizy.com"; 
        $userName = "epiz_34241103"; 
        $password = "KTVCNx3dsCZ"; 
        $dbname = "epiz_34241103_weather_db"; 
        $conn = mysqli_connect($serverName, $userName, $password, $dbname);

        date_default_timezone_set($timezone);
        $date = date('Y-m-d', time());
        $dateNow = strtotime(date('m/d/Y h:i:s a', time()));
        $dateDayAgo = ((float) $dateNow) - (float) (604800 / 6);
        $dayAgoDate = date('Y-m-d', $dateDayAgo);
        
        $sql="SELECT * FROM week_weather_data  where city_id = '{$cityId}' AND date_format(datetime,'%Y-%m-%d') < '$date' AND date_format(datetime,'%Y-%m-%d') >= '$dayAgoDate' ORDER BY datetime DESC";

        $res=$conn->query($sql);
        $resultCount = mysqli_num_rows($res);
        if ($resultCount > 0) {
            //Fetching all the rows of the result
            $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
            return $rows;
        } else {
            return null;
        }
    }

    function getWeekWeatherDataFromDb($cityId, $timezone) {
        $serverName = "sql106.epizy.com"; 
        $userName = "epiz_34241103"; 
        $password = "KTVCNx3dsCZ"; 
        $dbname = "epiz_34241103_weather_db"; 
        $conn = mysqli_connect($serverName, $userName, $password, $dbname);
        date_default_timezone_set($timezone);
        $date = date('Y-m-d', time());
        $dateNow = strtotime(date('m/d/Y h:i:s a', time()));
        $dateWeekAgo = ((float) $dateNow) - (float) (604800);
        $weekAgoDate = date('Y-m-d', $dateWeekAgo);
        
        $sql="SELECT * FROM week_weather_data  where city_id = '{$cityId}' AND date_format(datetime,'%Y-%m-%d') < '$date' AND date_format(datetime,'%Y-%m-%d') >= '$weekAgoDate' ORDER BY datetime DESC";

        $res=$conn->query($sql);
        $resultCount = mysqli_num_rows($res);
        if ($resultCount > 0) {
            //Fetching all the rows of the result
            $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
            return $rows;
        } else {
            return null;
        }
    }
    
?>
   