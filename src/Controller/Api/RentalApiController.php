<?php

namespace App\Controller\Api;

use App\Entity\Enum\AccountTypeEnum;
use App\Entity\Enum\PaymentStatusEnum;
use App\Entity\Enum\RentalEnum;
use App\Entity\Images\Eft;
use App\Entity\Rental;
use App\Entity\UserCarMissingItem;
use App\Service\ApiService;
use App\Service\ClientService;
use App\Service\Common;
use App\Service\DashboardService;
use App\Service\Notification;
use App\Service\Validate;
use App\Util\FileSystem;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api', name: 'api_')]
class RentalApiController extends AbstractApiController
{

    private $dashboardServicePermissions;
    private $doctrine;
    private $apiService;
    private $common;
    private $fileSystem;
    private $notification;
    private $clientService;

    public function __construct(DashboardService $dashboardServicePermissions,
        Common $common,
        FileSystem $fileSystem,
        ManagerRegistry $doctrine, ApiService $apiService,
        Notification $notification,
        ClientService $clientService
    ) {
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;
        $this->common = $common;
        $this->apiService = $apiService;
        $this->fileSystem = $fileSystem;
        $this->notification = $notification;
        $this->clientService = $clientService;
    }

    #[Route("/v1/rentals", name: "rentals", methods: ["GET"])]
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
    #[OA\Tag(name: 'Rental')]
    #[Security(name: 'Token')]
    public function index(Request $request): JsonResponse
    {
        $token = Validate::parameter($request->get('token'));
        $user = $this->apiService->validateToken($token);
        if ($user == null) {
            return $this->respondError(["message" => "Invalid token.Please login again"]);
        };
        $user = $user['object'];
        $admin = $user;
        $rentalList = [];
        $history = [];
        if ($admin->getType() == AccountTypeEnum::RENTEE) {
            $cars = $admin->getCars();
            foreach ($cars as $car) {
                $rentals = $car->getRentals();
                foreach ($rentals as $rental) {
                    $processedItem = $this->apiService->sortObjectToArray($rental);
                    $processedItem['bank'] = $this->apiService->process($rental->getCar()->getOwner()->getUserBanks());
                    $processedItem['payfast'] = $this->apiService->sortObjectToArray($rental->getCar()->getOwner()->getUserPayFast());
                    $processedItem['paypal'] = $this->apiService->sortObjectToArray($rental->getCar()->getOwner()->getUserPayPal());
                    $processedItem['breakdown'] = $this->clientService->totalBreakDown($rental);
                    if ($rental->getStatus() == RentalEnum::DONE) {
                        array_push($history, $processedItem);
                    } else {
                        array_push($rentalList, $processedItem);
                    }
                }
            }
        } else {
            $rentals = $this->doctrine->getRepository(Rental::class)->findBy(['user' => $admin], ["dateCreated" => "ASC"]);
            foreach ($rentals as $rental) {
                $processedItem = $this->apiService->sortObjectToArray($rental);
                $processedItem['bank'] = $this->apiService->process($rental->getCar()->getOwner()->getUserBanks());
                $processedItem['payfast'] = $this->apiService->sortObjectToArray($rental->getCar()->getOwner()->getUserPayFast());
                $processedItem['paypal'] = $this->apiService->sortObjectToArray($rental->getCar()->getOwner()->getUserPayPal());
                $processedItem['breakdown'] = $this->clientService->totalBreakDown($rental);
                if ($rental->getStatus() == RentalEnum::DONE) {
                    array_push($history, $processedItem);
                } else {
                    array_push($rentalList, $processedItem);
                }
            }
        }

        // $key_values = array_column($rentalList, 'dateModified');
        //     array_multisort($key_values, SORT_ASC, $rentalList);

        return $this->respond([
            'list' => $rentalList,
            'history' => $history,
        ]);
    }

