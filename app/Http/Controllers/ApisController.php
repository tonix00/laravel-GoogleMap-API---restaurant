<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \GuzzleHttp\Client;
use App\Cache;
use App\CacheDetail;
use App\Place;
use App\Http\Controllers\FoodsController;

class ApisController extends Controller
{
    private $cacheExpiryDay = 2;
    private $googleApiKey = "AIzaSyAp9NsRWvUE_XNCVXQYqYDaWrOA_A9ldLs";
    private $specificFood = "";

    public function index()
    {
        return view('map');
    }

    public function getRestaurants()
    {
        // create ID
        $query = 'Restaurants in Cebu';

        // google api
        $googleAPI = "https://maps.googleapis.com/maps/api/place/textsearch/json?query={$query}&key={$this->googleApiKey}"; 
        $cacheName = md5($query);
        $isCache = $this->isCache($cacheName);
 
        if($isCache==false)
            echo $this->setCacheData($cacheName,$googleAPI,'formatted_address');
        else
            echo $this->getCacheData($cacheName);
    }

    public function getByRadius($lat,$lng,$radius)
    {
        $googleAPI ="https://maps.googleapis.com/maps/api/place/nearbysearch/json?location={$lat},{$lng}&radius={$radius}&type=restaurant&key={$this->googleApiKey}"; 
        $cacheName = md5($lat.$lng.$radius);
        $isCache = $this->isCache($cacheName);

        if($isCache==false)
            echo $this->setCacheData($cacheName,$googleAPI,'vicinity');
        else
            echo $this->getCacheData($cacheName);
    }

    public function getByType($lat,$lng,$type)
    {
        $keyword = $type;
        $googleAPI ="https://maps.googleapis.com/maps/api/place/nearbysearch/json?location={$lat},{$lng}&rankby=distance&keyword={$keyword}&type=restaurant&key={$this->googleApiKey}"; 
        $cacheName = md5($lat.$lng.$keyword);
        $isCache = $this->isCache($cacheName);

        if($isCache==false)
            echo $this->setCacheData($cacheName,$googleAPI,'vicinity');
        else
            echo $this->getCacheData($cacheName);
    }

    public function getBySpecific($lat,$lng)
    {
        $keyword = "";
        if(isset($_GET['keyword']) && !empty($_GET['keyword'])){
            $keyword = $_GET['keyword'];
            $this->specificFood = $_GET['keyword'];
        }

        $googleAPI ="https://maps.googleapis.com/maps/api/place/nearbysearch/json?location={$lat},{$lng}&rankby=distance&keyword={$keyword}&type=restaurant&key={$this->googleApiKey}"; 
        $cacheName = md5($lat.$lng.$keyword);
        $isCache = $this->isCache($cacheName);

        if($isCache==false)
            echo $this->setCacheData($cacheName,$googleAPI,'vicinity');
        else
            echo $this->getCacheData($cacheName);
    }

    public function getPlaces()
    {
        // retrieve data and format data
        $places = Place::all();

        //format cache
        $toJson = array();
        foreach($places as $place)
        {
            $arr = array();
            $arr["formatted_address"]= $place->formatted_address;
            $arr["lat"]= $place->lat;
            $arr["lng"]= $place->lng;
            $arr["id"]= $place->id;
            $toJson[] = $arr;
        }

        // return json
        echo json_encode($toJson);
    }

    private function isCache($name)
    {
        $cache = Cache::where('name', $name)->first();
        $expireTime = 0;

        if($cache){
            $date = date_create($cache->creatded_at);
            date_add($date,date_interval_create_from_date_string("{$this->cacheExpiryDay} day"));

            $current = date("Y-m-d H:i:s.u", time());
            $date2 = new \DateTime($current);
            $interval = $date->diff($date2);
            $expireTime = $interval->d;
        }

        if($expireTime)
            return true;
        else
            return false;
    }

    private function getCacheData($cacheName)
    {
        // retrieve data and format data
        $caches = CacheDetail::where('cachename', $cacheName)->get();

        //format cache
        $toJson['results'] = array();
        foreach($caches as $cache)
        {
            $toJsonDetail = array();
            $toJsonDetail['name'] = $cache->name;
            $toJsonDetail['formatted_address'] = $cache->formatted_address;
            $toJsonDetail['geometry']['location']['lat'] = (float)$cache->lat;
            $toJsonDetail['geometry']['location']['lng'] = (float)$cache->lng;

            $toJson['results'][] = $toJsonDetail;
        }

        // return json
        return json_encode($toJson);
    }

    private function setCacheData($cacheName,$googleAPI,$address)
    {
        $cache = new Cache();
        $cache->name = $cacheName;
        $cache->save();

        // set food service
        $foodService = new FoodsController();

        // get data from google
        $client = new Client(['verify' => false ]);
        $res = $client->get($googleAPI);
        $content = (string) $res->getBody();
        $infos = json_decode($content,true);

        $toJson['results'] = array();

        // cache the data
        $results = $infos['results'];
        foreach($results as $result){
            $cacheDetail = new CacheDetail();
            $cacheDetail->formatted_address = $result[$address];
            $cacheDetail->name = $result['name'];
            $cacheDetail->lat = (string)$result['geometry']['location']['lat'];
            $cacheDetail->lng = (string)$result['geometry']['location']['lng'];
            $cacheDetail->cachename = $cacheName;
            $cacheDetail->save();
            

            $toJsonDetail = array();
            $toJsonDetail['name'] = $result['name'];
            $toJsonDetail['formatted_address'] = $result[$address];
            $toJsonDetail['geometry']['location']['lat'] = (float)$result['geometry']['location']['lat'];
            $toJsonDetail['geometry']['location']['lng'] = (float)$result['geometry']['location']['lng'];

            $toJson['results'][] = $toJsonDetail;

            if($this->specificFood && $address=='vicinity'){
                $foodService->f = $this->specificFood;
                $foodService->n = $result['name'];
                $foodService->saveFood($cacheDetail->lat,$cacheDetail->lng); 
            }

            unset($cacheDetail);
        }
        // return json
        return json_encode($toJson);
    }
}
