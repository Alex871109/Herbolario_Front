<?php

namespace App\Controller;

use App\Entity\Usos;
use App\Repository\UsosRepository;
use App\Services\FrontManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;

class UsosController extends AbstractController
{
    #[Route('/usos', name: 'app_usos')]
    public function index(FrontManager $frontManager, ParameterBagInterface $params): Response
    {
        if(isset($_COOKIE['jwt_token'])){
            $token = $_COOKIE['jwt_token'];
            $relative_url='api/usos/index';
            $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],]; 
            $response=$frontManager->petition('GET',$options,$relative_url);
            $usos=json_decode($response->getBody()->getContents(),true);
            return $this->render('usos/usos_index.html.twig', [
                    'usos'=>$usos['usos'],
            ]);
        }
        $this->addFlash('danger','Su sesion ha expirado');
        return $this->redirectToRoute('logging_con_api');
    }


    #[Route('/usos/nuevo', name: 'app_nuevo_usos')]
    public function nuevo(FrontManager $frontManager, Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            if(isset($_COOKIE['jwt_token'])){
                $nombre = $request->request->get('nombre'); 
                $nombre = trim($nombre);  
                if ($nombre !== '') {
                    $token = $_COOKIE['jwt_token'];
                    $relative_url='api/usos/new';
                    $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],'json' => ['nombre' => $nombre,],];
                    $response=$frontManager->petition('POST',$options,$relative_url);
                    $response_data=json_decode($response->getBody()->getContents(),true);
                    $response_status=$response->getStatusCode();

                    if($response_status===200 && $response_data['status']===200)
                        $this->addFlash('success', 'Uso correctamente añadido');
                
                    else
                        $this->addFlash('danger', 'Error al añadir uso');   

                }else {
                    $this->addFlash('danger', 'El Campo Uso no puede estar en blanco');
                }
                return $this->redirectToRoute('app_usos');
            }

            $this->addFlash('danger','Su sesion ha expirado');
            return $this->redirectToRoute('logging_con_api');
        }
        return $this->render('usos/usos_nuevo.html.twig', [
            'accion' => false,
        ]);
    }


    #[Route('/usos/{id}/editar', name: 'app_usos_editar')]
    public function editar(int $id,Request $request,FrontManager $frontManager): Response
    {
        
        if(isset($_COOKIE['jwt_token'])){
            $token = $_COOKIE['jwt_token'];
            $relative_url='api/usos/edit_get/'.$id;
            if($request->getMethod()==='POST'){
                $nombre=$request->request->get('nombre'); 
                $nombre=u($nombre)->trim();
                if($nombre!=""){
                    $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],'json' => ['nombre' => $nombre,]];
                    $response=$frontManager->petition('POST',$options,$relative_url);
                    if($response->getStatusCode()===200)
                        $this->addFlash('success','Uso correctamente modificado');
                    else
                    $this->addFlash('danger','Error al modificar el uso');
                } 
                else
                    $this->addFlash('danger','El Campo uso no puede estar en blanco');
                    
            return $this->redirectToRoute('app_usos');     

            }
            $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],];
            $response=$frontManager->petition('GET',$options,$relative_url);
            $uso=json_decode($response->getBody()->getContents(),true);  
            return $this->render('usos/usos_editar.html.twig', [
                'uso' => $uso,
                'accion' => true,  // accion editar para que se modifique el boton del template, el del submit
            ]);
        }
        $this->addFlash('danger','Su sesion ha expirado');
        return $this->redirectToRoute('logging_con_api');
    }

    #[Route('/usos/{id}/eliminar', name: 'app_usos_eliminar')]
    public function eliminar(FrontManager $frontManager, Request $request,int $id): Response
    {
        if(isset($_COOKIE['jwt_token'])){   
            if($request->getMethod()==='POST'){
                $token = $_COOKIE['jwt_token'];
                $relative_url='api/usos/delete/'.$id;
                $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],];
                $response=$frontManager->petition('DELETE',$options,$relative_url);
                if($response->getStatusCode() === 200) {
                    $this->addFlash('success', 'Uso eliminado correctamente');
                } else {
                    $this->addFlash('danger', 'Hubo un error al eliminar el uso');
                };
                return $this->redirectToRoute('app_usos');
            }
        }    
        $this->addFlash('danger','Su sesion ha expirado');
        return $this->redirectToRoute('logging_con_api');
    }


}
