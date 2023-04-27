<?php

namespace App\Services;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

Class FrontManager
{
    private $client;


    public function __construct(string  $base_url, ParameterBagInterface $params)
    {
        $base_url = ($params->get('app_baseurl'));$
        this->client = new Client([
            'base_uri' => $base_url,
            'timeout'  => 5.0,
        ]);

    }



    public function petition($type,$options,$relativeurl)
    {
        
   
        return $this->client->request($type, $relativeurl, [$options]);
   
    
    }

}




?>