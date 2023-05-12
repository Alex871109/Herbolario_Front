<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\FrontManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(PaginatorInterface $paginator ,Request $request,FrontManager $front_manager, ParameterBagInterface $params ): Response
    {
        if(isset($_COOKIE['jwt_token'])){
            $token = $_COOKIE['jwt_token'];
            $plantas_relative_url='api/planta/index';
            $herbolarios_relative_url='api/infocomercial/sortedbyplantprice';
            $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],];  
            $plantas_response= $front_manager->petition('GET',$options,$plantas_relative_url); 
            $plantas= json_decode($plantas_response->getBody()->getContents(), true); 
            $herbolarios_response= $front_manager->petition('GET',$options,$herbolarios_relative_url); 
            $herbolarios= json_decode($herbolarios_response->getBody()->getContents(), true);
            $herbolarios=$herbolarios['herbolarios'];
            $pagination = $paginator->paginate(
                $plantas['plantas'], 
                $request->query->getInt('page', 1), 
                2
            );
        
            return $this->render('index/index.html.twig', [
                'plantas' => $plantas,
                'herbolarios'=>$herbolarios,
                'pagination' => $pagination
            ]);
        }
        $this->addFlash('danger','Su sesion ha expirado');
        return $this->redirectToRoute('logging_con_api');
    }
    //create functio
    
}
