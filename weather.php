<?php

function getAndSaveCurrentWeatherDataFromApi($cityName, $timezone) {
    $serverName = "sql106.epizy.com"; 
    $userName = "epiz_34241103"; 
    $password = "KTVCNx3dsCZ"; 
    $dbname = "epiz_34241103_weather_db"; 
    $conn = mysqli_connect($serverName, $userName, $password, $dbname);

    if (mysqli_connect_errno()) {
        echo "Failed to connect!";
        return null;
    }
    $city=$cityName;
    $appId="ee71e515251c0596ff8d63184b6d87a9";
    $url = "http://api.openweathermap.org/data/2.5/weather?q=$city&appid=$appId&units=metric";
    if (($response = @file_get_contents($url)) === false) {
        $error = error_get_last();
        return null;
    }

    $data = json_decode($response, true);

    $city = $cityName;
    $cityId = $data['id'];
    $country = $data['sys']['country'];
    $datetime = date('Y-m-d', $data['dt']);
    $forecast = $data['weather'][0]['description'];
    $weatherIcon = $data['weather'][0]['icon'];
    $temperature = $data['main']['temp'];
    $min_temperature = $data['main']['temp_min'];
    $max_temperature = $data['main']['temp_max'];
    $feels_like = $data['main']['feels_like'];
    $humidity = $data['main']['humidity'];
    $wind_speed = $data['wind']['speed'];
    $pressure = $data['main']['pressure'];

    $sql = "INSERT INTO weather_data (city, country, datetime, forecast, temperature, min_temperature, max_temperature, feels_like, humidity, wind_speed, pressure, city_id, weather_icon)
    VALUES ('$city', '$country', '$datetime', '$forecast', '$temperature', '$min_temperature', '$max_temperature', '$feels_like', '$humidity', '$wind_speed', '$pressure', '$cityId', '$weatherIcon')";

    if (mysqli_query($conn, $sql)) {
        return getCurrentWeatherDataFromDb($city, $timezone);
    } else {
        return null;
    }
    mysqli_close($conn);
}

function checkIfDataExists($cityId, $date) {
    $serverName = "sql106.epizy.com"; 
    $userName = "epiz_34241103"; 
    $password = "KTVCNx3dsCZ"; 
    $dbname = "epiz_34241103_weather_db"; 
    $conn = mysqli_connect($serverName, $userName, $password, $dbname);
    
    $sql="SELECT * FROM week_weather_data  where city_id = '{$cityId}' AND datetime = '$date' ORDER BY datetime DESC";

    $res=$conn->query($sql);
    $resultCount = mysqli_num_rows($res);
    if ($resultCount > 0) {
        return true;
    } else {
        return false;
    }
}


function fetchAndSaveWeekDataFromApi($cityId) {
    $serverName = "sql106.epizy.com"; 
    $userName = "epiz_34241103"; 
    $password = "KTVCNx3dsCZ"; 
    $dbname = "epiz_34241103_weather_db"; 
    $conn = mysqli_connect($serverName, $userName, $password, $dbname);

    if (mysqli_connect_errno()) {
        echo "Failed to connect!";
        return null;
    }
    $appId="faf337b47999f8244054ad8ba8ea6159";
    $dateNow = strtotime(date('m/d/Y h:i:s a', time()));
    $dateWeekAgo = ((float) $dateNow) - (float) (604800);

    $url = "https://history.openweathermap.org/data/2.5/history/city?id=$cityId&type=hour&appid=$appId&start=$dateWeekAgo&end=$dateNow";

    if (($response = @file_get_contents($url)) === false) {
        $error = error_get_last();
        return null;
    }

    $datas = json_decode($response, true);

    if (!empty($datas)) {
        foreach($datas['list'] as $data) {
            $cityId = $cityId;
            $datetime = date('Y-m-d', $data['dt']);
            $forecast = $data['weather'][0]['description'];
            $weatherIcon = $data['weather'][0]['icon'];
            $temperature = $data['main']['temp'];
            $min_temperature = $data['main']['temp_min'];
            $max_temperature = $data['main']['temp_max'];
            $feels_like = $data['main']['feels_like'];
            $humidity = $data['main']['humidity'];
            $wind_speed = $data['wind']['speed'];
            $pressure = $data['main']['pressure'];

            $dataExists = checkIfDataExists($cityId, $datetime);

            if($dataExists == true) {
                continue;
            } else {
                // Insert the data into the table
                $sql = "INSERT INTO week_weather_data (datetime, forecast, temperature, min_temperature, max_temperature, feels_like, humidity, wind_speed, pressure, city_id, weather_icon)
                VALUES ('$datetime', '$forecast', '$temperature', '$min_temperature', '$max_temperature', '$feels_like', '$humidity', '$wind_speed', '$pressure', '$cityId', '$weatherIcon')";

                if (mysqli_query($conn, $sql)) {
                    continue;
                } 
            }
                        
            
        }
        return true;
    } else {
        mysqli_close($conn);
        echo "No data received";
        return null;
    }
    
}
?>