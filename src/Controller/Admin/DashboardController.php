<?php

namespace App\Controller\Admin;

use App\Entity\AppSettings;
use App\Entity\AppUserNotification;
use App\Entity\Car;
use App\Entity\InformationCenter;
use App\Entity\Levy;
use App\Entity\Rental;
use App\Entity\UserAdmin;
use App\Entity\UserCarAdditional;
use App\Entity\UserCarAvailableItem;
use App\Entity\UserCarIssue;
use App\Entity\UserCarMissingItem;
use App\Entity\UserDrivingRestriction;
use App\Entity\UserSetting;
use App\Entity\UserTripChecklist;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
#[Route("/admin", name:"admin")]    
    public function index(): Response
    {
        // redirect to some CRUD controller
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($routeBuilder->setController(UserAdminCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Car rental Admin');
    }

    public function configureMenuItems(): iterable
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Accounts');
        yield MenuItem::linkToCrud('Users', 'fas fa-list', UserAdmin::class);
        yield MenuItem::section('Settings');       
        yield MenuItem::linkToCrud('Setting', 'fas fa-list', UserSetting::class); 
        yield MenuItem::linkToCrud('Rentals', 'fas fa-list', Rental::class);
        yield MenuItem::linkToCrud('Vehicles', 'fas fa-list', Car::class);
        yield MenuItem::linkToCrud('Car Issues', 'fas fa-list', UserCarIssue::class);
        yield MenuItem::linkToCrud('Car Additional', 'fas fa-list', UserCarAdditional::class);
        yield MenuItem::linkToCrud('Car Available Items', 'fas fa-list', UserCarAvailableItem::class);
        yield MenuItem::linkToCrud('Car Missing Items', 'fas fa-list', UserCarMissingItem::class);
        yield MenuItem::linkToCrud('User Driving Restriction', 'fas fa-list', UserDrivingRestriction::class);
        yield MenuItem::linkToCrud('Trip Checklists', 'fas fa-list', UserTripChecklist::class);        
        yield MenuItem::section('App Settings');  
        yield MenuItem::linkToCrud('Settings', 'fas fa-list', AppSettings::class);
        yield MenuItem::linkToCrud('Levy', 'fas fa-list', Levy::class);
        yield MenuItem::linkToCrud('Notifications', 'fas fa-list', AppUserNotification::class);
        yield MenuItem::linkToCrud('Information center', 'fas fa-list', InformationCenter::class);
 
    }
}
