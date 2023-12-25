?php

require __DIR__ . "/vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

/**
* Makes an API call to OpenWeatherMap and
* retrieves the weather data of a given city.
*
* @param string $city
*
* @return void
*/
function getWeatherInformation($city)
{
   $apiKey = env("OPEN_WEATHER_MAP_API_KEY");
   $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=$city&units=metric&appid=$apiKey";
   $weather = file_get_contents($weatherUrl);

   $weatherDetails =json_decode($weather, true);


   $temperature = round($weatherDetails["main"]["temp"]);
   $weatherDescription = $weatherDetails["weather"][0]["description"];

   sendFulfillmentResponse($temperature, $weatherDescription);
}

/**
* Send weather data response to Dialogflow
*
* @param integer $temperature
* @param string  $weatherDescription
*
* @return void
*/
function sendFulfillmentResponse($temperature, $weatherDescription)
{
   $response = "It is $temperature degrees with $weatherDescription";

   $fulfillment = array(
       "fulfillmentText" => $response
   );

   echo(json_encode($fulfillment));
}

// listen to the POST request from Dialogflow
$request = file_get_contents("php://input");
$requestJson = json_decode($request, true);

$city = $requestJson['queryResult']['parameters']['geo-city'];

if (isset($city)) {
   getWeatherInformation($city);
}

