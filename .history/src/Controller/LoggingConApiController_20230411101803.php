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
                        'json' => ['username' => $username, 'password' => $password],'timeout' => 5,
                    ]);
   
                        // Verifica si la respuesta contiene un token JWT válido y redirige al usuario a la página de inicio si es exitoso

                    $jwt = json_decode($response->getBody()->getContents(), true)['token'];
                    dump($jwt);
                    die()
                    
                    if ($jwt) {
                        $response = new Response();
                        $cookie = new Cookie('jwt_token', $jwt, time() + (3600 * 24 * 7), '/', null, false, true);
                        $response->headers->setCookie($cookie);
                        $response->send();
                        return $this->redirectToRoute('app_index');
                        }
                    throw new AuthenticationException('Invalid credentials');
                }catch (RequestException $e) {
                    throw new AuthenticationException('Error en la solicitud HTTP: ' . $e->getMessage());
                }

            }

        // if($request->getMethod()==='POST'){

            // $username = $request->request->get('_username');
            // $password = $request->request->get('_password');
            // $client = new Client(['base_uri' => 'http://127.0.0.1:8000/','verify'=>false]);

            // $promise = $client->requestAsync('POST', 'api/login_check', [
            //     'json' => ['username' => $username, 'password' => $password],
                
            // ]);

            // $promise->then(
            //     function ($response) {
            //         // Manejar la respuesta
            //         dump($response->getBody());
            //         die();
            //     },
            //     function ($exception) {
            //         // Manejar cualquier error
            //         echo $exception->getMessage();
            //     }
            // );

            // $response = $promise->wait();

        //     $client = new Client();
        //     $response = $client->get('http://www.example.com');
        //     echo $response->getBody();
        //     $client->po
        // }
        
        return $this->render('logging_con_api\log.html.twig', [
            'controller_name' => 'LoggingConApiController',
        ]);
    
    }
}
