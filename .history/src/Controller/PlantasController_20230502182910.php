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
        $nombre_uso = [];
        $nombre_herbolario = [];
        $precio = [];
        $usos_array = [];
        $herbolario_array = [];
        $infocomercial_array = [];
        $usos_rela
        $planta=new Planta();
        $usos=$usosRepository->findAll();
        $herbolarios=$herbolarioRepository->findAll();
        if($request->getMethod()==='POST'){
            $planta_nombre=$request->request->get('nombre');
            $planta_especie=$request->request->get('especie');
            $planta_lugar=$request->request->get('lugar');
          
            $planta->setNombre($planta_nombre);
            $planta->setEspecie($planta_especie);
            $planta->setLugar($planta_lugar);
            $entityManager->persist( $planta);
            for($i=1;$i<4;$i++){
                $nombre_uso[$i]=$request->request->get('uso'.$i);
                $nombre_herbolario[$i]=$request->request->get('herbolario'.$i);
                $precio[$i]=$request->request->get('precio'.$i);
            }

            if(!$planta_nombre || !$planta_especie || !$planta_lugar || !$precio[1] || !$precio[2] || !$precio[3]){
                $this->addFlash('danger', 'Complete todos los campos ');
                return $this->redirectToRoute('app_plantas_nueva');
            }
            if($plantaRepository->findOneBy(['nombre'=>$planta_nombre])){
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
            
            for($i=1;$i<4;$i++){
                $usos_array[$i]=new Usos();
                $herbolario_array[$i]=new Herbolario();
                $infocomercial_array[$i]=new Infocomercial();
                $usos_array[$i]=$usosRepository->findOneBy(['nombre' =>$nombre_uso[$i]]);            
                $herbolario_array[$i]=$herbolarioRepository->findOneBy(['nombre' =>$nombre_herbolario[$i]]); 
                $infocomercial_array[$i]->setPrecio((float)$precio[$i]);
                $infocomercial_array[$i]->setHerbolarioid($herbolario_array[$i]);
                $infocomercial_array[$i]->setPlantaid($planta);
                $entityManager->persist( $infocomercial_array[$i]);
                $planta->addUso($usos_array[$i]);
                $entityManager->flush();
            }
            $this->addFlash('success','La informacion de la Planta se añadió exitosamente');
            return $this->redirectToRoute('app_index');
        }
        return $this->render('Plantas/plantas_nueva.html.twig', [
            'accion' => false,
            'usos'=>$usos,
            'herbolarios'=>$herbolarios
        ]);
    }
}
