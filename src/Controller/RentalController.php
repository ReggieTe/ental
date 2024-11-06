<?php

namespace App\Controller;

use App\Entity\Enum\AccountTypeEnum;
use App\Entity\Enum\PaymentStatusEnum;
use App\Entity\Enum\RentalEnum;
use App\Entity\Images\Eft;
use App\Entity\Rental;
use App\Entity\RentalDiscount;
use App\Entity\RentalLevy;
use App\Entity\UserAdmin;
use App\Entity\UserCarMissingItem;
use App\Entity\UserTripChecklist;
use App\Service\ApiService;
use App\Service\ClientService;
use App\Service\Common;
use App\Service\DashboardService;
use App\Service\Notification;
use App\Service\Validate;
use App\Util\FileSystem;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class RentalController extends AbstractController
{

    private $dashboardServicePermissions;
    private $doctrine;
    private $apiService;
    private $common;
    private $fileSystem;
    private $notification;
    private $clientService;
    private $pdf;

    public function __construct(
        ManagerRegistry $doctrine, DashboardService $dashboardServicePermissions,
        Common $common,
        FileSystem $fileSystem,
        ApiService $apiService,
        Notification $notification,
        ClientService $clientService,
        Pdf $pdf
    ) {
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;
        $this->common = $common;
        $this->apiService = $apiService;
        $this->fileSystem = $fileSystem;
        $this->notification = $notification;
        $this->clientService = $clientService;
        $this->pdf = $pdf;
    }

    #[Route("/dashboard/rentals", name: "View Rentals")]
    public function index(#[CurrentUser] UserAdmin $user, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $admin = $user;
        $rentalList = [];
        $history = [];
        if ($admin->getType() == AccountTypeEnum::RENTEE) {
            $cars = $admin->getCars();
            foreach ($cars as $car) {
                $rentals = $car->getRentals();
                foreach ($rentals as $rental) {
                    $processedItem = $this->apiService->sortObjectToArray($rental);
                    $processedItem['levy'] = $this->apiService->getRentalLevies($rental->getId());
                    $processedItem['discount'] = $this->apiService->getRentalDiscounts($rental->getId());
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
                $processedItem['levy'] = $this->apiService->getRentalLevies($rental->getId());
                $processedItem['discount'] = $this->apiService->getRentalDiscounts($rental->getId());
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

        //dd($rentalList);
        return $this->render('dashboard/rentals/index.html.twig', [
            'title' => "Rentals",
            'list' => array_reverse($rentalList),
            'history' => $history,
            'permissions' => $this->dashboardServicePermissions->getDashboardPermission($admin),
            'site' => $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }

    #[Route("/dashboard/rental/view/{id}", name: "View Rental", methods: ["POST", "GET"])]
    public function viewRental(#[CurrentUser] UserAdmin $user, Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $admin = $user;
        $downloadPayment = "";
        $instance = $this->doctrine->getRepository(Rental::class)->find($id);
        if (!$instance) {
            $this->addFlash('danger', "Invalid rental");
            return $this->redirect("/dashboard/rentals");
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
        $rental['bank'] = $this->apiService->process($instance->getCar()->getOwner()->getUserBanks());
        $rental['payfast'] = $this->apiService->sortObjectToArray($instance->getCar()->getOwner()->getUserPayFast());
        $rental['paypal'] = $this->apiService->sortObjectToArray($instance->getCar()->getOwner()->getUserPayPal());
        $rental['levy'] = $this->apiService->getRentalLevies($id);
        $rental['discount'] = $this->apiService->getRentalDiscounts($id);
        $rental['breakdown'] = $breakdown;

        return $this->render('dashboard/rentals/view/index.html.twig', [
            'title' => "Rental",
            'rental' => $rental,
            'bank' => $bank,
            'paypal' => $paypal,
            'payfast' => $payfast,
            'done' => $done,
            'missingItems' => $missingItems,
            'refundableDeposit' => $vehiclePaidDeposit,
            'downloadPayment' => $downloadPayment,
            'permissions' => $this->dashboardServicePermissions->getDashboardPermission($admin),
            'site' => $this->dashboardServicePermissions->getSiteDetails(),
        ]);

    }

    #[Route("/dashboard/rental/download/{id}", name: "Download Rental", methods: ["POST", "GET"])]
    public function downloadInvoice(#[CurrentUser] UserAdmin $user, Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $instance = $this->doctrine->getRepository(Rental::class)->find($id);
        if (!$instance) {
            $this->addFlash('danger', "Invalid rental");
            return $this->redirect("/dashboard/rentals");
        }

        $appData = $this->dashboardServicePermissions->getSiteDetails();
        $additionals = $instance->getCar()->getOwner()->getUserCarAdditionals();
        $additionItems = [];
        foreach ($additionals as $additional) {
            if ($additional->getAddToBookingtotal()) {
                array_push($additionItems, ['description' => $additional->getDescription(), 'amount' => $additional->getAmount()]);
            }

        }
        //Calcaulate days
        $pickupdate = Validate::sortDate($instance->getPickupdate());
        $returndate = Validate::sortDate($instance->getDropoffdate());
        $currentDate = new \DateTime('now');
        $bookingDays = $pickupdate->diff($returndate);
        $daysToBooking = $pickupdate->diff($currentDate);
        $daysBooked = $bookingDays->d;
        if ($daysBooked <= 0) {
            $daysBooked = 1;
        }
        $daysTillBookedDate = $daysToBooking->d;

        $html = $this->render('email/templates/pdf/invoice.html.twig', [
            'statement' => [
                "id" => $instance->getId(),
                "user" => $this->apiService->sortObjectToArray($instance->getUser()),
                "rental" => $this->apiService->sortObjectToArray($instance),
                'levy' => $this->apiService->getRentalLevies($id),
                'discount' => $this->apiService->getRentalDiscounts($id),
                "daysBooked" => $daysBooked,
                "daysTillBookedDate" => $daysTillBookedDate,
                "totalBookingFee" => $instance->getQuoteAmount(),
                "additionItems" => $additionItems,
                "restrictions" => $this->apiService->process($instance->getCar()->getOwner()->getUserDrivingRestrictions()),
                "car" => $this->apiService->sortObjectToArray($instance->getCar()),
            ],
            'site' => $appData,
        ]);
        $pdf = $this->pdf->getOutputFromHtml($html);
        return new Response(
            $pdf,
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Rental#' . $instance->getId() . '-invoice.pdf"',
            )
        );
    }

    #[Route("/dashboard/rental/approve/payment/{id}", name: "approveRentalPayment")]
    public function approveRentalPayment(#[CurrentUser] UserAdmin $user, Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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

            $this->addFlash('success', "Payment approved successfully");
        } else {
            $this->addFlash('danger', "Invalid rental");
        }
        return $this->redirect("/dashboard/rental/view/$id");
    }

    #[Route("/dashboard/rental/reject/payment/{id}", name: "rejectRentalPayment")]
    public function rejectRentalPayment(#[CurrentUser] UserAdmin $user, Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
            $this->addFlash('success', "Payment POP rejected successfully");
        } else {
            $this->addFlash('danger', "Invalid rental");
        }
        return $this->redirect("/dashboard/rental/view/$id");
    }

    #[Route("/dashboard/upload/eft", name: "uploadEft", methods: ["GET", "POST"])]
    public function upload(#[CurrentUser] UserAdmin $user, Request $request): Response
    {
        try {
            $file = $request->files->get('file');
            $id = $request->request->get('id');
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
                            $this->addFlash('warning', "Note : Old eft file replaced by new eft file");
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
                                "template" => "email/templates/eft.html.twig",
                                "context" => [
                                    'user' => $rental->getCar()->getOwner(),
                                    'invoice' => ['id' => $rental->getId()],
                                ],
                            ]);
                            $this->notification->sendNotification($rental->getCar()->getOwner(), [
                                'header' => "Rental #" . $rental->getId() . " Proof of Payment Added",
                                'body' => "Rental #" . $rental->getId() . " Proof of Payment Added.Check email for full details!",
                            ]);

                            $this->addFlash('success', "Eft Uploaded successfully");
                        } else { $this->addFlash('danger', "Invalid type ! Only pdf is allowed.Please try again");}
                    } else { $this->addFlash('danger', "Invalid rental.Please try again");}
                } else { $this->addFlash('danger', "Error uploading eft file,pdf only .Please try again");}
            } else { $this->addFlash('danger', "Invalid Eft.Please try again");}
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }
        return $this->redirect("/dashboard/rentals");

    }

    #[Route("/dashboard/rental/delete/{id?}", name: "Delete Rental", methods:["GET"])]
    public function delete(#[CurrentUser] UserAdmin $user, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $rental = $this->doctrine->getRepository(Rental::class)->find($id);
        if ($rental) {
            $entityManager = $this->doctrine->getManager();
            //Delete levies
            $rentalLevies = $this->doctrine->getRepository(RentalLevy::class)->findBy(['rental' => $id]);
            foreach ($rentalLevies as $item) {
                $entityManager->remove($item);
                $entityManager->flush();
            }
            //Delete discounts
            $rentalDiscount = $this->doctrine->getRepository(RentalDiscount::class)->findBy(['discount' => $id]);
            foreach ($rentalDiscount as $item) {
                $entityManager->remove($item);
                $entityManager->flush();
            }

            $rentalChecklist = $this->doctrine->getRepository(UserTripChecklist::class)->findBy(['rental' => $id]);
            foreach ($rentalChecklist as $item) {
                $entityManager->remove($item);
                $entityManager->flush();
            }
            $entityManager->remove($rental);
            $entityManager->flush();
            $this->common->deleteFile($id, $user->getId(), "eft");
            $this->addFlash('success', 'Rental updated successfully');
        } else {
            $this->addFlash('danger', 'Failed to delete rental.Please try again later');
        }
        return $this->redirect("/dashboard/rentals");
    }
}
