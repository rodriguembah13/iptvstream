<?php


namespace App\Service;


use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EndpointService
{
    private $params;
    /**
     * @var Client
     */
    private $client;
    private $tokencinet;
    private $logger;

    /**
     * EkolopayService constructor.
     * @param LoggerInterface $logger
     * @param ParameterBagInterface $params
     */
    public function __construct(LoggerInterface $logger, ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->logger = $logger;
        $this->client = new Client([
            'base_uri' => $params->get('API_URL'),
        ]);
    }
    function getLiveStreamCategory(){
        $endpoint =$this->params->get('API_URL')."/player_api.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),
                'action'=>'get_live_categories',

            ]]);
        $valresp = json_decode($res->getBody(), true);
        $response = $valresp;
        return $response;
    }
    function getLiveStreambyCategory($category){
        $endpoint =$this->params->get('API_URL')."/player_api.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),
                'action'=>'get_live_streams',
                'category_id'=>$category

            ]]);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getLiveStreams(){
        $endpoint =$this->params->get('API_URL')."/player_api.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),
                'action'=>'get_live_streams',

            ]]);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getVodStreamCategory(){
        $endpoint ="/player_api.php?username=".$this->params->get('API_USERNAME').
            "&password=".$this->params->get('API_USERNAME')."&action=get_vod_categories";
        $res = $this->client->get($endpoint);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getVodStreambyCategory($category){
        $endpoint ="/player_api.php?username=".$this->params->get('API_USERNAME').
            "&password=".$this->params->get('API_USERNAME')."&action=get_vod_streams&category_id=".$category;
        $res = $this->client->get($endpoint);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getVodStreamInfoByID($id){
        $endpoint ="/player_api.php?username=".$this->params->get('API_USERNAME').
            "&password=".$this->params->get('API_USERNAME')."&action=get_vod_info&vod_id=".$id;
        $res = $this->client->get($endpoint);
        $valresp = json_decode($res->getBody(), true);
        $response = $valresp['response'];
        return $response;
    }
    function getSeriesCategory(){
        $endpoint =$this->params->get('API_URL')."/player_api.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),
                'action'=>'get_series_categories',

            ]]);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getSeriebyCategory($category){
        $endpoint =$this->params->get('API_URL')."/player_api.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),
                'action'=>'get_series',
                'category_id'=>$category

            ]]);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getSeries(){
        $endpoint =$this->params->get('API_URL')."/player_api.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),
                'action'=>'get_series',

            ]]);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getSerieStreamInfoByID($id){
        $endpoint =$this->params->get('API_URL')."/player_api.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),
                'action'=>'get_series_info',
                'series_id'=>$id

            ]]);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getShortEPGBystreamID($id){
        $endpoint =$this->params->get('API_URL')."/player_api.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),
                'action'=>'get_short_epg',
                'stream_id'=>$id

            ]]);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getFullEPG(){
        $endpoint =$this->params->get('API_URL')."/xmltv.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),

            ]]);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
    function getAllLists(){
        $endpoint =$this->params->get('API_URL')."/get.php";
        $res = $this->client->get($endpoint,
            ['query' => [
                'username'=>$this->params->get('API_USERNAME'),
                'password'=>$this->params->get('API_USERNAME'),
                'type'=>'m3u_plus',
                'output'=>'m3u8',

            ]]);
        $valresp = json_decode($res->getBody(), true);
        return $valresp;
    }
}
