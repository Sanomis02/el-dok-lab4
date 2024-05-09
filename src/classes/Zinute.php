<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 12:49 PM
 */

class Zinute
{

    private $id;
    private $autoriaus_id;
    private $skelbimo_id;
    private $zinutes_data;
    private $ar_uzblokuota;
    private $turinys;
    public $zinutes_autorius;

    protected $db;

    /**
     * Zinute constructor.
     */
    public function __construct(PDO $db,$autoriaus_id=0, $skelbimo_id=0,$zinutes_data="",$ar_uzblokuota=1,$turinys="")
    {
        $this->db = $db;
        $this->autoriaus_id = $autoriaus_id;
        $this->skelbimo_id = $skelbimo_id;
        $this->zinutes_data = $zinutes_data;
        $this->ar_uzblokuota = $ar_uzblokuota;
        $this->turinys = $turinys;
    }

    public function create($row)
    {
        $this->id = $row["id"];
        $this->autoriaus_id = $row["autoriaus_id"];
        $this->skelbimo_id = $row["skelbimo_id"];
        $this->zinutes_data = date("Y-m-d H:i", strtotime($row["zinutes_data"]));
        $this->ar_uzblokuota = $row["ar_uzblokuota"];
        $this->turinys = $row["turinys"];
        $this->zinutes_autorius=$this->getZinutesAutorius($row["autoriaus_id"]);

        return $this;
    }

    private function getZinutesAutorius($autoriaus_id)
    {
        $query = $this->db->query('SELECT * FROM Vartotojai WHERE id='.$autoriaus_id);
        $results = $query->fetch();
        $objektas = new Vartotojas();
        $objektas_uzkrautas = $objektas->create($results);

        return $objektas_uzkrautas;

    }






    /**
     * @return mixed
     */
    public function getAutoriaus_id()
    {
        return $this->autoriaus_id;
    }

    /**
     * @param mixed $autoriaus_id
     */
    public function setAutoriaus_id($autoriaus_id)
    {
        $this->autoriaus_id = $autoriaus_id;
    }

    /**
     * @return mixed
     */
    public function getSkelbimo_id()
    {
        return $this->skelbimo_id;
    }

    /**
     * @param mixed $skelbimo_id
     */
    public function setSkelbimo_id($skelbimo_id)
    {
        $this->skelbimo_id = $skelbimo_id;
    }

    /**
     * @return mixed
     */
    public function getZinutes_data()
    {
        return $this->zinutes_data;
    }

    /**
     * @param mixed $zinutes_data
     */
    public function setZinutes_data($zinutes_data)
    {
        $this->zinutes_data = $zinutes_data;
    }


    /**
     * @return mixed
     */
    public function getAr_uzblokuota()
    {
        return $this->ar_uzblokuota;
    }

    /**
     * @param mixed $ar_uzblokuota
     */
    public function setAr_uzblokuota($ar_uzblokuota)
    {
        $this->ar_uzblokuota = $ar_uzblokuota;
    }


    /**
     * @return mixed
     */
    public function getTurinys()
    {
        return $this->turinys;
    }

    /**
     * @param mixed $turinys
     */
    public function setTurinys($turinys)
    {
        $this->turinys = $turinys;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }





    public function getName()
    {
        return "My first Tod";
    }

}