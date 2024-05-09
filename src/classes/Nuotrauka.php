<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 12:49 PM
 */




class Nuotrauka
{

    private $id;
    private $anketos_id;
    private $nuotraukos_pav;
    private $ar_rodyti;

    /**
     * Gyvunas constructor.
     */
    public function __construct($anketos_id=0,$nuotraukos_pav="",$ar_rodyti=0)
    {
        $this->anketos_id = $anketos_id;
        $this->nuotraukos_pav = $nuotraukos_pav;
    }

    public function sukurtiObjekta($row)
    {
        $this->anketos_id = $row["anketos_id"];
        $this->nuotraukos_pav = $row["nuotraukos_pav"];
        $this->ar_rodyti = $row["ar_rodyti"];
        $this->id = $row["id"];
        return $this;
    }
    public function getNuotraukosPavPagalId($id_is_anketos)
    {
        $this->id=$id_is_anketos;

//        $this->nuotraukos_pav = $row["nuotraukos_pav"];
//        return self::getNuotraukos_pav();
//        return $this->id;
        return $this->getId();
//        return $id_is_anketos;
    }

    /**
     * @return mixed
     */
    public function getAnketos_id()
    {
        return $this->anketos_id;
    }

    /**
     * @param mixed $anketos_id
     */
    public function setAnketos_id($anketos_id)
    {
        $this->anketos_id = $anketos_id;
    }

    /**
     * @return mixed
     */
    public function getNuotraukos_pav()
    {
        return $this->nuotraukos_pav;
    }

    /**
     * @param mixed $nuotraukos_pav
     */
    public function setNuotraukos_pav($nuotraukos_pav)
    {
        $this->nuotraukos_pav = $nuotraukos_pav;
    }


    /**
     * @return mixed
     */
    public function getAr_rodyti()
    {
        return $this->ar_rodyti;
    }

    /**
     * @param mixed $ar_rodyti
     */
    public function setAr_rodyti($ar_rodyti)
    {
        $this->ar_rodyti = $ar_rodyti;
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
        return "Mano pirmas gyvuno nuotraukos_pav";
    }
    public function getPagrNuotraukaTuscia()
    {
        return "tuscia_pagrindine_nuotrauka.jpg";
    }

}