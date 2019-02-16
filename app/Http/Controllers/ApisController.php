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
    private $specificFood = "";

    public function index()
    {
        return view('map');
    }

    public function getRestaurants()
    {
        $query = 'Restaurants in Cebu';

        $cache = new Cache();
        $cacheName = $cache->createCacheName($query);
        $isCache = $cache->isCache($cacheName);

        $cacheDetail = new CacheDetail();

        if($isCache==false){
            $cache->setCache($cacheName);
            $cacheDetail->setRestaurants($cacheName,$query);
        }

        echo $cacheDetail->getCacheData($cacheName);
    }

    public function getByRadius($lat,$lng,$radius)
    {
        $cache = new Cache();
        $cacheName = $cache->createCacheName($lat.$lng.$radius);
        $isCache = $cache->isCache($cacheName);

        $cacheDetail = new CacheDetail();

        if($isCache==false){
            $cache->setCache($cacheName);
            $cacheDetail->setByRadius($cacheName,$lat,$lng,$radius);
        }
            
        echo $cacheDetail->getCacheData($cacheName);
    }

    public function getByType($lat,$lng,$type)
    {
        $keyword = $type;
        $cache = new Cache();
        $cacheName = $cache->createCacheName($lat.$lng.$keyword);
        $isCache = $cache->isCache($cacheName);

        $cacheDetail = new CacheDetail();

        if($isCache==false){
            $cache->setCache($cacheName);
            $cacheDetail->setByType($cacheName,$lat,$lng,$keyword);
        }
            
        echo $cacheDetail->getCacheData($cacheName);
    }

    public function getBySpecific($lat,$lng)
    {
        $keyword = "";
        if(isset($_GET['keyword']) && !empty($_GET['keyword'])){
            $keyword = $_GET['keyword'];
        }

        $cache = new Cache();
        $cacheName = $cache->createCacheName($lat.$lng.$keyword);
        $isCache = $cache->isCache($cacheName);

        $cacheDetail = new CacheDetail();

        if($isCache==false){
            $cache->setCache($cacheName);
            $cacheDetail->getBySpecific($cacheName,$lat,$lng,$keyword);
        }   
        echo $cacheDetail->getCacheData($cacheName);
    }

    public function getPlaces()
    {
        $places = new Place();
        echo $places-> getPlaces();
    }
}
