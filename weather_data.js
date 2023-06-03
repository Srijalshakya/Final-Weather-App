//current state
let currCity = "Opelika";
let units = "metric";

// Identifiers
let city = document.querySelector(".weather__city");
let datetime = document.querySelector(".weather__datetime");
let weather__forecast = document.querySelector('.weather__forecast');
let weather__temperature = document.querySelector(".weather__temperature");
let weather__icon = document.querySelector(".weather__icon");
let weather__minmax = document.querySelector(".weather__minmax")
let weather__realfeel = document.querySelector('.weather__realfeel');
let weather__humidity = document.querySelector('.weather__humidity');
let weather__wind = document.querySelector('.weather__wind');
let weather__pressure = document.querySelector('.weather__pressure');
let weather_past_data_table = document.querySelector('.past_weather_table');

// Input
document.querySelector(".weather__search").addEventListener('submit', e => {
    let search = document.querySelector(".weather__searchform");
    // Stop default action
    e.preventDefault();
    // Update current city
    currCity = search.value;
    // retrive weather forecast 
    searchCityWeather(currCity);
})

function convertTimeStamp(dateString){
    const timezone = new Date().getTimezoneOffset();
    const convertTimezone = timezone / 60; // simplify seconds to hours 
    const date = new Date(dateString);

    const options = {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
        timeZone: `${Intl.DateTimeFormat().resolvedOptions().timeZone}`,
        hour12: true,
    }
    return date.toLocaleDateString("en-US", options)
   
}

// change country code to name
function convertCountryCode(country){
    let regionNames = new Intl.DisplayNames(["en"], {type: "region"});
    return regionNames.of(country)
}

// searching city name
function searchCityWeather(cityName) {
    // localStorage.clear();
    let searchBar = document.querySelector(".weather__searchform");
    if(!getLocalStorageWeatherData(cityName)){
        getWeatherData(cityName)
    }
    searchBar.value = ""
}

// fetch weather info from server
function getWeatherData(cityName){
    // Fetch today weather data from server
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    fetch(`http://srijalweatherapp.infinityfreeapp.com/today_weather_api.php?cityName=${cityName}&timezone=${timezone}`)
    .then(res => res.json())
    .then((json) =>{
        console.log(json.source);
        const currentData = json.data;
        if (currentData == null) {
            alert(`Weather information not found for ${cityName}`);
            return
        }
        let id = currentData.city_id;
        setTodayData(currentData);

        // Fetch Past Weather Data
        fetch(`http://srijalweatherapp.infinityfreeapp.com/past_weather_data_api.php?cityId=${id}&$timezone=${timezone}`)
        .then(res => res.json())
        .then((data)=>{
            console.log(data.source);
            const lastWeekData = data.data;
            let toStoreData = JSON.stringify([currentData, ...lastWeekData]);
            localStorage.setItem(cityName, toStoreData);
            setLastWeekData(lastWeekData);
        })
        .catch(err => alert(`Week records of ${cityName} not found`))
    })
    .catch(error => {
        console.log(error);
        alert(`Unable to fetch weather data for ${cityName}`)
    })
}

function getLocalStorageWeatherData(name){
    if(localStorage.getItem(name) == null){
        return false
    }
    else{
        let time = new Date()
        let day = time.getDate()
        let month = time.getMonth()+1
        let year = time.getFullYear()
        let fullDate = [year,("0" + month).slice(-2), ("0" + day).slice(-2)].join('-')
        data = JSON.parse(localStorage.getItem(name))
        currentWeatherData = data[0]
        if(currentWeatherData.datetime == fullDate){
            console.log("Data accessed from local storage")
            data.shift()
            setTodayData(currentWeatherData)
            setLastWeekData(data)
            return true
        }
        return false
    }   
}

function setTodayData(data){
    //Displaying in the dom 
    city.innerHTML = `${data.city}, ${convertCountryCode(data.country)}`;
    datetime.innerHTML = convertTimeStamp(data.datetime); 
    weather__forecast.innerHTML = `<p>${data.forecast}`;
    weather__temperature.innerHTML = `${data.temperature}&#176`;
    weather__icon.innerHTML = `   <img src="http://openweathermap.org/img/wn/${data.weather_icon}@4x.png" />`;
    weather__minmax.innerHTML = `<p>Min: ${data.min_temperature}&#176</p><p>Max: ${data.max_temperature}&#176</p>`;
    weather__realfeel.innerHTML = `${data.feels_like}&#176`;
    weather__humidity.innerHTML = `${data.humidity}%`;
    weather__wind.innerHTML = `${data.wind_speed} ${units === "imperial" ? "mph": "m/s"}`;
    weather__pressure.innerHTML = `${data.pressure} hPa`;
}

function setLastWeekData(data) {
    //Reset Table UI
    weather_past_data_table.innerHTML = "";
    //Create table header
                
    let header = `<tr>
                  <th>Date</th>
                  <th>Weather</th>
                  <th>Temperature</th>
                  <th>Min Temp</th>
                  <th>Max Temp</th>
                  <th>Pressure</th>
                  <th>Humidity</th>
                  <th>Wind Speed</th>
                </tr>`;
    weather_past_data_table.innerHTML += header;

    //loop through all data
    data.forEach(row => {
        let dataRow = `
                        <tr>
                            <td>${convertTimeStamp(row.datetime)}</td>
                            <td>${row.forecast}</td>
                            <td>${row.temperature}</td>
                            <td>${row.min_temperature}</td>
                            <td>${row.max_temperature}</td>
                            <td>${row.pressure}</td>
                            <td>${row.humidity}</td>
                            <td>${row.wind_speed}</td>
                        </tr>
                        `;
        weather_past_data_table.innerHTML += dataRow;
    });
}

document.body.addEventListener('load', searchCityWeather(currCity))