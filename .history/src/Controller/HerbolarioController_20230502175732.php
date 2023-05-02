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
use Symfony\Component\Serializer\Encoder\JsonDecode;

class HerbolarioController extends AbstractController
{
    #[Route('/herbolario', name: 'app_herbolario')]
    public function index(FrontManager $frontManager,ParameterBagInterface $params): Response
    {
        if(isset($_COOKIE['jwt_token'])){
            $token = $_COOKIE['jwt_token'];
            $relative_url='api/herbolario/index';
            $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],]; 
            $response=$frontManager->petition('GET',$options,$relative_url);
            $herbolarios=json_decode($response->getBody()->getContents(),true);
            return $this->render('herbolario/herbolario_index.html.twig', [
                'herbolarios' => $herbolarios['herbolarios'],
            ]);
        }
        $this->addFlash('danger','Su sesion ha expirado');
        return $this->redirectToRoute('logging_con_api');
    }

    #[Route('/herbolario/nuevo', name: 'app_nuevo_herbolario')]
    public function nuevo(Request $request, FrontManager $frontManager): Response
    {
        if ($request->getMethod() === 'POST') {
            if(isset($_COOKIE['jwt_token'])){
                $nombre = $request->request->get('nombre'); 
                $url=$request->request->get('url'); 
                $nombre = trim($nombre); 
                $url=trim($url); 
                if ($nombre !== ''&& $url!="") {
                    $token = $_COOKIE['jwt_token'];
                    $relative_url='api/herbolario/new';
                    $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],'json' => ['nombre' => $nombre,'url' => $url],];
                    $response=$frontManager->petition('POST',$options,$relative_url);
                    $response_data=json_decode($response->getBody()->getContents(),true);
                    $response_status=$response->getStatusCode();

                    if($response_status===200 && $response_data['status']===200)
                        $this->addFlash('success', 'Herbolario correctamente añadido');
                
                    else
                        $this->addFlash('danger', 'Error al añadir herbolario');   

                }else {
                    $this->addFlash('danger', 'Los campos Uso y URL no pueden estar en blanco');
                }
                return $this->redirectToRoute('app_herbolario');
            }

            $this->addFlash('danger','Su sesion ha expirado');
            return $this->redirectToRoute('logging_con_api');
        }

        return $this->render('herbolario/herbolario_nuevo.html.twig', [
            'accion'=>false,
        ]);
    }

    #[Route('/herbolario/{id}/editar',name:'app_editar_herbolario')]
    public function editar(int  $id,Request $request,FrontManager $frontManager): Response
    {
        if(isset($_COOKIE['jwt_token'])){
            $token = $_COOKIE['jwt_token'];
            $relative_url='api/herbolario/edit_get/'.$id;
            if($request->getMethod()==='POST'){
                $nombre=$request->request->get('nombre');
                $url=$request->request->get('url'); 
                $nombre=trim($nombre);
                $url=trim($url);  
                    if($nombre!="" && $url!=""){
                        $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],'json' => ['nombre' => $nombre, 'url'=>$url]];
                        $response=$frontManager->petition('POST',$options,$relative_url);
                        if($response->getStatusCode()===200)
                            $this->addFlash('success','Herbolario correctamente modificado');
                        else
                        $this->addFlash('danger','Error al modificar el Herbolario');
                    }
                    else
                        $this->addFlash('danger','Los campos no pueden estar en blanco');

                return $this->redirectToRoute('app_herbolario');
            }
            $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],];
            $response=$frontManager->petition('GET',$options,$relative_url);
            $herbolario=json_decode($response->getBody()->getContents(),true); 
            return $this->render('herbolario/herbolario_editar.html.twig', [
                'accion'=>true,
                'herbolario'=>$herbolario['herbolario'],
            ]);
        }
        $this->addFlash('danger','Su sesion ha expirado');
        return $this->redirectToRoute('logging_con_api');
    }

    #[Route('/herbolario/{id}/eliminar',name:'app_eliminar_herbolario')]
    public function eliminar(int $id,Request $request,FrontManager $frontManager): Response
    {
        if(isset($_COOKIE['jwt_token'])){   
            if($request->getMethod()==='POST'){
                $token = $_COOKIE['jwt_token'];
                $relative_url='api/herbolario/delete/'.$id;
                $options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],];
                $response=$frontManager->petition('DELETE',$options,$relative_url);
                if($response->getStatusCode() === 200) {
                    $this->addFlash('success', 'herbolario eliminado correctamente');
                } else {
                    $this->addFlash('danger', 'Hubo un error al eliminar el herbolario');
                };
                return $this->redirectToRoute('app_usos');
            }
        }    
        $this->addFlash('danger','Su sesion ha expirado');
        return $this->redirectToRoute('logging_con_api');
     
    }


}
