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
        
        $token = $_COOKIE['jwt_token'];
        // $base_url = $params->get('app_baseurl');
        // $plantas_array= json_decode($front_manager->petition('GET',$base_url,'api/planta/index',$token), true);  
             
        $herbolarios_array=json_decode($front_manager->petition('GET',$base_url,'api/infocomercial/sortedbyplantprice',$token), true);
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
