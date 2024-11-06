<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Contact;
use App\Entity\Enum\PaymentEnum;
use App\Entity\Enum\PaymentStatusEnum;
use App\Entity\Enum\PromotionTypeEnum;
use App\Entity\Enum\RentalEnum;
use App\Entity\Enum\SectionEnum;
use App\Entity\Rental;
use App\Entity\RentalDiscount;
use App\Entity\RentalLevy;
use App\Entity\UserAdmin;
use App\Form\ContactType;
use App\Service\ApiService;
use App\Service\Common;
use App\Service\DashboardService;
use App\Service\Notification;
use App\Service\Validate;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    private $dashboardServicePermissions;
    private $doctrine;
    private $common;
    private $apiService;
    private $requestStack;
    private $notification;
    private $pdf;

    public function __construct(
        ManagerRegistry $doctrine, DashboardService $dashboardServicePermissions,
        RequestStack $requestStack,
        Common $common,
        ApiService $apiService,
        Notification $notification,
        Pdf $pdf

    ) {
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;
        $this->common = $common;
        $this->apiService = $apiService;
        $this->requestStack = $requestStack;
        $this->notification = $notification;
        $this->pdf = $pdf;
    }
    #[Route("/", name: "index", methods: ["GET"])]

    public function index(): Response
    {
        return $this->redirect("/home");
    }

    #[Route("/home", name: "homePage", methods: ["GET"])]

    public function home(#[CurrentUser] ?UserAdmin $user): Response
    {

        $vehicles = $this->doctrine->getRepository(Car::class)->findBy(['active' => 1, "booked" => 0]);
        $availableCar = $this->apiService->process($vehicles);
        $documents = [];
        $session = $this->requestStack->getSession();
        $currentPage = "";
        $statement = [];
        $invoiceGenerated = false;
        $filters = [];

        if (!empty($session->get('currentPage'))) {
            $currentPage = $session->get('currentPage');
        }
        if (!empty($session->get('statement'))) {
            $statement = $session->get('statement');
        }
        if (!empty($session->get('invoiceGenerated'))) {
            $invoiceGenerated = $session->get('invoiceGenerated');
        }
        if ($user) {
            $documents = $this->common->getFiles($user->getId(), $user->getId(), "document");
        }
        $counts = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10];
        $filters['fuel'] = $this->apiService->prepareFilters($vehicles, "fuel");
        $filters['transmission'] = $this->apiService->prepareFilters($vehicles, "transmission");
        $filters['payment'] = $this->apiService->prepareFilters($vehicles, "payment");
        $filters['doors'] = $counts;
        $filters['seats'] = $counts;
        $filters['bags'] = $counts;
        $filters['brand'] = $this->apiService->prepareFilters($vehicles, "brand");
        $filters['price'] = $this->apiService->prepareFilters($vehicles, "price");
        $filters['deposit'] = $this->apiService->prepareFilters($vehicles, "deposit");

        return $this->renderPage('web/home/index.html.twig', [
            'current' => $currentPage,
            'cars' => $availableCar,
            'documents' => $documents,
            'missingDocument' => false,
            'statement' => $statement,
            "invoiceGenerated" => $invoiceGenerated,
            "filters" => $filters,
            'title' => 'Home',
            'paymentTypes' => $filters['payment'],
        ]);
    }

    #[Route("/form/process", name: "processForm", methods: ["POST"])]

    public function processForm(#[CurrentUser] ?UserAdmin $user, Request $request): Response
    {
        $message = "";
        $daysBooked = 0;
        $daysTillBookedDate = 0;
        $totalBookingFee = 0;
        $currentVehicle = null;
        $complete = false;
        $restrictions = [];
        $additionItems = [];
        $qoute = new Rental();
        try {
            //    $user =  $this->apiService->sortObjectToArray($user);
            $location = $request->request->get('location');
            $pickupdate = $request->request->get('pickupdate');
            $returndate = $request->request->get('returndate');
            $pickSameAsDrop = $request->request->get('pickSameAsDrop');
            $dropofflocation = $request->request->get('dlocation');
            $id = $request->request->get('id');
            $selectedVehicle = $request->request->get('selected-vehicle');

            if (empty($location)) {
                return new JsonResponse([
                    "complete" => false,
                    "type" => "location",
                    "message" => "Invalid location ! Please select a valid location",
                ]);
            }
            if ($pickSameAsDrop) {
                $dropofflocation = $location;
            } else {
                if (empty($dropofflocation)) {
                    return new JsonResponse([
                        "complete" => false,
                        "type" => "location",
                        "message" => "Invalid drop off location ! Please select a valid location",
                    ]);
                }
            }

            if (empty($pickupdate)) {
                return new JsonResponse([
                    "complete" => false,
                    "type" => "location",
                    "message" => "Invalid pick up date ! Please try again",
                ]);
            }
            if (empty($returndate)) {
                return new JsonResponse([
                    "complete" => false,
                    "type" => "location",
                    "message" => "Invalid return date ! Please try again",
                ]);
            }
            if (empty($selectedVehicle)) {
                return new JsonResponse([
                    "complete" => false,
                    "type" => "cars",
                    "message" => "Invalid vehicle ! Please select a vehicle and try again",
                ]);
            }

            $documents = $this->common->getFiles($user->getId(), $user->getId(), "document");
            $pickupdate = Validate::sortDate($pickupdate);
            $returndate = Validate::sortDate($returndate);

            $session = $this->requestStack->getSession();
            if ($id) {
                if (!empty($session->get('qouteId'))) {
                    $id = $session->get('qouteId');
                }
            }

            if ($pickupdate && $returndate) {
                $pickupdate = Validate::sortDate($pickupdate);
                $returndate = Validate::sortDate($returndate);
                $currentDate = new \DateTime('now');
                $bookingDays = $pickupdate->diff($returndate);
                $daysToBooking = $pickupdate->diff($currentDate);
                $daysBooked = $bookingDays->d;
                if ($daysBooked <= 0) {
                    $daysBooked = 1;
                }
                $daysTillBookedDate = $daysToBooking->d;
            }

            if ($selectedVehicle) {
                $vehicle = $this->doctrine->getRepository(Car::class)->find($selectedVehicle);
                if ($vehicle) {
                    $additionals = $vehicle->getOwner()->getUserCarAdditionals();
                    $additionalTotalToAdd = 0;
                    foreach ($additionals as $additional) {
                        if ($additional->getAddToBookingtotal()) {
                            $additionalTotalToAdd = $additionalTotalToAdd + $additional->getAmount();
                            array_push($additionItems, ['description' => $additional->getDescription(), 'amount' => $additional->getAmount()]);
                        }

                    }

                    $restrictionList = $vehicle->getOwner()->getUserDrivingRestrictions();
                    $restrictions = $this->apiService->process($restrictionList);

                    $car = $this->apiService->sortObjectToArray($vehicle);
                    $currentVehicle = $car;
                    if ($car["rate"]) {
                        $totalBookingFee = $daysBooked * $car["rate"];
                        $totalBookingFee = $totalBookingFee + $car['deposit'] + $additionalTotalToAdd;
                    }
                }

                //Calculate Discounts

                $promotions = $vehicle->getOwner()->getPromotions();
                $today = new \DateTime("now");
                $appliedDiscounts = [];
                foreach ($promotions as $promotion) {
                    if ($promotion->getActive()) {
                        $startDate = $promotion->getStartDate();
                        $endDate = $promotion->getEndDate();
                        $diffBetweenTodayAndStartDate = $today->diff($startDate);
                        $diffBetweenTodayAndEndDate = $today->diff($endDate);

                        $displayPromotion = $this->apiService->sortObjectToArray($promotion);
                        if ($promotion->getType() == PromotionTypeEnum::AMOUNT) {
                            $totalBookingFee = $totalBookingFee - (float) $promotion->getAmount();
                            $displayPromotion['total'] = $promotion->getAmount();
                            $displayPromotion['display'] = $promotion->getAmount();

                        }
                        if ($promotion->getType() == PromotionTypeEnum::PECENTAGE) {
                            if ($promotion->getAmount()) {
                                $discount = $totalBookingFee * ($promotion->getAmount() / 100);
                                $totalBookingFee = $totalBookingFee - (float) $discount;
                                $displayPromotion['total'] = $discount;
                                $displayPromotion['display'] = $promotion->getAmount() . "%";
                            }
                        }
                        if ($promotion->getType() == PromotionTypeEnum::DAY) {
                            if ($promotion->getAmount()) {
                                $discount = $promotion->getAmount() * $vehicle->getRatePerDay();
                                $totalBookingFee = $totalBookingFee - (float) $discount;
                                $displayPromotion['total'] = $discount;
                                $displayPromotion['display'] = $promotion->getAmount() . " days";
                            }
                        }
                        array_push($appliedDiscounts, $displayPromotion);

                    }
                }
                //Calcaluate levies
                $taxableTotal = (float) $totalBookingFee - (float) $vehicle->getRefundableDeposit();
                $taxApplied = [];
                $levies = $this->apiService->getLevies($vehicle->getOwner());
                foreach ($levies as $levy) {
                    $apply = true;

                    if (str_contains(strtolower($levy->getName()), 'airport')) {
                        if (!str_contains(strtolower($location), 'airport')) {
                            $apply = false;
                        }
                        if (str_contains(strtolower($location), 'airport')) {
                            $totalBookingFee = $totalBookingFee + ($taxableTotal * ($levy->getAmount() / 100));
                        }
                    }
                    if ($apply) {
                        $taxAmount = 0;
                        if ($levy->getAmount()) {
                            $taxAmount = $taxableTotal * ($levy->getAmount() / 100);
                        }
                        $taxItem = $this->apiService->sortObjectToArray($levy);
                        $taxItem['total'] = $taxAmount;
                        array_push($taxApplied, $taxItem);
                    }
                }
            }

            $entityManager = $this->doctrine->getManager();
            if (!empty($id)) {
                $instance = $this->doctrine->getRepository(Rental::class)->find($id);
                if ($instance) {
                    $entityManager->remove($instance);
                    $entityManager->flush();
                }
            }
            if ($vehicle) {
                //create qoute
                $qoute->setUser($user);
                $qoute->setCar($vehicle);
                $qoute->setLocation($location);
                $qoute->setDropOffLocation($dropofflocation);
                $qoute->setPickupdate($pickupdate);
                $qoute->setDropoffdate($returndate);
                $qoute->setQuoteAmount($totalBookingFee);
                $qoute->setPaidAmount(0);
                $qoute->setPaymentStatus(PaymentStatusEnum::PENDING);
                $qoute->setStatus(RentalEnum::WAITINGPAYMENT);
                $qoute->setPaymentType(PaymentEnum::EFT);
                $entityManager->persist($qoute);
                $entityManager->flush();

                foreach ($taxApplied as $levy) {
                    $rentalLevy = new RentalLevy();
                    $rentalLevy->setRental($qoute->getId());
                    $rentalLevy->setLevy($levy['id']);
                    $rentalLevy->setTotal($levy['total']);
                    $entityManager->persist($rentalLevy);
                    $entityManager->flush();
                }

                foreach ($appliedDiscounts as $discount) {
                    $rentalDiscount = new RentalDiscount();
                    $rentalDiscount->setRental($qoute->getId());
                    $rentalDiscount->setDiscount($discount['id']);
                    $rentalDiscount->setTotal($discount['total']);
                    $entityManager->persist($rentalDiscount);
                    $entityManager->flush();
                }

                $id = $qoute->getId();
                $session->set('qouteId', $id);
                $session->set("invoiceGenerated", true);
                $session->set("statement", [
                    "message" => $message,
                    "user" => $user,
                    "rental" => $this->apiService->sortObjectToArray($qoute),
                    'levy' => $taxApplied,
                    'discount' => $appliedDiscounts,
                    "location" => $location,
                    "documents" => $documents,
                    "daysBooked" => $daysBooked,
                    "daysTillBookedDate" => $daysTillBookedDate,
                    "totalBookingFee" => $totalBookingFee,
                    "additionItems" => $additionItems,
                    "restrictions" => $restrictions,
                    "car" => $currentVehicle,
                    "id" => $id,
                ]);
                $complete = true;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $appData = $this->dashboardServicePermissions->getSiteDetails();

        return new JsonResponse([
            "complete" => $complete,
            "levy" => $qoute->getLevies(),
            "message" => $message,
            "html" => $this->renderView('js/statement.html.twig', [
                "site" => $appData,
                "statement" => [
                    "message" => $message,
                    "user" => $user,
                    "rental" => $this->apiService->sortObjectToArray($qoute),
                    'levy' => $taxApplied,
                    'discount' => $appliedDiscounts,
                    "location" => $location,
                    "documents" => $documents,
                    "daysBooked" => $daysBooked,
                    "daysTillBookedDate" => $daysTillBookedDate,
                    "totalBookingFee" => $totalBookingFee,
                    "additionItems" => $additionItems,
                    "restrictions" => $restrictions,
                    "car" => $currentVehicle,
                    "id" => $id,
                ]]),
            "id" => $id,
        ]);
    }

    #[Route("/form/process/totals", name: "processFormTotals", methods: ["POST"])]

    public function processTotals(Request $request): Response
    {
        $statement = [];
        $complete = true;
        $message = "";
        $session = $this->requestStack->getSession();
        $payment = [];
        $appData = $this->dashboardServicePermissions->getSiteDetails();
        try {

            if (!empty($session->get('statement'))) {
                $statement = $session->get('statement');
                $vehicles = $this->doctrine->getRepository(Car::class)->findBy(['active' => 1, "booked" => 0]);
                $payment = $this->apiService->prepareFilters($vehicles, "payment");
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return new JsonResponse([
            "complete" => $complete,
            "message" => $message,
            "html" => $this->renderView('js/payment.html.twig', [
                "site" => $appData,
                "statement" => $statement,
                'paymentTypes' => $payment,
            ]),
        ]);
    }

    #[Route("/form/process/payment", name: "processFormPayment", methods: ["POST"])]

    public function processFormPayment(#[CurrentUser] UserAdmin $user, Request $request): Response
    {
        $message = "";
        $complete = true;
        try {
            $session = $this->requestStack->getSession();
            $paymentType = $request->request->get('payment-type');
            $agree = $request->request->get('agree');
            if ($agree == "on") {
                $statement = null;
                if (!empty($session->get('statement'))) {
                    $statement = $session->get('statement');
                    if ($statement) {
                        $instance = $this->doctrine->getRepository(Rental::class)->find($statement['id']);
                        if ($instance) {
                            $carOwner = $instance->getCar()->getOwner();
                            if ($paymentType == PaymentEnum::PAYFAST) {
                                if (!$carOwner->getIsPayfastEnabled()) {
                                    return new JsonResponse([
                                        "message" => "Unfortnately car owner doesn't accept payfast.Try cash!",
                                        "complete" => false,
                                    ]);
                                }
                            }

                            if ($paymentType == PaymentEnum::PAYPAL) {
                                if (!$carOwner->getIsPaypalEnabled()) {
                                    return new JsonResponse([
                                        "message" => "Unfortnately car owner doesn't accept paypal.Try cash!",
                                        "complete" => false,
                                    ]);
                                }
                            }

                            if ($paymentType == PaymentEnum::EFT) {
                                if (!$carOwner->getIsBankEnabled()) {
                                    return new JsonResponse([
                                        "message" => "Unfortnately car owner doesn't accept eft.Try cash!",
                                        "complete" => false,
                                    ]);
                                }
                            }
                            $entityManager = $this->doctrine->getManager();
                            $instance->setPaymentType($paymentType);
                            $instance->setPaymentStatus(PaymentStatusEnum::PENDING);
                            $instance->setStatus(RentalEnum::WAITINGPAYMENT);
                            $entityManager->persist($instance);
                            $entityManager->flush();
                            //Send email & invoice
                            $appData = $this->dashboardServicePermissions->getSiteDetails();

                            $html = $this->render('email/templates/pdf/invoice.html.twig', [
                                'statement' => $statement,
                                'site' => $appData,
                            ]);
                            $pdf = $this->pdf->getOutputFromHtml($html);
                            $generateFiles = [[
                                "contents" => $pdf,
                                "name" => "Rental#" . $instance->getId() . "-invoice.pdf",
                            ]];

                            // $generateFiles =  [];

                            $this->notification->sendEmail([
                                "from" => $appData["email"],
                                "to" => $user->getEmail(),
                                "replyTo" => $appData["email"],
                                "subject" => 'Rental#' . $instance->getId() . ' invoice',
                                "text" => 'Rental#' . $instance->getId() . ' invoice',
                                "template" => "email/templates/invoice.html.twig",
                                "context" => [
                                    'user' => $user,
                                    'statement' => $statement,
                                ],
                            ], $statement['documents'], $generateFiles);
                            //Send copy to Car Owner
                            $this->notification->sendEmail([
                                "from" => $appData["email"],
                                "to" => $statement['car']['owner']['email'],
                                "replyTo" => $appData["email"],
                                "subject" => 'New Rental#' . $instance->getId() . ' invoice',
                                "text" => 'New Rental#' . $instance->getId() . ' invoice',
                                "template" => "email/templates/invoice.copy.html.twig",
                                "context" => [
                                    'user' => $user,
                                    'statement' => $statement,
                                ],
                            ], $statement['documents'], $generateFiles);

                            $this->notification->sendNotification($user, [
                                'header' => 'Rental #' . $instance->getId() . ' invoice created',
                                'body' => 'Invoice for #' . $instance->getId() . ' has been generated.Check email inbox for full details',
                            ]);
                            //clean session
                            $session->set('statement', []);
                            $session->set('currentPage', '');
                            $session->set('qouteId', '');
                            $session->set("invoiceGenerated", false);
                            $message = "Transaction was successful.now redirecting to rentals";
                            $complete = true;
                        } else {
                            $message = "Invalid transaction. Please retry again";
                            $complete = false;
                        }
                    }
                } else {
                    $message = "No statement available.Refreshing cache now";

                    //clean session
                    $session->set('statement', []);
                    $session->set('currentPage', '');
                    $session->set('qouteId', '');
                    $session->set("invoiceGenerated", false);
                    $complete = true;
                }
            } else {
                $message = "Please read and agree to our terms and conditions to continue";
                $complete = false;
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $complete = false;
        }

        return new JsonResponse([
            "message" => $message,
            "complete" => $complete,
        ]);
    }

    #[Route("/dashboard/discard/rental", name: "Discard", methods: ["POST", "GET"])]

    public function discard(#[CurrentUser] UserAdmin $user, Request $request): Response
    {
        //clean session
        $session = $this->requestStack->getSession();
        $session->set('statement', []);
        $session->set('currentPage', '');
        $session->set('qouteId', '');
        $session->set("invoiceGenerated", false);

        return $this->redirect("/dashboard/rentals");
    }

    #[Route("/form/current/page", name: "setPage", methods: ["POST"])]

    public function setPage(Request $request): Response
    {
        $message = "";
        try {
            $currentPage = strtolower($request->request->get('current'));
            if (!empty($currentPage)) {
                $session = $this->requestStack->getSession();
                $session->set('currentPage', $currentPage);
                $message = "Page set " . $currentPage;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        return new JsonResponse(array('message' => $message));
    }

    #[Route("/form/search/cars/location", name: "filterCarsOnLocationChange", methods: ["POST"])]

    public function filterCarsOnLocationChange(Request $request): Response
    {
        $message = "";
        $nearByCars = [];
        $distances = [];
        $complete = true;

        try {
            $vehicles = $this->doctrine->getRepository(Car::class)->findBy(["active" => 1, "booked" => 0]);
            $location = $request->request->get('location');
            foreach ($vehicles as $key => $value) {
                $carLocation = $value->getOwner()->getAddress();
                if ($carLocation &$location) {
                    $result = $this->apiService->getDistance($carLocation, $location);
                    if (isset($result['distance'])) {
                        array_push($distances, $result['distance']);
                        $distance = $result['distance'];
                        if ($distance >= 0 && $distance <= 60) {
                            array_push($nearByCars, $this->apiService->sortObjectToArray($value));
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $complete = false;
        }
        return new JsonResponse([
            'complete' => $complete,
            'count' => count($nearByCars),
            'message' => $message,
        ]);
    }

    #[Route("/form/filter/cars/location", name: "filterCarsWithDistance", methods: ["POST"])]

    public function filterCarWithDistance(Request $request): Response
    {
        $message = "";
        $nearByCars = [];
        $distances = [];
        $complete = true;
        $appData = $this->dashboardServicePermissions->getSiteDetails();

        try {
            $vehicles = $this->doctrine->getRepository(Car::class)->findBy(["active" => 1, "booked" => 0]);
            $location = $request->request->get('location');
            foreach ($vehicles as $key => $value) {
                $carLocation = $value->getOwner()->getAddress();
                if ($carLocation &$location) {
                    $result = $this->apiService->getDistance($carLocation, $location);
                    if (isset($result['distance'])) {
                        $distance = $result['distance'];
                        if ($distance >= 0 && $distance <= 60) {
                            array_push($nearByCars, $this->apiService->sortObjectToArray($value));
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $complete = false;
        }
        return new JsonResponse([
            'complete' => $complete,
            'count' => count($nearByCars),
            'html' => $this->renderView('js/vehicles.html.twig', ["cars" => $nearByCars, 'site' => $appData]),
            'message' => $message,
        ]);
    }

    #[Route("/form/filter/cars", name: "filterCars", methods: ["POST"])]

    public function filterCars( Request $request): Response
    {
        $message = "filterins";
        $cars = [];
        $complete = true;
        $appData = $this->dashboardServicePermissions->getSiteDetails();

        try {

            $filters = [
                "active" => 1,
                "booked" => 0,
            ];

            $transmission = strtolower($request->request->get('filtertransmission'));
            $fuel = strtolower($request->request->get('filterfuel'));
            $seats = strtolower($request->request->get('filterseats'));
            $doors = strtolower($request->request->get('filterdoors'));
            $brand = strtolower($request->request->get('filterbrand'));
            $price = strtolower($request->request->get('filterprice'));
            $deposit = strtolower($request->request->get('filterdeposit'));
            $clearFilters = $request->request->get('clearFilters');

            if ($clearFilters) {
                if ($transmission) {
                    $filters['transmission'] = $transmission;
                }
                if ($fuel) {
                    $filters['fuel'] = $fuel;
                }
                if ($seats) {
                    $filters['seat_number'] = $seats;
                }
                if ($doors) {
                    $filters['door_number'] = $doors;
                }
                if ($brand) {
                    $filters['brand'] = $brand;
                }
                if ($price) {
                    $filters['ratePerDay'] = $price;
                }
                if ($deposit) {
                    $filters['refundableDeposit'] = $deposit;
                }
            }

            $vehicles = $this->doctrine->getRepository(Car::class)->findBy($filters);
            $location = $request->request->get('location');
            foreach ($vehicles as $key => $value) {
                $carLocation = $value->getOwner()->getAddress();
                if ($carLocation &$location) {
                    $result = $this->apiService->getDistance($carLocation, $location);
                    if (isset($result['distance'])) {
                        $distance = $result['distance'];
                        if ($distance <= 60) {
                            array_push($cars, $this->apiService->sortObjectToArray($value));
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $complete = false;
        }
        return new JsonResponse([
            'complete' => $complete,
            'location' => $location,
            'count' => count($cars),
            'html' => $this->renderView('js/vehicles.html.twig', ["cars" => $cars, 'site' => $appData]),
            'message' => $message,
        ]);
    }

    #[Route("/about", name: "about Us", methods: ["GET"])]

    public function about(): Response
    {
        return $this->renderPage('web/about/index.html.twig', [
            'title' => 'About Us',
        ]);
    }

    #[Route("/contact", name: "Contact Us", methods: ["GET", "POST"])]

    public function contact(#[CurrentUser] ?UserAdmin $user, Request $request): Response
    {

        $app = $this->dashboardServicePermissions->getSiteDetails();
        $contact = new Contact();

        if ($user) {
            $email = $user->getEmail();
            $name = $user->getName();
            if (!empty($email)) {
                $contact->setEmail($email);
            }
            if (!empty($name)) {
                $contact->setName(ucfirst($name));
            }
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();
            $ticketNumber = $contact->getId();

            //To user
            $this->notification->sendEmail([
                "from" => $app["email"],
                "to" => $contact->getEmail(),
                "replyTo" => $app['email'],
                "subject" => 'Acknoweledge Ticket',
                "text" => "Acknoweledge Ticket #$ticketNumber",
                "template" => "email/templates/default.html.twig",
                "context" => [
                    'message' => "Thank you for getting touch with us, we will get back to you as soon as possible",
                ],
            ]);
            //To site admin
            $this->notification->sendEmail([
                "from" => $app['email'],
                "to" => $app['email'],
                "replyTo" => $app['email'],
                "subject" => 'Ticket #' . $ticketNumber . ':' . $contact->getSubject(),
                "text" => 'Ticket #' . $ticketNumber . ':' . $contact->getSubject(),
                "template" => "email/templates/default.html.twig",
                "context" => [
                    'message' => $contact->getMessage(),
                ],
            ]);

            if ($user) {
                $this->notification->sendNotification($user, [
                    'header' => 'Ticket #' . $ticketNumber . ':' . $contact->getSubject(),
                    'body' => "Thank you for getting touch with us, we will get back to you as soon as possible.<br> Ticket #$ticketNumber",
                ]);
            }
            $this->addFlash('notice-success', 'Message received.Thank you for get in touch');
            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);
        }

        return $this->render('web/contact/index.html.twig', [
            'form' => $form,
            'title' => 'Contact Us',
            'permissions' => $this->dashboardServicePermissions->getDashboardPermission($user),
            'site' => $app,

        ]);
    }
    #[Route("/disclaimer", name: "Disclaimer", methods: ["GET"])]

    public function disclaimer(): Response
    {
        return $this->renderPage('web/disclaimer/index.html.twig', [
            'title' => 'Disclaimer',
            'data' => $this->apiService->getInformation(SectionEnum::DISCLAIMER),
        ]);
    }

    #[Route("/privacy", name: "Privacy", methods: ["GET"])]

    public function privacy(): Response
    {
        return $this->renderPage('web/privacy/index.html.twig', [
            'title' => 'Privacy',
            'data' => $this->apiService->getInformation(SectionEnum::PRIVACY),
        ]);
    }

    #[Route("/terms", name: "Terms And Conditions", methods: ["GET"])]

    public function terms(): Response
    {
        return $this->renderPage('web/terms/index.html.twig', [
            'title' => 'Terms and Conditions',
            'data' => $this->apiService->getInformation(SectionEnum::TERMS),
        ]);
    }

    #[Route("/faqs", name: "FAQs", methods: ["GET"])]

    public function FAQs(): Response
    {
        return $this->renderPage('web/faqs/index.html.twig', [
            'title' => 'FAQs',
            'data' => $this->apiService->getInformation(SectionEnum::FAQS),
        ]);
    }
    private function renderPage(String $template, array $data = [], $user = null): Response
    {

        $data['permissions'] = $this->dashboardServicePermissions->getDashboardPermission($user);
        $data['site'] = $this->dashboardServicePermissions->getSiteDetails();
        return $this->render($template, $data);
    }
}
