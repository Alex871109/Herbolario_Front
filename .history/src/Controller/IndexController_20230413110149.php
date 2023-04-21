<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(EntityManagerInterface $entityMI,PaginatorInterface $paginator ,Request $request): Response
    {
        
        $cookie = $_COOKIE['jwt_token'];
        $client = new Client(['base_uri' => 'http://127.0.0.1:8000/','verify'=>false]);
        $response = $client->request('GET', 'api/planta/index', [
        'headers' => ['Authorization' => 'Bearer ' . $cookie 
        ],]);
        
        $plantas=
        $herbolarios=[];
        foreach($plantas as $indice=>$planta){
            $info_comercial_array=$infocomercialRepository->findByPlantaid($planta);
            dump($info_comercial_array);
            foreach($info_comercial_array as $infocomercial){
                $precio=$infocomercial->getPrecio();
                $herbolario=$infocomercial->getHerbolarioid();
                $array_ordenar_precio[$precio]=$herbolario;
            }
            ksort( $array_ordenar_precio);
            $herbolarios[$indice]=array_shift($array_ordenar_precio);
        }
        
       
  
        $pagination = $paginator->paginate(
            $plantas, 
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

       
        return $this->render('index/index.html.twig', [
            'plantas' => $plantas,
            'herbolarios'=>$herbolarios,
            'pagination' => $pagination
        ]);
    }
}
