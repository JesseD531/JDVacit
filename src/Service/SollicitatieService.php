<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Sollicitatie;
use App\Entity\User;
use App\Entity\Vacature;

class SollicitatieService{

    private $sollRep;
    private $uRep;
    private $vacRep;
    
    public function __construct(EntityManagerInterface $em){
        $this->sollRep = $em->getRepository(Sollicitatie::class);
        $this->uRep = $em->getRepository(User::class);
        $this->vacRep = $em->getRepository(Vacature::class);
    }

    public function ophalenSollicitatiePerGebruiker($user_wn_id){

        $user = $this->uRep->find($user_wn_id);
        $data = $this->sollRep->ophalenSollicitatiePerGebruiker($user);
        
        return($data);
    }

    public function toevoegenSollicitatie($soll){
        
        //Repositories in de service zodat het geen spaghetti in de repositories zelf wordt.
        //Dit biedt ook meer flexibiliteit in de code.

        $userWN = $this->uRep->find($soll["user_wn_id"]);
        $vacature = $this->vacRep->find($soll["vacature_id"]);

        $new_soll = $this->sollRep->opslaanSollicitatie($soll, $userWN, $vacature);

        return($new_soll);
    }

    public function verwijderSollicitatie($soll_id){

        $soll = $this->sollRep->verwijderSollicitatie($soll_id);
        
        return($soll);
    }

    public function updateSollicitatie($soll_id){
        
        $data = $this->sollRep->verstuurUitnodiging($soll_id);

        return($data);
    }

    public function updateMotivatie($soll_id, $new_mot){
        
        $data = $this->sollRep->veranderMotivatie($soll_id, $new_mot);

        return($data);
    }
}