    #[Route("/v1/rental/view", name: "rental_view", methods: ["GET"])]
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
    #[OA\Tag(name: 'Rental')]
    #[Security(name: 'Token')]
    public function viewRental(Request $request): JsonResponse
    {

        $token = Validate::parameter($request->get('token'));
        $id = Validate::parameter($request->get('id'));
        $user = $this->apiService->validateToken($token);
        if ($user == null) {
            return $this->respondError(["message" => "Invalid token.Please login again"]);
        };
        $user = $user['object'];
        $admin = $user;
        $downloadPayment = "";
        $instance = $this->doctrine->getRepository(Rental::class)->find($id);
        if (!$instance) {
            return $this->respondError(["message" => "Invalid rental"]);
        }
        $rental = $this->apiService->sortObjectToArray($instance);
        $bank = $this->apiService->process($instance->getCar()->getOwner()->getUserBanks());
        $breakdown = $this->clientService->totalBreakDown($instance);
        $payfast = $this->apiService->sortObjectToArray($instance->getCar()->getOwner()->getUserPayFast());
        $paypal = $this->apiService->sortObjectToArray($instance->getCar()->getOwner()->getUserPayPal());

        $efts = $this->common->getFiles($id, $admin->getId(), "eft");
        if (count($efts)) {
            $eft = current($efts);
            $downloadPayment = $eft['file'];
        }
        $checklists = $instance->getUserTripChecklists();
        $missingItems = [];
        $vehicle = $instance->getCar();
        $vehiclePaidDeposit = $vehicle->getRefundableDeposit();
        $done = true;
        foreach ($checklists as $checklist) {
            $missingItemslist = $this->doctrine->getRepository(UserCarMissingItem::class)->findBy(['checklist' => $checklist]);
            $missingItems = $this->apiService->process($missingItemslist);
            if ($checklist->getStatus() != PaymentStatusEnum::DONE) {
                $done = false;
            }
            foreach ($missingItemslist as $item) {
                $vehiclePaidDeposit = (float) $vehiclePaidDeposit - ((int) $item->getMeasurement() * (float) $item->getAmount());
            }
        }

        return $this->respond([
            'title' => "Rental",
            'rental' => $rental,
            'bank' => $bank,
            'paypal' => $paypal,
            'payfast' => $payfast,
            'done' => $done,
            'missingItems' => $missingItems,
            'refundableDeposit' => $vehiclePaidDeposit,
            'downloadPayment' => $downloadPayment,
        ]);
    }
    #[Route("/v1/rental/approve/payment", name: "rental_approve_payment", methods: ["POST"])]
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
    #[OA\Tag(name: 'Rental')]
    #[Security(name: 'Token')]
    public function approveRentalPayment(Request $request): JsonResponse
    {

        $token = Validate::parameter($request->get('token'));
        $id = Validate::parameter($request->get('id'));
        $user = $this->apiService->validateToken($token);
        if ($user == null) {
            return $this->respondError(["message" => "Invalid token.Please login again"]);
        };
        $user = $user['object'];
        $admin = $user;
        $rental = $this->doctrine->getRepository(Rental::class)->find($id);
        $downloadPayment = [];
        if ($rental) {
            $entityManager = $this->doctrine->getManager();
            $rental->setPaymentStatus(PaymentStatusEnum::SUCCESSFUL);
            $rental->setStatus(RentalEnum::INPROGRESS);
            // $rental->setBooked(true);
            $vehicle = $rental->getCar();
            $vehicle->setBooked(true);

            $entityManager->persist($rental);
            $entityManager->persist($vehicle);
            $entityManager->flush();

            $efts = $this->common->getFiles($id, $rental->getUser()->getId(), "eft");
            if (count($efts)) {
                $downloadPayment = current($efts);
            }
            //Send Email to notifiy car renter
            $appData = $this->dashboardServicePermissions->getSiteDetails();
            $this->notification->sendEmail([
                "from" => $appData["email"],
                "to" => $rental->getUser()->getEmail(),
                "replyTo" => $appData["email"],
                "subject" => "Rental #" . $rental->getId() . " Proof of Payment Approved",
                "text" => "Rental #" . $rental->getId() . " Proof of Payment Approved",
                "template" => "email/templates/eft.approve.html.twig",
                "context" => [
                    'user' => $rental->getUser(),
                    'invoice' => ['id' => $rental->getId()],
                ],
            ], [$downloadPayment]);

            $this->notification->sendNotification($rental->getUser(), [
                'header' => "Rental #" . $rental->getId() . " Proof of Payment Approved",
                'body' => "Rental #" . $rental->getId() . " Proof of Payment Approved.Check email for full details!",
            ]);

            return $this->respondError(["message" => "Payment approved successfully"]);
        } else {
            return $this->respondError(["message" => "Invalid rental"]);
        }
    }

    #[Route("/v1/rental/reject/payment", name: "rental_reject_payment", methods: ["POST"])]
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
    #[OA\Tag(name: 'Rental')]
    #[Security(name: 'Token')]
    public function rejectRentalPayment(Request $request): JsonResponse
    {

        $token = Validate::parameter($request->get('token'));
        $id = Validate::parameter($request->get('id'));
        $user = $this->apiService->validateToken($token);
        if ($user == null) {
            return $this->respondError(["message" => "Invalid token.Please login again"]);
        };
        $user = $user['object'];
        $admin = $user;
        $rental = $this->doctrine->getRepository(Rental::class)->find($id);
        if ($rental) {
            $efts = $this->common->getFiles($id, $admin->getId(), "eft");
            if (count($efts)) {
                $downloadPayment = current($efts);
            }
            //Send Email to notifiy car renter
            $appData = $this->dashboardServicePermissions->getSiteDetails();
            $this->notification->sendEmail([
                "from" => $appData["email"],
                "to" => $rental->getUser()->getEmail(),
                "replyTo" => $appData["email"],
                "subject" => "Rental #" . $rental->getId() . " Proof of Payment Rejected",
                "text" => "Rental #" . $rental->getId() . " Proof of Payment Rejected",
                "template" => "email/templates/eft.rejected.html.twig",
                "context" => [
                    'user' => $rental->getUser(),
                    'invoice' => ['id' => $rental->getId()],
                ],
            ]);

            $this->notification->sendNotification($rental->getUser(), [
                'header' => "Rental #" . $rental->getId() . " Proof of Payment Rejected",
                'body' => "Rental #" . $rental->getId() . " Proof of Payment Rejected.Check email for full details!",
            ]);
            return $this->respondError(["message" => "Payment POP rejected successfully"]);
        } else {
            return $this->respondError(["message" => "Invalid rental"]);
        }
    }

