<?php

namespace App\Services;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

Class FrontManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }



    public function petition($type,$baseurl,$relativeurl,$token)
    {
        
    $client= new Client(['base_uri' => $baseurl]);
    $response = $client->request($type, $relativeurl, [
    'headers' => ['Authorization' => ['Bearer '.$token, 'Accept'        => 'application/json'], 
    ],]);
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