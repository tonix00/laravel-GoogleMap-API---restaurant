<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
    private $googleApiKey;
    private $cacheExpiryDay = 2; // Expire in day 2

    public function __construct(){
        $this->googleApiKey = config('googleapikey.GoogleAPIKey');
    }

    public function createCacheName($name){
        return md5($name);
    }

    public function setCache($name){
        $this->name = $name;
        $this->save();
    }

    public function isCache($name)
    {
        $cache = $this->where('name', $name)->first();
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

}
