<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Service\SollicitatieService;
use App\Service\VacatureService;
use App\Service\UserService;


class ProfielWGController extends AbstractController
{

    private $ss;
    private $vs;
    private $us;

    public function __construct(SollicitatieService $ss, VacatureService $vs, UserService $us){

        $this->ss = $ss;
        $this->vs = $vs;
        $this->us = $us;
    }

    public function index(): Response
    {
        
    }
    /**
     * @Route("/profielWG/prof", name="profielWG")
     */
    public function ophalenProfiel(){//Mijn ProfielWG

        $user = $this->getUser();

        return($this->render('profiel_wg/mijn_profielWG.html.twig', ['user' => $user]));
    }
    /**
     * @Route("/bijwerkenWG", name="bijwerkenWG")
     */
    public function bijwerkenProfiel(){//Mijn_ProfielWG

        $user = $this->userToDatabase($_POST);
        $currentUser = $this->getUser();
        $id = ["id" => $currentUser->getId()];
        $userWG = array_merge($id, $user);

        $user2 = $this->us->toevoegenUser($userWG);

        return($this->redirectToRoute('profielWG'));
    }

    /**
     * @Route("/formvac", name="formvac")
     */
    public function redirectNaarFormulier(){//route naar formulierpagina

        return($this->render('profiel_wg/vactoe.html.twig'));
    }

    /**
     * @Route("/vactoe", name="vactoe")
     */
    public function uploadVacature(){//na uploaden redirecten naar Homepage

        //user is degene die vacature uploadt.
        //vacature wordt op dit tijdstip geuploadt.

        $user = $this->getUser();
        $uid = $user->getId();

        //POST-variable komt hier terecht

        $vaca = [
            "titel" => $_POST['titel'],
            "omschrijving" => $_POST['omschrijving'],
            "niveau" => $_POST['niveau'],
            "datum" => new \DateTime(date('Y/m/d h:i:s a', time())),
            "user_wg_id" => $uid,
            "platform_id" => $_POST['platform']
        ];

        $data = $this->vs->toevoegenVacature($vaca);

        return($this->redirectToRoute('homepage'));
    }

    /**
     * @Route("/vacsol", name="vacsol")
     */
    public function uitnodigingVersturen(){//Mijn_Vacatures_Sollicitaties

        $soll_id = $_POST['sol_id'];
        $data = $this->ss->updateSollicitatie($soll_id);
        
        return new JSONResponse(['success'=>'true']);
    }
    /**
     * @Route("/profielWG/vac", name="mijn_vac")
     */
    public function ophalenVacatures(){//Mijn_Vacatures

        //Vacatures per bedrijf (FK collection)

        $user = $this->getUser();
        $data = $user->getVacatures();

        return($this->render('profiel_wg/mijn_vacatures.html.twig', ['data' => $data]));
    }
    /**
     * @Route("/profielWG/vac/{vac_id}", name="mijn_vacsol")
     */
    public function ophalenSollicitatiesPerVacature($vac_id){

        //Alle sollicitaties op een bepaalde vacature (FK collection)

        $vacature = $this->vs->ophalenVacature($vac_id);
        $solls = $vacature->getSollicitaties();

        return($this->render('profiel_wg/mijn_vacatures_sollicitaties.html.twig', ['data' => $solls]));
    }

    private function userToDatabase($array){

        $user = [
            'email' => $array['email'],
            'roles' => ["ROLE_COMPANY"],
            'password' => $array['password'],
            'record_type' => 'WG',
            'gebruikersnaam' => $array['gebruikersnaam'],
            'adres' => $array['adres'],
            'geboortedatum' => new \DateTime($array['geboortedatum']),
            'telefoonnummer' => $array['telefoonnummer'],
            'postcode' => $array['postcode'],
            'woonplaats' => $array['woonplaats']
        ];

        return($user);
    }
}
