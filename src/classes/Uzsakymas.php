<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 12:49 PM
 */
include_once __DIR__.'/../classes/Medziaga.php';

class Uzsakymas
{

    private $id;
    private $uzsakovas;
    private $reklamos_medziagos;
    protected $db;


    public function __construct(PDO $db,$id=1,$uzsakovas='')
    {
        $this->db = $db;
        $this->id = $id;
        $this->uzsakovas = $uzsakovas;
        $this->reklamos_medziagos = $this->getVisasReklamosMedziagas($id);
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

    private function getVisasReklamosMedziagas($uzsakymo_id)
    {
        $query = $this->db->query("SELECT reklamos_medziagos.turinys AS 'turinys', reklamos_tipai.id AS 'tipas' FROM
        reklamos_uzsakymai INNER JOIN reklamos_uzsakymu_paketai ON reklamos_uzsakymai.id = reklamos_uzsakymu_paketai.fk_uzsakymas
        INNER JOIN reklamos_paketai ON reklamos_uzsakymu_paketai.fk_paketas = reklamos_paketai.id
        INNER JOIN reklamos_medziagos ON reklamos_paketai.fk_medziaga = reklamos_medziagos.id
        INNER JOIN reklamos_tipai ON reklamos_medziagos.fk_tipas = reklamos_tipai.id
        WHERE reklamos_uzsakymai.id =:uzsakymo_id");
        $query->bindParam(':uzsakymo_id', $uzsakymo_id, PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $rowcount = $query->rowCount();
        $all_objects = array();
        if ($rowcount > 0) {
            foreach ($results as $row) {
                $tdo = new Medziaga($this->db);
                $all_objects[] = $tdo->create($row);
            }
        }
        return $all_objects;

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


}
