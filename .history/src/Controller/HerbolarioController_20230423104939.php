<?php

namespace App\Controller;

use App\Entity\Herbolario;
use App\Repository\HerbolarioRepository;
use App\Services\FrontManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HerbolarioController extends AbstractController
{
    #[Route('/herbolario', name: 'app_herbolario')]
    public function index(FrontManager $frontManager,ParameterBagInterface $params): Response
    {
        
        $token = $_COOKIE['jwt_token'];
        $base_url = $params->get('app_baseurl');
        $herbolarios=$frontManager->petition('GET',$base_url,'api/herbolario/index',$token);
        dump($herbolarios);
        die();
        return $this->render('herbolario/herbolario_index.html.twig', [
            'herbolarios' => $herbolarios,
        ]);
    }

    #[Route('/herbolario/nuevo', name: 'app_nuevo_herbolario')]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $herbolario= new Herbolario();
        if($request->getMethod()==='POST'){
            $nombre=$request->request->get('nombre');
            $url=$request->request->get('url'); 
            $nombre=trim($nombre);
            $url=trim($url);  
                if($nombre!="" && $url!=""){
                    $herbolario->setNombre($nombre);
                    $herbolario->setUrl($url);
                    $entityManager->persist($herbolario);
                    $entityManager->flush();
                    $this->addFlash('success','Herbolario correctamente añadido');
                }
                else
                    $this->addFlash('danger','Los campos no pueden estar en blanco');

            return $this->redirectToRoute('app_herbolario');

        }
        return $this->render('herbolario/herbolario_nuevo.html.twig', [
            'accion'=>false,
        ]);
    }

    #[Route('/herbolario/{id}/editar',name:'app_editar_herbolario')]
    public function editar(Herbolario $herbolario,Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->getMethod()==='POST'){
            $nombre=$request->request->get('nombre');
            $url=$request->request->get('url'); 
            $nombre=trim($nombre);
            $url=trim($url);  
                if($nombre!="" && $url!=""){
                    $herbolario->setNombre($nombre);
                    $herbolario->setUrl($url);

                    $entityManager->flush();
                    $this->addFlash('success','Herbolario correctamente editado');
                }
                else
                    $this->addFlash('danger','Los campos no pueden estar en blanco');

            return $this->redirectToRoute('app_herbolario');
        }
        dump($herbolario);

        return $this->render('herbolario/herbolario_editar.html.twig', [
            'accion'=>true,
            'herbolario'=>$herbolario,
        ]);
    }

    #[Route('/herbolario/{id}/eliminar',name:'app_eliminar_herbolario')]
    public function eliminar(Herbolario $herbolario,Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->getMethod()==='POST'){
            $entityManager->remove($herbolario);
            $entityManager->flush();
            $this->addFlash('success','Herbolario correctamente eliminado');
            return $this->redirectToRoute('app_herbolario');

        }
     
    }


}
