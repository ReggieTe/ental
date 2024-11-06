<?php

namespace App\Controller\Api;

use App\Entity\UserAdmin;
use App\Entity\UserAppNotification;
use App\Service\ApiService;
use App\Service\Validate;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api', name: 'api_')]
class LoginApiController extends AbstractApiController
{
    private $apiService;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, ApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;
    }

    #[Route("/v1/login", name: "account_login", methods: ["POST"])]
    #[OA\Response(
        response: 200,
        description: 'Success message '
    )]
    #[OA\Parameter(
        name: 'apiKey',
        in: 'query',
        description: 'App Secret key',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Login')]
    #[Security(name: 'Token')]
    public function logout(Request $request): JsonResponse
    {try {
        $token = Validate::parameter($request->get('token'));
        $user = $this->apiService->validateToken($token);
        $user = $user['object'];
        $user->setLastlogout(new \DateTime('now'));
        $user->setToken(uniqid());
        $manager = $this->doctrine->getManager();
        $manager->persist($user);
        $manager->flush();

        return $this->respond(['logout' => true, 'message' => "Logged out successfully"]);

    } catch (Exception $e) {
        return $this->respondError(["message" => $e->getMessage()]);
    }}

    #[Route("/v1/logout", name: "acount_logout", methods: ["POST"])]
    #[OA\Response(
        response: 200,
        description: 'Success message '
    )]
    #[OA\Parameter(
        name: 'apiKey',
        in: 'query',
        description: 'App Secret key',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'token',
        in: 'query',
        description: 'Authentication token',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Login')]
    #[Security(name: 'Token')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        try {
            $email = $request->get('email');
            $password = $request->get('password');
            $fcmId = $request->get('fcmId');
            if ($email == "") {
                return $this->respondError(["message" => "Email required.Please try again"]);
            }
            if ($password == "") {
                return $this->respondError(["message" => "Password required.Please try again"]);
            }

            if (isset($email) && isset($password)) {
                $users = $this->doctrine->getRepository(UserAdmin::class)->findBy(['email' => $email]);
                if (count($users)) {
                    $user = current($users);
                    if ($user) {
                        $salt = $user->getSalt();
                        $hashedPassword = Validate::hash($password, $salt);
                        if (!empty($user->getApiPassword()) && !empty($hashedPassword)) {
                            if ($user->getApiPassword() == $hashedPassword) {
                                if ($user instanceof UserAdmin) {
                                    if ($fcmId) {
                                        $user->setFcmId($fcmId);
                                    }
                                    $user->setToken(uniqid());
                                    $user->setLastlogin(new \DateTime('now'));
                                    $manager = $this->doctrine->getManager();
                                    $manager->persist($user);
                                    $manager->flush();
                                    $settings = $this->apiService->sortObjectToArray($user->getUserSetting());
                                    $user = $this->apiService->validateToken($user->getToken());

                                    $alerts = 0;
                                    $notifications = $manager->getRepository(UserAppNotification::class)->findBy(['user' => $user['object'], "notificationRead" => 0]);
                                    $alerts = count($notifications);

                                    return $this->respond([
                                        'profile' => $user['json'],
                                        'notifications' => $alerts,
                                        'settings' => $settings,
                                        'paypal' => $this->apiService->sortObjectToArray($user['object']->getUserPayPal()),
                                        'payfast' => $this->apiService->sortObjectToArray($user['object']->getUserPayFast()),

                                    ]);
                                }
                            }
                        }
                    }
                }

            }
            return $this->respondError(["message" => "Invalid login details.Please login again"]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }

    }

}
