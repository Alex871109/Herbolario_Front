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

Class Manager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }



    public function object_to_array($entity,$groups)
    {
    $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
    $normalizer = new ObjectNormalizer($classMetadataFactory);
    $serializer = new Serializer([$normalizer]);
    return $serializer->normalize($entity, null, ['groups' => $groups]);
    
    }

    public function array_to_object($array)
    {
    $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
    $normalizer = new ObjectNormalizer($classMetadataFactory);
    $serializer = new Serializer([$normalizer]);
    return $serializer->denormalize($array,$entity,null);
    
    }

}




?>