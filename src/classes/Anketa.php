<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 12:49 PM
 */
include_once __DIR__.'/../classes/Nuotrauka.php';
include_once __DIR__.'/../classes/Gyvunas.php';

class Anketa
{

    private $id;
    private $gyvuno_amzius;
    private $ar_Rastas;

    public $autorius_id;

    public $pagr_nuotraukos_id;
    private $miestas;
    private $rajonas;
    private $gatve;
    private $gyvuno_vardas;
    private $lytis;
    private $apskritis;
    public $fk_Naudotojasid;

    public $fk_rusies_id;
    private $aprasymas;
    private $dingimo_data;

    public $pagr_nuotraukos_pav;

    public $gyvuno_pav;
    public $gyvuno_santraupa;
    public $gyvuno_id;

    protected $db;
//    private $ntr_pvd;

    /**
     *  Anketos konstruktorius
     */
    public function __construct(PDO $db, $gyvuno_amzius = 0, $ar_Rastas=0,$autorius_id=0, $pagr_nuotraukos_id=0, $miestas = "",
                                $rajonas="", $gatve="", $gyvuno_vardas="", $lytis="", $apskritis="", $fk_Naudotojasid=0, $fk_rusies_id=0, $aprasymas="", $dingimo_data=null)
    {
        $this->db = $db;
        $this->gyvuno_amzius = $gyvuno_amzius;
        $this->ar_Rastas = $ar_Rastas;
        $this->autorius_id = $autorius_id;
        $this->pagr_nuotraukos_id = $pagr_nuotraukos_id;
        $this->miestas= $miestas;
        $this->rajonas=$rajonas;
        $this->gatve=$gatve;
        $this->gyvuno_vardas=$gyvuno_vardas;
        $this->lytis=$lytis;
        $this->apskritis=$apskritis;
        $this->fk_Naudotojasid=$fk_Naudotojasid;
        $this->fk_rusies_id=$fk_rusies_id;
        $this->aprasymas=$aprasymas;
        $this->dingimo_data=$dingimo_data;
    }

    public function create($row)
    {
        $this->id=$row["id"];
        $this->gyvuno_amzius = $row["gyvuno_amzius"];
        $this->ar_Rastas = $row["ar_Rastas"];
        $this->autorius_id = $row["autorius_id"];
        $this->pagr_nuotraukos_id = $row["pagr_nuotraukos_id"];
        $this->pagr_nuotraukos_pav = $this->getPagrNuotraukosPavadinimas($this->pagr_nuotraukos_id);
        $this->miestas= $row["miestas"];
        $this->rajonas=$row["rajonas"];
        $this->gatve=$row["gatve"];
        $this->gyvuno_vardas=$row["gyvuno_vardas"];
        $this->lytis=$row["lytis"];
        $this->apskritis=$row["apskritis"];
        $this->fk_Naudotojasid=$row["fk_Naudotojasid"];
        $this->fk_rusies_id=$row["fk_rusies_id"];
        $this->gyvuno_pav = $this->getRusiesPavadinimas($this->fk_rusies_id);
        $this->gyvuno_santraupa = $this->getRusiesSantraupa($this->fk_rusies_id);
        $this->gyvuno_id = $this->getRusiesId($this->fk_rusies_id);
        $this->aprasymas=$row["aprasymas"];//suformatuojam data 2023-09-25 09:55 formatu
        $this->dingimo_data=date("Y-m-d H:i", strtotime($row["dingimo_data"]));
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getGyvuno_amzius()
    {
        return $this->gyvuno_amzius;
    }

    public function setGyvuno_amzius($gyvuno_amzius)
    {
        $this->gyvuno_amzius = $gyvuno_amzius;
    }

    public function getAr_Rastas()
    {
        return $this->ar_Rastas;
    }

    public function setAr_Rastas($ar_Rastas)
    {
        $this->ar_Rastas=$ar_Rastas;
    }

    public function setAutorius_id($autorius_id)
    {
        $this->autorius_id=$autorius_id;
    }

    public function getAutorius_id()
    {
        return $this->autorius_id;
    }

    public function getPagr_nuotraukos_id()
    {
        return $this->pagr_nuotraukos_id;
    }

    public function setPagr_nuotraukos_id($pagr_nuotraukos_id)
    {
        $this->pagr_nuotraukos_id = $pagr_nuotraukos_id;
    }

    public function getPagrNuotraukosPavadinimas($id_is_anketos)
    {

        $query = $this->db->prepare("SELECT * FROM gyvunu_nuotraukos WHERE id=:id_is_anketos");
        $query->bindParam(':id_is_anketos', $id_is_anketos, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch();
        $nuotrauka = new Nuotrauka();
        $nuotrauka->sukurtiObjekta($result);
        return $nuotrauka->getNuotraukos_pav() ? : $nuotrauka->getPagrNuotraukaTuscia();
    }
    public function getRusiesPavadinimas($id_is_anketos)
    {

        $query = $this->db->prepare("SELECT * FROM Rusys WHERE id=:id_is_anketos");
        $query->bindParam(':id_is_anketos', $id_is_anketos, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch();
        $gyvunas = new Gyvunas();
        $gyvunas->sukurtiObjekta($result);
        return $gyvunas->getPavadinimas() ? : $gyvunas->getGyvunoPavadTuscias();
    }

    public function getRusiesSantraupa($id_is_anketos)
    {

        $query = $this->db->prepare("SELECT * FROM Rusys WHERE id=:id_is_anketos");
        $query->bindParam(':id_is_anketos', $id_is_anketos, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch();
        $gyvunas = new Gyvunas();
        $gyvunas->sukurtiObjekta($result);
        return $gyvunas->getSantraupa() ? : $gyvunas->getGyvunoSantraupaTuscia();
    }
    public function getRusiesId($id_is_anketos)
    {

        $query = $this->db->prepare("SELECT * FROM Rusys WHERE id=:id_is_anketos");
        $query->bindParam(':id_is_anketos', $id_is_anketos, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch();
        $gyvunas = new Gyvunas();
        $gyvunas->sukurtiObjekta($result);
        return $gyvunas->getId() ? : $gyvunas->getGyvunoIdNulis();
    }

    public function setMiestas($miestas)
    {
        $this->miestas = $miestas;
    }

    public function getMiestas()
    {
        return $this->miestas;
    }

    public function setRajonas($rajonas)
    {
        $this->rajonas=$rajonas;
    }

    public function getRajonas()
    {
        return $this->rajonas;
    }

    public function getGatve()
    {
        return $this->gatve;
    }

    public function setGatve($gatve)
    {
        $this->gatve = $gatve;
    }

    public function setGyvuno_vardas($gyvuno_vardas)
    {
        $this->gyvuno_vardas = $gyvuno_vardas;
    }

    public function getGyvuno_vardas()
    {
        return $this->gyvuno_vardas;
    }

    public function setLytis($lytis)
    {
        $this->lytis = $lytis;
    }

    public function getLytis()
    {
        return $this->lytis;
    }

    public function getApskritis()
    {
        return $this->apskritis;
    }

    public function setApskritis($apskritis)
    {
        $this->apskritis = $apskritis;
    }

    public function setAprasymas($aprasymas)
    {
        $this->aprasymas = $aprasymas;
    }

    public function getAprasymas()
    {
        return $this->aprasymas;
    }
    public function setDingimo_data($dingimo_data)
    {
        $this->dingimo_data = $dingimo_data;
    }

    public function getDingimo_data()
    {
        return $this->dingimo_data;
    }
}
