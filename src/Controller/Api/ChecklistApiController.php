<?php

namespace App\Controller\Api;

use App\Entity\Enum\AgreedEnum;
use App\Entity\Enum\ChecklistEnum;
use App\Entity\Enum\PaymentStatusEnum;
use App\Entity\Enum\RentalEnum;
use App\Entity\Rental;
use App\Entity\UserCarAvailableItem;
use App\Entity\UserCarIssue;
use App\Entity\UserCarMissingItem;
use App\Entity\UserTripChecklist;
use App\Form\Api\ChecklistType;
use App\Service\ApiService;
use App\Service\DashboardService;
use App\Service\Notification;
use App\Service\Validate;
use App\Util\Form;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api', name: 'api_')]
class ChecklistApiController extends AbstractApiController
{

    private $apiService;
    private $doctrine;
    private $dashboardServicePermissions;
    private $notification;

    public function __construct(ManagerRegistry $doctrine, ApiService $apiService, DashboardService $dashboardServicePermissions,
        Notification $notification
    ) {
        $this->apiService = $apiService;
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;
        $this->notification = $notification;
    }
    #[Route("/v1/checklist/add", name: "checklist_create", methods: ["POST"])]
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
    #[OA\Tag(name: 'Checklist')]
    #[Security(name: 'Token')]
    public function add(Request $request): JsonResponse
    {
        try {

            $token = Validate::parameter($request->get('token'));
            $rentalId = Validate::parameter($request->get('rentalId'));
            $id = Validate::parameter($request->get('id'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $instance = new UserTripChecklist;
            $user = $user;
            $formTitle = 'Add checklist';
            $issues = [];
            $availableItems = [];
            $missingItems = [];
            //Check if rental doesn't have the checklist the user is trying to create
            if (empty($id)) {
                $type = $request->request->get('checklist');
                if ($type) {
                    $instances = $this->doctrine->getRepository(UserTripChecklist::class)->findBy(['type' => $type['type'], 'rental' => $rentalId]);
                    if (count($instances)) {
                        $existingChecklist = current($instances);
                        //Load the checklist
                        $instance = $existingChecklist;
                    }
                }
            }

            if (!empty($id)) {
                $formTitle = "Update checklist";
                $instance = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
                $issueList = $this->doctrine->getRepository(UserCarIssue::class)->findBy(['checklist' => $instance]);
                $issues = $this->apiService->process($issueList);

                $availableItemsList = $this->doctrine->getRepository(UserCarAvailableItem::class)->findBy(['checklist' => $instance]);
                $availableItems = $this->apiService->process($availableItemsList);

                $missingItemslist = $this->doctrine->getRepository(UserCarMissingItem::class)->findBy(['checklist' => $instance]);
                $missingItems = $this->apiService->process($missingItemslist);
            }

            $form = $this->buildForm(ChecklistType::class, $instance);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->doctrine->getManager();
                $rental = $this->doctrine->getRepository(Rental::class)->findOneBy(['id' => $rentalId]);
                if ($rental) {
                    $instance->setOwner($user);
                    $instance->setRental($rental);
                    $instance->setStatus(PaymentStatusEnum::PROCESSING);
                    $entityManager->persist($instance);
                    $entityManager->flush();
                    $requestRenterToSign = $request->request->get('checklist');
                    if ($requestRenterToSign) {
                        //dd($requestRenterToSign);
                        // send email to renter to review and sign checklist
                        if (isset($requestRenterToSign['requestRenterToSign'])) {
                            $appData = $this->dashboardServicePermissions->getSiteDetails();
                            $this->notification->sendEmail([
                                "from" => $appData["email"],
                                "to" => $rental->getUser()->getEmail(),
                                "replyTo" => $appData["email"],
                                "subject" => "Rental #" . $rental->getId() . " Checklist review",
                                "text" => "Rental #" . $rental->getId() . " Checklist review",
                                "template" => "email/templates/checklist.html.twig",
                                "context" => [
                                    'user' => $this->apiService->sortObjectToArray($rental->getUser()),
                                    'invoice' => ['id' => $rental->getId()],
                                    'checklist' => $this->apiService->sortObjectToArray($instance),
                                ],
                            ]);

                            $this->notification->sendNotification($rental->getUser(), [
                                'header' => "Rental #" . $rental->getId() . " Checklist review",
                                'body' => "Rental #" . $rental->getId() . " Checklist review.Check email for full details!",
                            ]);
                        }
                    }
                    //$this->addFlash('success', $formTitle.' was successfully');
                    return $this->respond([
                        'form' => $form,
                        //'id' =>$id,
                        'rentalId' => $rentalId,
                        'issues' => $issues,
                        'availableItems' => $availableItems,
                        'missingItems' => $missingItems,
                        'title' => $formTitle,
                    ]);

                }
            }

            return $this->respondError([
                'message' => "Form contains errors",
                'errors' => Form::ErrorMessages($form),
            ]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/checklist/view", name: "checklist_view", methods: ["GET"])]
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
    #[OA\Tag(name: 'Checklist')]
    #[Security(name: 'Token')]
    public function viewChecklist(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $id = Validate::parameter($request->get('id'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $instance = new UserTripChecklist;
            $user = $user;
            $checklist = 'Checklist';
            $issues = [];
            $availableItems = [];
            $missingItems = [];
            $additionItems = [];
            $restrictions = [];
            $types = array_flip(AgreedEnum::choices());

            //if (!empty($id)) {
            $instance = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
            $issueList = $this->doctrine->getRepository(UserCarIssue::class)->findBy(['checklist' => $instance]);
            $issues = $this->apiService->process($issueList);

            $availableItemsList = $this->doctrine->getRepository(UserCarAvailableItem::class)->findBy(['checklist' => $instance]);
            $availableItems = $this->apiService->process($availableItemsList);

            $missingItemslist = $this->doctrine->getRepository(UserCarMissingItem::class)->findBy(['checklist' => $instance]);
            $missingItems = $this->apiService->process($missingItemslist);
            $vehicle = $instance->getRental()->getCar();
            $vehiclePaidDeposit = $vehicle->getRefundableDeposit();
            foreach ($missingItemslist as $item) {
                $vehiclePaidDeposit = (float) $vehiclePaidDeposit - ((int) $item->getMeasurement() * (float) $item->getAmount());
            }
            //}

            $vehicle = $instance->getRental()->getCar();
            if ($vehicle) {
                $additionals = $vehicle->getOwner()->getUserCarAdditionals();
                $additionalTotalToAdd = 0;
                foreach ($additionals as $additional) {
                    $additionalTotalToAdd = $additionalTotalToAdd + $additional->getAmount();
                    array_push($additionItems, ['description' => $additional->getDescription(), 'amount' => $additional->getAmount()]);
                }
                $restrictionList = $vehicle->getOwner()->getUserDrivingRestrictions();
                $restrictions = $this->apiService->process($restrictionList);
            }

            return $this->respond([
                'checklist' => $this->apiService->sortObjectToArray($instance),
                'issues' => $issues,
                'availableItems' => $availableItems,
                'missingItems' => $missingItems,
                'restrictions' => $restrictions,
                'additionals' => $additionals,
                'refundableDeposit' => $vehiclePaidDeposit,
                "types" => $types,
            ]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/sign/document", name: "checklist_sign_document", methods: ["POST"])]
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
    #[OA\Tag(name: 'Checklist')]
    #[Security(name: 'Token')]
    public function signDocument(Request $request): JsonResponse
    {
        try {

            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $agree = $request->request->get('agree');
            $id = $request->request->get('id');
            $rentalId = $request->request->get('rental');
            if (!empty($agree)) {
                $entityManager = $this->doctrine->getManager();
                $instance = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
                if ($instance) {
                    if ($agree == AgreedEnum::YES) {
                        $instance->setDateSignedByRenter(new \DateTime('now'));
                        $instance->setRenterAgreed($agree);
                        $instance->setStatus(PaymentStatusEnum::DONE);
                        $entityManager->persist($instance);
                        $entityManager->flush();
                        //Send Email
                        $appData = $this->dashboardServicePermissions->getSiteDetails();
                        $this->notification->sendEmail([
                            "from" => $appData["email"],
                            "to" => $instance->getRental()->getCar()->getOwner()->getEmail(),
                            "replyTo" => $appData["email"],
                            "subject" => 'Rental #' . $instance->getRental()->getId() . ' Checklist approved',
                            "text" => 'Rental #' . $instance->getRental()->getId() . ' Checklist approved',
                            "template" => "email/templates/checklist.approved.html.twig",
                            "context" => [
                                'user' => $instance->getRental()->getCar()->getOwner(),
                            ],
                        ]);

                        $this->notification->sendNotification($instance->getRental()->getCar()->getOwner(), [
                            'header' => 'Rental #' . $instance->getRental()->getId() . ' Checklist approved',
                            'body' => 'Rental #' . $instance->getRental()->getId() . ' Checklist approved.Check email for full details!',
                        ]);
                        //IF END OF RESERVATION
                        //Calcalate balance
                        if ($instance->getType() == ChecklistEnum::END) {
                            //$instance->get
                            $rental = $instance->getRental();
                            $qouteAmount = $rental->getQuoteAmount();
                            $issues = $instance->getUserCarAvailableItems();
                            foreach ($issues as $issue) {
                                $qouteAmount = $qouteAmount + ((float) $issue->getAmount() * (int) $issue->getMeasurement());
                            }
                            $rental->setPaidAmount($qouteAmount);
                            $rental->setStatus(RentalEnum::DONE);
                            $vehicle = $rental->getCar();
                            $vehicle->setBooked(false);
                            $entityManager->persist($rental);
                            $entityManager->persist($vehicle);
                            $entityManager->flush();
                        }
                        return $this->respondError(["message" => 'Updated successfully']);
                    } else {
                        return $this->respondError(["message" => 'Check with the car owner so that the issue is resolved successfully']);
                    }

                } else {
                    return $this->respondError(["message" => 'Failed to update checklist']);
                }
            } else {
                return $this->respondError(["message" => 'Select correct item to proceed']);
            }

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/issue/add", name: "checklist_issue_add", methods: ["POST"])]
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
    #[OA\Tag(name: 'Checklist')]
    #[Security(name: 'Token')]
    public function addIssue(Request $request): JsonResponse
    {
        try {

            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $issue = $request->request->get('issue');
            $id = $request->request->get('id');
            $rentalId = $request->request->get('rental');

            if ($issue) {
                $entityManager = $this->doctrine->getManager();
                $instance = new UserCarIssue();
                $trip = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
                if ($trip) {
                    $instance->setChecklist($trip);
                    $instance->setDescription($issue);
                    $entityManager->persist($instance);
                    $entityManager->flush();
                    return $this->respond(["message" => 'Issue added successfully']);
                } else {
                    return $this->respondError(["message" => 'Failed to add issue']);
                }
            } else {
                return $this->respondError(["message" => 'Issue must not be blank']);
            }
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/issue/delete", name: "checklist_issue_delete", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'Checklist')]
    #[Security(name: 'Token')]
    public function deleteIssue(Request $request): JsonResponse
    {
        try {

            $token = Validate::parameter($request->get('token'));
            $id = Validate::parameter($request->get('item'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            if ($this->apiService->delete(UserCarIssue::class, $id)) {
                return $this->respond(["message" => "Issue item deleted successfully"]);
            }
            return $this->respondError(["message" => "Failed to delete issue"]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/available/item/delete", name: "checklist_available_delete", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'Checklist')]
    #[Security(name: 'Token')]
    public function deleteAvailableItem(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $id = Validate::parameter($request->get('id'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            if ($this->apiService->delete(UserCarAvailableItem::class, $id)) {
                return $this->respond(["message" => "Issue item deleted successfully"]);
            }
            return $this->respondError(["message" => "Failed to delete item"]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    // #[Route("", name: "", methods: ["POST"])]
    // #[OA\Response(
    //     response: 200,
    //     description: 'Success message '
    // )]
    // #[OA\Parameter(
    //     name: 'apiKey',
    //     in: 'query',
    //     description: 'App Secret key',
    //     schema: new OA\Schema(type: 'string')
    // )]
    // #[OA\Parameter(
    //     name: 'token',
    //     in: 'query',
    //     description: 'Authentication token',
    //     schema: new OA\Schema(type: 'string')
    // )]
    // #[OA\Parameter(
    //     name: 'id',
    //     in: 'query',
    //     description: 'Item Id',
    //     schema: new OA\Schema(type: 'string')
    // )]
    // #[OA\Tag(name: 'Checklist')]
    // #[Security(name: 'Token')]
    // public function deleteMissingItem(Request $request): JsonResponse
    // {
    //     try {

    //         $token = Validate::parameter($request->get('token'));
    //         $id = Validate::parameter($request->get('id'));
    //         $user = $this->apiService->validateToken($token);
    //         if ($user == null) {
    //             return $this->respondError(["message" => "Invalid token.Please login again"]);
    //         };

    //         if ($this->apiService->delete(UserCarMissingItem::class, $id)) {
    //             return $this->respond(["message" => "Issue item deleted successfully"]);
    //         }
    //         return $this->respondError(["message" => "Failed to delete item"]);
    //     } catch (Exception $e) {
    //         return $this->respondError(["message" => $e->getMessage()]);
    //     }
    // }

    #[Route("/v1/available/item/add", name: "checklist_ available_add", methods: ["POST"])]
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
    #[OA\Tag(name: 'Checklist')]
    #[Security(name: 'Token')]
    public function addAvailableItem(Request $request): JsonResponse
    {
        try {

            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $amount = $request->request->get('amount');
            $measurement = $request->request->get('measurement');
            $description = $request->request->get('description');
            $id = $request->request->get('id');
            $rentalId = $request->request->get('rental');

            if ($description && $amount && $measurement) {
                $entityManager = $this->doctrine->getManager();
                $instance = new UserCarAvailableItem();
                $trip = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
                if ($trip) {
                    $instance->setChecklist($trip);
                    $instance->setAmount($amount);
                    $instance->setMeasurement($measurement);
                    $instance->setDescription($description);
                    $entityManager->persist($instance);
                    $entityManager->flush();
                    return $this->respond(["message" => 'item added successfully']);
                } else {
                    return $this->respondError(["message" => 'Failed to add item']);
                }
            } else {
                return $this->respondError(["message" => "All fields must be filled $description $amount $measurement"]);
            }

            return $this->redirect("/dashboard/checklist/add/$rentalId/$id");
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/missing/item/add", name: "checklist_missing_add", methods: ["POST"])]
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
    #[OA\Tag(name: 'Checklist')]
    #[Security(name: 'Token')]
    public function addMissingItem(Request $request): JsonResponse
    {
        try {

            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $amount = $request->request->get('amount');
            $measurement = $request->request->get('measurement');
            $description = $request->request->get('description');
            $id = $request->request->get('id');
            $rentalId = $request->request->get('rental');

            if ($description && $amount && $measurement) {
                $entityManager = $this->doctrine->getManager();
                $instance = new UserCarMissingItem();
                $trip = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
                if ($trip) {
                    $instance->setChecklist($trip);
                    $instance->setAmount($amount);
                    $instance->setMeasurement($measurement);
                    $instance->setDescription($description);
                    $entityManager->persist($instance);
                    $entityManager->flush();
                    return $this->respond(["message" => 'item added successfully']);
                } else {
                    return $this->respondError(["message" => 'Failed to add item']);
                }
            } else {

                return $this->respondError(["message" => 'Issue must not be blank']);
            }

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/checklist/delete", name: "checklist_delete", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'Checklist')]
    #[Security(name: 'Token')]
    public function delete(Request $request): JsonResponse
    {
        try {

            $token = Validate::parameter($request->get('token'));
            $id = Validate::parameter($request->get('id'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $checklist = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
            if ($checklist) {
                if ($checklist->getStatus() != PaymentStatusEnum::DONE) {
                    $entityManager = $this->doctrine->getManager();
                    $entityManager->remove($checklist);
                    $entityManager->flush();
                    return $this->respondError(["message" => "Checklist item deleted successfully"]);
                } else {
                    return $this->respondError(["message" => "Checklist can't be deleted ,it's closed"]);
                }
            }
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

}
