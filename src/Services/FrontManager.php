<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Serializer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

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