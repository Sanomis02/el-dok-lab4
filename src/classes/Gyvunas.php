<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 12:49 PM
 */




class Gyvunas
{

    private $id;
    private $pavadinimas;
    private $santraupa;

    /**
     * Gyvunas constructor.
     */
    public function __construct($pavadinimas="",$santraupa="")
    {
        $this->pavadinimas = $pavadinimas;
        $this->santraupa = $santraupa;
    }

    public function sukurtiObjekta($row)
    {
        $this->pavadinimas = $row["pavadinimas"];
        $this->santraupa = $row["santraupa"];
        $this->id = $row["id"];
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPavadinimas()
    {
        return $this->pavadinimas;
    }

    /**
     * @param mixed $pavadinimas
     */
    public function setPavadinimas($pavadinimas)
    {
        $this->pavadinimas = $pavadinimas;
    }

    /**
     * @return mixed
     */
    public function getSantraupa()
    {
        return $this->santraupa;
    }

    /**
     * @param mixed $santraupa
     */
    public function setSantraupa($santraupa)
    {
        $this->santraupa = $santraupa;
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
        return "Mano pirmas gyvuno pavadinimas";
    }
    public function getGyvunoPavadTuscias()
    {
        return "Tokio pavadinimo nera";
    }
    public function getGyvunoSantraupaTuscia()
    {
        return "tuscia";
    }
    public function getGyvunoIdNulis()
    {
        return 0;
    }

}