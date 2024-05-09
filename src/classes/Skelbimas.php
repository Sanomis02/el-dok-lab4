<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 12:49 PM
 */
include_once __DIR__.'/../classes/Anketa.php';

class Skelbimas
{

    private $id;
    private $anketa_id;
    private $autorius_id;
    private $rusies_id;
    private $ar_aktyvus;
    private $skelbimo_data;
    private $galiojimo_laikas;
    private $perziuros_kiekis;
    private $aprasymas;
    public $skelbimo_anketa;
    public $skelbimo_zinutes;

    protected $db;


    public function __construct(PDO $db,$anketa_id=1,$autorius_id=1,$rusies_id=1,$ar_aktyvus=1,$galiojimo_laikas=null,$skelbimo_data=null, $perziuros_kiekis=0,
                                $aprasymas="")
    {
        $this->db = $db;
        $this->anketa_id = $anketa_id;
        $this->autorius_id = $autorius_id;
        $this->rusies_id = $rusies_id;
        $this->ar_aktyvus = $ar_aktyvus;
        $this->skelbimo_data = $skelbimo_data;
        $this->galiojimo_laikas = $galiojimo_laikas;
        $this->perziuros_kiekis = $perziuros_kiekis;
        $this->aprasymas = $aprasymas;
    }

    public function create($row)
    {
        $this->id = $row["id"];
        $this->anketa_id = $row["anketa_id"];
        $this->autorius_id = $row["autorius_id"];
        $this->rusies_id = $row["rusies_id"];
        $this->ar_aktyvus = $row["ar_aktyvus"];
        $this->galiojimo_laikas = date("Y-m-d H:i", strtotime($row["galiojimo_laikas"]));
        $this->skelbimo_data = date("Y-m-d H:i", strtotime($row["skelbimo_data"]));
        $this->perziuros_kiekis = $row["perziuros_kiekis"];
        $this->aprasymas = $row["aprasymas"];
        $this->skelbimo_anketa=$this->getAnketaPagalSkelbimoAnketa($this->anketa_id);
        $this->skelbimo_zinutes=$this->getRelatedZinutes($row["id"]);
//        echo'<pre>';
//        var_dump($this->skelbimo_zinutes);die;
        return $this;
    }

    private function getAnketaPagalSkelbimoAnketa($anketa_skelbime_id)
    {
        $query = $this->db->query('SELECT * FROM Anketos WHERE id='.$anketa_skelbime_id);
        $results = $query->fetch();
        $objektas = new Anketa($this->db);
        $objektas_uzkrautas = $objektas->create($results);

        return $objektas_uzkrautas;

    }

    private function getRelatedZinutes($skelbimo_id)
    {

        $query = $this->db->prepare("SELECT * FROM Zinutes WHERE skelbimo_id=:skelbimo_id");
        $query->bindParam(':skelbimo_id', $skelbimo_id, PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $rowcount = $query->rowCount();
        $all_objects = array();
        if ($rowcount > 0) {
            foreach ($results as $row) {
                $tdo = new Zinute($this->db);
                $all_objects[] = $tdo->create($row);
            }
        }
        return $all_objects;
    }


    public function getGaliojimo_laikas()
    {
        return $this->galiojimo_laikas;
    }

    public function setGaliojimo_laikas($galiojimo_laikas)
    {
        $this->galiojimo_laikas = $galiojimo_laikas;
    }
    public function getSkelbimo_data()
    {
        return $this->skelbimo_data;
    }

    public function setSkelbimo_data($skelbimo_data)
    {
        $this->skelbimo_data = $skelbimo_data;
    }

    public function getPerziuros_kiekis()
    {
        return $this->perziuros_kiekis;
    }

    public function setPerziuros_kiekis($perziuros_kiekis)
    {
        $this->perziuros_kiekis = $perziuros_kiekis;
    }

    public function getanketa_id()
    {
        return $this->anketa_id;
    }

    public function setanketa_id($anketa_id)
    {
        $this->anketa_id=$anketa_id;
    }

    public function getAutorius_id()
    {
        return $this->autorius_id;
    }

    public function setAutorius_id($autorius_id)
    {
        $this->autorius_id = $autorius_id;
    }
    public function getRusies_id()
    {
        return $this->rusies_id;
    }

    public function setRusies_id($rusies_id)
    {
        $this->rusies_id = $rusies_id;
    }
    public function getAprasymas()
    {
        return $this->aprasymas;
    }

    public function setAprasymas($aprasymas)
    {
        $this->aprasymas = $aprasymas;
    }
    public function getAr_aktyvus()
    {
        return $this->ar_aktyvus;
    }

    public function setAr_aktyvus($ar_aktyvus)
    {
        $this->ar_aktyvus = $ar_aktyvus;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}
