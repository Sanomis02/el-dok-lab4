<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 12:49 PM
 */

class Vartotojas
{

    private $id;
    private $ar_gali_skelbt;
    private $vardas;
    private $pastas;
    private $slaptazodis;
    private $prieiga;

    /**
     * Vartotojai constructor.
     */
    public function __construct($ar_gali_skelbt=0, $vardas="",$pastas="",$slaptazodis="",$prieiga="")
    {
        $this->ar_gali_skelbt = $ar_gali_skelbt;
        $this->vardas = $vardas;
        $this->pastas = $pastas;
        $this->slaptazodis = $slaptazodis;
        $this->prieiga = $prieiga;
    }

    public function create($row)
    {
        $this->ar_gali_skelbt = $row["ar_gali_skelbt"];
        $this->vardas = $row["vardas"];
        $this->pastas = $row["pastas"];
        $this->slaptazodis = $row["slaptazodis"];
        $this->prieiga = $row["prieiga"];
        $this->id = $row["id"];
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAr_gali_skelbt()
    {
        return $this->ar_gali_skelbt;
    }

    /**
     * @param mixed $ar_gali_skelbt
     */
    public function setAr_gali_skelbt($ar_gali_skelbt)
    {
        $this->ar_gali_skelbt = $ar_gali_skelbt;
    }

    /**
     * @return mixed
     */
    public function getVardas()
    {
        return $this->vardas;
    }

    /**
     * @param mixed $vardas
     */
    public function setVardas($vardas)
    {
        $this->vardas = $vardas;
    }

    /**
     * @return mixed
     */
    public function getPastas()
    {
        return $this->pastas;
    }

    /**
     * @param mixed $pastas
     */
    public function setPastas($pastas)
    {
        $this->pastas = $pastas;
    }

    /**
     * @return mixed
     */
    public function getSlaptazodis()
    {
        return $this->slaptazodis;
    }

    /**
     * @param mixed $slaptazodis
     */
    public function setSlaptazodis($slaptazodis)
    {
        $this->slaptazodis = $slaptazodis;
    }

    /**
     * @return mixed
     */
    public function getPrieiga()
    {
        return $this->prieiga;
    }

    /**
     * @param mixed $prieiga
     */
    public function setPrieiga($prieiga)
    {
        $this->prieiga = $prieiga;
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
        return "Pirmas Vartotojas";
    }

}