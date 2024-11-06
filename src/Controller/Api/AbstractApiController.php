<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

abstract class AbstractApiController extends AbstractController
{
    protected function buildForm(string $type, $data = null, array $options = []): FormInterface
    {
        $options = array_merge($options, [
           'csrf_protection' => false,
        ]);

        return $this->container->get('form.factory')->createNamed('', $type, $data, $options);
    }

    protected function respondError($data):JsonResponse{
        $data['code'] =400;
        return $this->respond($data,Response::HTTP_BAD_REQUEST);
    }

    protected function respond($data, int $statusCode = Response::HTTP_OK):JsonResponse
    {
        if($statusCode == Response::HTTP_OK){
            
            $code = 200;
            if(isset($data['code'])){
                $code = $data['code'];
                unset($data['code']);
            }
            
            $message = 'Success';
            if(isset($data['message'])){
                $message = $data['message'];
                unset($data['message']);
            }
           
           $data = [
               'code'=>$code,
               'message'=>$message,
               'body'=>['data'=>$data]
               ];
        }
        return new JsonResponse($data, $statusCode);
    }

    

    protected function respondForm($data, int $statusCode = Response::HTTP_OK):JsonResponse
    {
        return new JsonResponse($data, $statusCode);
    }
}