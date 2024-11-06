<?php

namespace App\Controller\Api;

use App\Controller\Api\AbstractApiController;
use App\Entity\UserAdmin;
use App\Entity\UserOtp;
use App\Service\ApiService;
use App\Service\Notification;
use App\Service\Validate;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api', name: 'api_')]
class VerifyApiController extends AbstractApiController
{
    private $apiService;
    private $doctrine;
    private $notification;

    public function __construct(Notification $notification, ManagerRegistry $doctrine, ApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->notification = $notification;
        $this->doctrine = $doctrine;
    }

    #[Route("/v1/validate/phone", name: "verify_phonee", methods: ["POST"])]
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
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Item Id',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Verify')]
    #[Security(name: 'Token')]
    public function index(Request $request): JsonResponse
    {
        try {
            $phone = $request->get("phone");

            if ($phone == "") {
                return $this->respondError(["message" => "phone required.Please try again"]);
            }
            if (isset($phone)) {
                $phoneValid = $this->doctrine->getRepository(UserAdmin::class)->findBy(['phone' => $phone]);
                if (count($phoneValid)) {
                    return $this->respondError(["message" => "Phone number.Already registered"]);
                }
            }
            return $this->respond(['message' => "Phone Available"]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/verify/phone", name: "verify_phone", methods: ["POST"])]
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
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Item Id',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Verify')]
    #[Security(name: 'Token')]
    public function phone(Request $request): JsonResponse
    {
        try {
            $phone = $request->get("phone");
            $user = null;
            if ($phone == "") {
                return $this->respondError(["message" => "phone required.Please try again"]);
            }
            if (isset($phone)) {
                $phoneValid = $this->doctrine->getRepository(UserAdmin::class)->findBy(['phone' => $phone]);
                if (!count($phoneValid)) {
                    return $this->respondError(["message" => "Phone invalid."]);
                }
            }
            $user = current($phoneValid);
            //use phone to get user
            $message = "Failed sending code.Please try again";
            $otpCode = rand(1000, 10000);
            if (isset($phone)) {
                $codeSent = false;
                $result = []; //$this->otpService->getOtp($phone);
                $opt = $result['otpCode'];
                $expired = $result['expired'];
                if ($opt != null) {
                    $otpCode = $opt;
                    if (!$expired) {
                        $message = "Resending a now expired code";
                    }
                    $message = "Wait and try again after 1 minutes";
                    $codeSent = true;
                } else {
                    //$this->otpService->createOtp($phone,$otpCode,$user,true);
                    $message = "Verification code sent to Phone number :$phone";
                }

                if (!$codeSent) {
                    //$this->otpService->send($phone,$otpCode);
                }
            }
            return $this->respond(['otp' => $otpCode, 'message' => $message]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/verify/phone/otp", name: "verify_phone_otp", methods: ["POST"])]
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
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Item Id',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Verify')]
    #[Security(name: 'Token')]
    public function verifyPhoneOtp(Request $request): JsonResponse
    {
        try {
            $phone = $request->get("phone");
            $otp = $request->get('otp');
            $user = null;
            $code = '+27';

            if ($phone == "") {
                return $this->respondError(["message" => "phone required.Please try again"]);
            }
            if (isset($phone)) {
                $phoneValid = $this->doctrine->getRepository(UserAdmin::class)->findBy(['phone' => $phone]);
                if (!count($phoneValid)) {
                    return $this->respondError(["message" => "Phone invalid."]);
                }
            }

            $user = current($phoneValid);
            if (isset($phone) && !empty($otp)) {
                $otps = $this->doctrine->getRepository(UserOtp::class)->findBy(['phone' => $phone, 'state' => 0, 'otp' => $otp]);
                if (count($otps)) {
                    $foundOtp = current($otps);
                    $user->setPhoneVerified(1);
                    $user->setDateModified(new DateTime('now'));
                    $foundOtp->setState(1);
                    $foundOtp->setDateModified(new DateTime('now'));

                    $entityManager = $this->doctrine->getManager();
                    $entityManager->persist($user);
                    $entityManager->persist($foundOtp);
                    $entityManager->flush();

                    return $this->respond(['message' => "Phone number verified successfully", 'id' => $user->getId()]);
                }
                return $this->respondError(['message' => "Otp expired, request a new otp!"]);
            }
            return $this->respondError(['message' => "Failed to verify phone number.Try again"]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }

    }

    #[Route("/v1/email", name: "verify_email", methods: ["POST"])]
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
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Item Id',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Verify')]
    #[Security(name: 'Token')]
    public function email(Request $request): JsonResponse
    {
        try {
            $email = $request->get("email");
            if ($email == "") {
                return $this->respondError(["message" => "Email required.Please try again"]);
            }
            if (isset($email)) {
                $emailValid = $this->doctrine->getRepository(UserAdmin::class)->findBy(['email' => $email]);
                if (!count($emailValid)) {
                    return $this->respondError(["message" => "Email invalid."]);
                }
            }
            $user = current($emailValid);

            $message = "Email required.Please try again";
            if (isset($email)) {
                $codeSent = false;
                $otpCode = rand(1000, 10000);
                //check previously sent otp
                $result = []; //$this->otpService->getOtp($email,true);
                $opt = $result['otpCode'];
                $expired = $result['expired'];
                if ($opt != null) {
                    $otpCode = $opt;
                    if (!$expired) {
                        $message = "Resending code";
                    }
                    $message = "Wait and try again after 1 minutes";
                    $codeSent = true;
                } else {

                    //$this->otpService->createOtp($email,$otpCode,$user,false);
                    $message = "Verification code sent to email:$email";
                }

                if (!$codeSent) {

                    // $this->otpService->sendEmail(
                    //     $this->apiService->getAppSettings()['email'],
                    //     $user->getEmail(),
                    //     $otpCode);
                }
            }
            return $this->respond(['message' => $message, 'otp' => $otpCode]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/verify/email/otp", name: "verify_email_otp", methods: ["POST"])]
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
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Item Id',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Verify')]
    #[Security(name: 'Token')]
    public function verifyEmailOtp(Request $request): JsonResponse
    {
        try {
            $email = $request->get("email");
            $otp = $request->get('otp');

            if ($email == "") {
                return $this->respondError(["message" => "Email required.Please try again"]);
            }
            if ($otp == "") {
                return $this->respondError(["message" => "Otp required.Please try again"]);
            }
            if (isset($email)) {
                $emailValid = $this->doctrine->getRepository(UserAdmin::class)->findBy(['email' => $email]);
                if (!count($emailValid)) {
                    return $this->respondError(["message" => "Email invalid."]);
                }
            }
            $user = current($emailValid);

            if (isset($email) && !empty($otp)) {
                $otps = $this->doctrine->getRepository(UserOtp::class)->findBy(['email' => $email, 'state' => 0, 'otp' => $otp]);
                if (count($otps)) {
                    $foundOtp = current($otps);
                    $user->setEmailVerified(1);
                    $user->setDateModified(new DateTime('now'));
                    $foundOtp->setState(1);
                    $foundOtp->setDateModified(new DateTime('now'));

                    $entityManager = $this->doctrine->getManager();
                    $entityManager->persist($user);
                    $entityManager->persist($foundOtp);
                    $entityManager->flush();

                    return $this->respond(['message' => "Email verified successfully", 'id' => $user->getId()]);
                }

                return $this->respondError(["message" => "Failed to verify email.Try again.Otp expired, request a new otp!"]);
            }

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }

    }

    #[Route("/v1/new/password", name: "verify_new_password", methods: ["POST"])]
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
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Item Id',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Verify')]
    #[Security(name: 'Token')]
    public function newPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        try {
            $password = $request->get("password");
            $conpassword = $request->get("conpassword");
            $id = $request->get("id");

            if ($password == "" || $conpassword == "") {
                return $this->respondError(["message" => "Password required.Please try again"]);
            }

            if ($password != $conpassword) {
                return $this->respondError(["message" => "Passwords must be the same.Please try again"]);
            }

            $user = $this->doctrine->getRepository(UserAdmin::class)->find(["id" => $id]);
            if ($user != null) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $password));
                $salt = uniqid();
                $user->setSalt($salt);
                $user->setLastPasswordResetRequestDate(new \DateTime('now'));
                $user->setApiPassword(Validate::hash($password, $salt));
                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $appData = $this->apiService->getAppSettings();
                $this->notification->sendEmail([
                    "from" => $appData["email"],
                    "to" => $user->getEmail(),
                    "replyTo" => $appData["email"],
                    "subject" => 'Account Password Changed',
                    "text" => 'Account Password Changed',
                    "template" => "email/templates/passwordChanged.html.twig",
                    "context" => [
                        'user' => $user,
                    ],
                ]);

                $message = "Password changed successfully.Login";
            } else {
                $message = "Invalid user.Please try again";
            }
            return $this->respond(['message' => $message, "state" => "success"]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

}