    #[Route("/v1/upload/eft", name: "rental_upload_eft", methods: ["POST"])]
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
    #[OA\Tag(name: 'Rental')]
    #[Security(name: 'Token')]
    public function uploadEft(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $file = Validate::parameter($request->files->get('file'));
            $id = Validate::parameter($request->request->get('id'));
            if ($file) {
                if ($file->getMimeType() == "application/pdf") {
                    $rental = $this->doctrine->getRepository(Rental::class)->find($id);
                    if ($rental) {
                        $entityManager = $this->doctrine->getManager();
                        $imageObject = new Eft();
                        $newFilename = "eft-$id-" . uniqid() . '.pdf';
                        $item = $this->doctrine->getRepository(Eft::class)->findOneBy(['owner' => $id]);
                        if ($item) {
                            $entityManager->remove($item);
                            $entityManager->flush();
                            $this->fileSystem->deleterUseFile($item->getImage(), $user->getId(), "eft");
                            // $this->addFlash('warning', "Note : Old eft file replaced by new eft file");
                        }
                        $destination = $this->fileSystem->getPath($user->getId(), "eft");
                        $file->move($destination, $newFilename);
                        $localImage = $destination . $newFilename;
                        if (is_file($localImage)) {
                            $imageObject->setImage($newFilename);
                            $imageObject->setOwner($id);
                            $entityManager->persist($imageObject);
                            $entityManager->flush();
                            //Update rental payment status
                            $rental->setPaymentStatus(PaymentStatusEnum::PROCESSING);
                            $entityManager->persist($rental);
                            $entityManager->flush();
                            //Send Email to notifiy car owner
                            $appData = $this->dashboardServicePermissions->getSiteDetails();
                            $this->notification->sendEmail([
                                "from" => $appData["email"],
                                "to" => $rental->getCar()->getOwner()->getEmail(),
                                "replyTo" => $appData["email"],
                                "subject" => "Rental #" . $rental->getId() . " Proof of Payment Added",
                                "text" => "Rental #" . $rental->getId() . " Proof of Payment Added",
                                "template" => "email/templates/eft.rejected.html.twig",
                                "context" => [
                                    'user' => $rental->getCar()->getOwner(),
                                    'invoice' => ['id' => $rental->getId()],
                                ],
                            ]);
                            $this->notification->sendNotification($rental->getCar()->getOwner(), [
                                'header' => "Rental #" . $rental->getId() . " Proof of Payment Added",
                                'body' => "Rental #" . $rental->getId() . " Proof of Payment Added.Check email for full details!",
                            ]);

                            return $this->respond(["message" => "Eft Uploaded successfully"]);
                        } else {return $this->respondError(["message" => "Invalid type ! Only pdf is allowed.Please try again"]);}
                    } else {return $this->respondError(["message" => "Invalid rental.Please try again"]);}
                } else {return $this->respondError(["message" => "Error uploading eft file,pdf only .Please try again"]);}
            } else {return $this->respondError(["message" => "Invalid Eft.Please try again"]);}
        } catch (\Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }

    }

    #[Route("/v1/rental/delete", name: "rental_delete", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'Rental')]
    #[Security(name: 'Token')]
    public function delete(Request $request): JsonResponse
    {

        $token = Validate::parameter($request->get('token'));
        $id = Validate::parameter($request->get('id'));
        $user = $this->apiService->validateToken($token);
        if ($user == null) {
            return $this->respondError(["message" => "Invalid token.Please login again"]);
        };
        $user = $user['object'];
        $rental = $this->doctrine->getRepository(Rental::class)->find($id);
        if ($rental) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($rental);
            $entityManager->flush();
            $this->common->deleteFile($id, $user->getId(), "eft");
            return $this->respond(["message" => 'Rental updated successfully']);
        } else {
            return $this->respondError(["message" => 'Failed to delete rental.Please try again later']);
        }
    }

}
