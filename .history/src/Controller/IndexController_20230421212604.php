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
    public function index(EntityManagerInterface $entityMI,PaginatorInterface $paginator ,Request $request,FrontManager $front_manager, ParameterBagInterface $params ): Response
    {
        
        $token = $_COOKIE['jwt_token'];
        $base_url = $params->get('app_baseurl');
        /
        $client_planta = new Client(['base_uri' => 'https://127.0.0.1:8000/']);
        $response_planta = $client_planta->request('GET', 'api/planta/index', [
        'headers' => ['Authorization' => ['Bearer '.$token, 'Accept'        => 'application/json'], 
        ],]);
        
        $plantas_array= json_decode($response_planta->getBody()->getContents(), true);

        $client_herbolario=new Client(['base_uri' => 'https://127.0.0.1:8000/']);
        $response_herbolario=$client_herbolario->request('GET', 'api/infocomercial/sortedbyplantprice', [
            'headers' => ['Authorization' => ['Bearer '.$token, 'Accept'        => 'application/json'], 
            ],]);
        
        $herbolarios_array=json_decode($response_herbolario->getBody()->getContents(), true);
        
        $plantas = $plantas_array['plantas'];
        $herbolarios=$herbolarios_array['herbolarios'];
        
        $pagination = $paginator->paginate(
            $plantas, 
            $request->query->getInt('page', 1), 
            2
        );
       
        return $this->render('index/index.html.twig', [
            'plantas' => $plantas,
            'herbolarios'=>$herbolarios,
            'pagination' => $pagination
        ]);
    }
}
