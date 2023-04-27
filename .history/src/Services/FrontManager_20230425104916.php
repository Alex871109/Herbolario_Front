<?php

namespace App\Services;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

Class FrontManager
{
    private $client;

    public function __construct(private readonly string  $base_url)
    {
        $this->client = new Client([
            'base_uri' => $base_url,
            'timeout'  => 5.0,
        ]);

    }



    public function petition($type,$options,$relativeurl)
    {
        
   
    $response = $this->client->request($type, $relativeurl, [$options]);
    return $response->getBody()->getContents();
    
    }

    public function array_to_object($array,$entity)
    {
    $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
    $normalizer = new ObjectNormalizer($classMetadataFactory);
    $serializer = new Serializer([$normalizer]);
    return $serializer->denormalize($array,$entity::class,null);
    
    }

}




?>