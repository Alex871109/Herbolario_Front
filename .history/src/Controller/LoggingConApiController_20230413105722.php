<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Cookie;
use GuzzleHttp\Promise;


class LoggingConApiController extends AbstractController
{
    #[Route('/Apilogging', name: 'logging_con_api')]
    public function index(Request $request): Response
    {
        
            if($request->getMethod()==='POST'){
                try{
                    $username = $request->request->get('_username');
                    $password = $request->request->get('_password');
                    $client = new Client(['base_uri' => 'http://127.0.0.1:8000/','verify'=>false]);
                    $response = $client->request('POST', 'api/login_check', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $cookie // agregar el token JWT como un encabezado de autorización
                        ],);
                    $jwt = json_decode($response->getBody()->getContents(), true)['token'];                    
                    $response = new Response();
                    $cookie = new Cookie('jwt_token', $jwt, time() + 3600 , '/', null, false, true);
                    $response->headers->setCookie($cookie);
                    $response->send();
                    return $this->redirectToRoute('app_index');

                }catch (RequestException $e) {
                    //throw new AuthenticationException('123Error en la solicitud HTTP: ' . $e->getMessage());
                    echo "Usuario o Contraseña equivocados";
                }

            }
        
        return $this->render('logging_con_api\log.html.twig', [
            'controller_name' => 'LoggingConApiController',
        ]);
    
    }
}
