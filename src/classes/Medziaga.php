<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 12:49 PM
 */

class Medziaga
{
    private $id;
    private $turinys;
    private $fk_tipas;
    private $tipas;

    protected $db;


    public function __construct(PDO $db, $duomenys = '', $fk_tipas = 1)
    {
        $this->db = $db;
        $this->turinys = $duomenys;
        $this->fk_tipas = $fk_tipas;
    }

    public function create($row)
    {
        $this->id = $row["id"];
        $this->turinys = $row["turinys"];
        $this->fk_tipas = $row["fk_tipas"];
        $this->tipas = $this->getTipasFromMedziaga($this->fk_tipas);
        return $this;
    }

    private function getTipasFromMedziaga($fk_tipas)
    {
        $query = $this->db->query('SELECT rusis FROM reklamos_tipai WHERE id='.$fk_tipas);
        $results = $query->fetchColumn();
        return $results;
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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTurinys()
    {
        return $this->turinys;
    }

    public function setTurinys($duomenys)
    {
        $this->turinys = $duomenys;
    }

    public function getFk_tipas()
    {
        return $this->fk_tipas;
    }

    public function getTipas()
    {
        return $this->tipas;
    }

    public function setTipas($fk_tipas)
    {
        $this->tipas = getTipasFromMedziaga($fk_tipas);
    }


    public function setFk_tipas($tipasId)
    {
        $this->fk_tipas = $tipasId;
    }
}
