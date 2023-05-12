<?php

namespace App\Controller;

use App\Entity\Herbolario;
use App\Entity\Infocomercial;
use App\Entity\Planta;
use App\Entity\Usos;
use App\Repository\HerbolarioRepository;
use App\Repository\PlantaRepository;
use App\Repository\UsosRepository;
use App\Services\FrontManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlantasController extends AbstractController
{
    #[Route('/plantas/nueva', name: 'app_plantas_nueva')]
    public function nueva(Request $request,FrontManager $frontManager): Response
    {
        // Verificar si el usuario tiene una sesión activa
        if(isset($_COOKIE['jwt_token'])){

            // Inicializar algunas variables para almacenar la información de los usos y herbolarios
            $token = $_COOKIE['jwt_token'];
            $nombre_uso = [];
            $nombre_herbolario = [];
            $precio = [];

            $usos_index_options=$herbolario_index_options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],]; 
            $usos_index_relativeurl='api/usos/index';
            $usos_response=$frontManager->petition('GET',$usos_index_options,$usos_index_relativeurl);
            $usos=json_decode($usos_response->getBody()->getContents(),true);
            $herbolario_index_relativeurl='api/herbolario/index';
            $herbolarios_response=$frontManager->petition('GET',$herbolario_index_options,$herbolario_index_relativeurl);
            $herbolarios=json_decode($herbolarios_response->getBody()->getContents(),true);

            if($request->getMethod()==='POST'){
                $planta_nombre=$request->request->get('nombre');
                $planta_especie=$request->request->get('especie');
                $planta_lugar=$request->request->get('lugar');
                $planta_find_relative_url='api/planta/findby';
                $planta_query_params = ['nombre' => $planta_nombre];
                $options_find=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],'query' => $planta_query_params];
                $planta_find_response=$frontManager->petition('GET',$options_find,$planta_find_relative_url);
                $planta_find_response_content=json_decode($planta_find_response->getBody()->getContents(),true);
                $usos_index_relativeurl='api/usos/index';
                $usos=$frontManager->petition('GET',$usos_index_options,$usos_index_relativeurl);

                for($i=1;$i<4;$i++){
                    $nombre_uso[$i]=$request->request->get('uso'.$i);
                    $nombre_herbolario[$i]=$request->request->get('herbolario'.$i);
                    $precio[$i]=$request->request->get('precio'.$i);
                }

                if(!$planta_nombre || !$planta_especie || !$planta_lugar || !$precio[1] || !$precio[2] || !$precio[3]){
                    $this->addFlash('danger', 'Complete todos los campos ');
                    return $this->redirectToRoute('app_plantas_nueva');
                }

                if($planta_find_response_content['status']===200){
                    $this->addFlash('danger', "La planta '$planta_nombre' ya esta registrada");
                    return $this->redirectToRoute('app_plantas_nueva');

                }

                if($nombre_uso[1]===$nombre_uso[2]||$nombre_uso[1]===$nombre_uso[3]||$nombre_uso[2]===$nombre_uso[3]){
                    $this->addFlash('danger', 'No pueden repetirse Usos en la misma planta');
                    return $this->redirectToRoute('app_plantas_nueva');
                }elseif($nombre_herbolario[1]===$nombre_herbolario[2]||$nombre_herbolario[1]===$nombre_herbolario[3]||$nombre_herbolario[2]===$nombre_herbolario[3]){
                    $this->addFlash('danger', 'No pueden repetirse Herbolarios en la misma planta');
                    return $this->redirectToRoute('app_plantas_nueva');
                }
                $planta_new_relativeurl='api/planta/new';
                $usos_planta = [];
                $herbolarios_planta = [];
                for ($i = 1; $i <= 3; $i++) {
                    $usos_planta[] = array('nombre' => $nombre_uso[$i]);
                    $herbolarios_planta[] = array('nombre' => $nombre_herbolario[$i], 'precio' => $precio[$i]);
                }
                     

                $planta_new_options=['headers' => ['Authorization' => 'Bearer '.$token, 'Accept'        => 'application/json'],
                                    'json' => ['nombre' => $planta_nombre,
                                               'especie'=>$planta_especie,
                                                'lugar'=>$planta_lugar,
                                                'uso'=>$usos_planta,
                                                'herbolario'=>$herbolarios_planta
                                                ]];
                $frontManager->petition('POST',$planta_new_options,$planta_new_relativeurl); 

                $this->addFlash('success','La informacion de la Planta se añadió exitosamente');
                return $this->redirectToRoute('app_index');
            }
            return $this->render('Plantas/plantas_nueva.html.twig', [
                'accion' => false,
                'usos'=>$usos['usos'],
                'herbolarios'=>$herbolarios['herbolarios']
            ]);
        }

        $this->addFlash('danger','Su sesion ha expirado');
        return $this->redirectToRoute('logging_con_api');
        
    }
}
