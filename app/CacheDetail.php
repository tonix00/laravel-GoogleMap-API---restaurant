<?php

namespace App;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use \GuzzleHttp\Client;

use App\Food;

class CacheDetail extends Model
{
    private $ApiKey;

    public function __construct(){
        $this->ApiKey = config('googleapikey.GoogleAPIKey');
    }

    public function setRestaurants($cacheName,$query){
        $apiURL = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=";
        $apiURL = $apiURL . $query . "&key=";
        $apiURL = $apiURL . $this->ApiKey;
        $this->setCacheDetail($cacheName,$apiURL,'formatted_address', false);
    }

    public function setByRadius($cacheName,$lat,$lng,$radius){
        $apiURL = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=";
        $apiURL = $apiURL . "{$lat},{$lng}&type=restaurant&radius={$radius}" . "&key=";
        $apiURL = $apiURL . $this->ApiKey;
        $this->setCacheDetail($cacheName,$apiURL,'vicinity', false);
    }

    public function setByType($cacheName,$lat,$lng,$keyword){
        $apiURL = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=";
        $apiURL = $apiURL . "{$lat},{$lng}&rankby=distance&type=restaurant&keyword={$keyword}" . "&key=";
        $apiURL = $apiURL . $this->ApiKey;
        $this->setCacheDetail($cacheName,$apiURL,'vicinity', false);
    }

    public function getBySpecific($cacheName,$lat,$lng,$keyword){
        $apiURL = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=";
        $apiURL = $apiURL . "{$lat},{$lng}&rankby=distance&type=restaurant&keyword={$keyword}" . "&key=";
        $apiURL = $apiURL . $this->ApiKey;
        $this->setCacheDetail($cacheName,$apiURL,'vicinity', $keyword);
    }

    private function setCacheDetail($cacheName,$googleAPI,$address, $specificFood){
        // get data from google
        $client = new Client(['verify' => false ]);
        $res = $client->get($googleAPI);
        $content = (string) $res->getBody();
        $infos = json_decode($content,true);

        // cache the data
        $results = $infos['results'];
        foreach($results as $result){
            $cacheDetail = App::make(CacheDetail::class);
            $cacheDetail->formatted_address = $result[$address];
            $cacheDetail->name = $result['name'];
            $cacheDetail->lat = (string)$result['geometry']['location']['lat'];
            $cacheDetail->lng = (string)$result['geometry']['location']['lng'];
            $cacheDetail->cachename = $cacheName;
            $cacheDetail->place_id = $result['place_id'];
            $cacheDetail->save();

            if($specificFood){
                $food = new Food();
                $food->setFood($specificFood,$cacheDetail->place_id);
            }
        }
    }

    public function getCacheData($cacheName)
    {
        // retrieve data and format data
        $caches = $this->where('cachename', $cacheName)->get();

        //format cache
        $toJson['results'] = array();
        foreach($caches as $cache)
        {
            $toJsonDetail = array();
            $toJsonDetail['name'] = $cache->name;
            $toJsonDetail['place_id'] = $cache->place_id;
            $toJsonDetail['formatted_address'] = $cache->formatted_address;
            $toJsonDetail['geometry']['location']['lat'] = (float)$cache->lat;
            $toJsonDetail['geometry']['location']['lng'] = (float)$cache->lng;

            $toJson['results'][] = $toJsonDetail;
        }

        // return json
        return json_encode($toJson);
    }

    public function getCacheDetail($place_id){
        return $this->where('place_id', $place_id)->first();
    }

}