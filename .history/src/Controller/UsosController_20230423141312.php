<?php

namespace App\Controller;

use App\Entity\Usos;
use App\Repository\UsosRepository;
use App\Services\FrontManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;

class UsosController extends AbstractController
{
    #[Route('/usos', name: 'app_usos')]
    public function index(FrontManager $frontManager): Response
    {
        $usos=$usosRepository->findAll();
        return $this->render('usos/usos_index.html.twig', [
                'usos'=>$usos,
        ]);
    }


    #[Route('/usos/nuevo', name: 'app_nuevo_usos')]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $usos= new Usos();
        if($request->getMethod()==='POST'){
        $nombre=$request->request->get('nombre'); 
        $nombre=trim($nombre);  
            if($nombre!=""){
                $usos->setNombre($nombre);
                $entityManager->persist($usos);
                $entityManager->flush();
                $this->addFlash('success','Uso correctamente añadido');
            }
            else
                $this->addFlash('danger','El Campo Uso no puede estar en blanco');

        return $this->redirectToRoute('app_usos');

        }
        return $this->render('usos/usos_nuevo.html.twig', [
            'accion' => false,
        ]);
    }


    #[Route('/usos/{id}/editar', name: 'app_usos_editar')]
    public function editar(Usos $uso,Request $request,EntityManagerInterface $entityManager): Response
    {
        
        
        if($request->getMethod()==='POST'){
            $nombre=$request->request->get('nombre'); 
            $nombre=u($nombre)->trim();
            if($nombre!=""){
                $uso->setNombre($nombre);
                $entityManager->flush();
                $this->addFlash('success','Uso correctamente modificado');
            } 
            else
                $this->addFlash('danger','El Campo uso no puede estar en blanco');
                
         return $this->redirectToRoute('app_usos');     

        }
            
        return $this->render('usos/usos_editar.html.twig', [
            'uso' => $uso,
            'accion' => true,  // accion editar para que se modifique el boton del template, el del submit
        ]);
    }

    #[Route('/usos/{id}/eliminar', name: 'app_usos_eliminar')]
    public function eliminar(Usos $uso,UsosRepository $usosRepository,EntityManagerInterface $entityManager,Request $request): Response
    {
        if($request->getMethod()==='POST'){
            $entityManager->remove($uso);
            $entityManager->flush();
            $this->addFlash('success', 'Uso eliminado correctamente');
            return $this->redirectToRoute('app_usos');
        }
    }


}
