<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 1:42 PM
 */
include_once __DIR__.'/../classes/Anketa.php';
class HomeController
{
    protected $view;
    protected $db;
    protected $app;
    protected $flash;
    private $data=array();

    public function __construct(\Slim\Views\Twig $view, PDO $db, \Slim\Flash\Messages $flash)
    {
        $this->view = $view;
        $this->db = $db;
        $this->flash = $flash;
    }

    public function home($request, $response, $args)
    {
        try {
            if (isset($_SESSION["ateita_is_paieskos_home"])) {
                unset($_SESSION["ateita_is_paieskos_home"]);
            }

            $this->defaultData();
            $this->addData("prieiga", $_SESSION["prieiga"]);
            $this->addData("sess_paiesk_fragm_home", $_SESSION["sess_paiesk_fragm_home"]);
//        var_dump($this->getAllSkelbimus());die;
            $this->addData("skelbimai", $this->getAllNaujauiusSkelbimus());
            /*----------patikrinimas rezultato------------*/
//        echo'<pre>';
//        var_dump($this->getData());die;
            /*--------------------------------------------*/
            $this->addData("successMessage",$this->getFlashMessage("success")[0]);
            $this->view->render($response,'home_main.twig',$this->getData());

        } catch (\DomainException $e) {
            throw new DomainException($e->getMessage(), null, $e);
        }

        return $response;
    }


    private function getAllNaujauiusSkelbimus()
    {
        $query = $this->db->query('SELECT * FROM Skelbimai order by id desc');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $all_objects = array();
        foreach ($results as $row){
            $tdo = new Skelbimas($this->db);
            $all_objects[] = $tdo->create($row);
        }
        return $all_objects;
    }









    public function login($request, $response, $args)
    {
        $this->addData("pageTitle","įeiti to Projektą Gyvūnėliai");
        $data = $this->getData();
        $this->view->render($response,'login.twig',$data);
        return $response;
    }

    public function authenticateUser($request, $response, $args)
    {
        if($request->isPost()){
            $allPostPutVars = $request->getParsedBody();
            // return $response->withRedirect('/',301);
            var_dump($allPostPutVars);
        }
    }

    public function create($request, $response, $args)
    {
        $this->addData("pageTitle","Slim Todo - Create Task");
        $data = $this->getData();
        $this->view->render($response,'create.twig',$data);
        return $response;
    }




    public function viewSearchHome($request, $response, $args)
    {
//                echo'<pre>';
//        var_dump($_SESSION["paiesk_fragm_sesija"]);die;
        if (isset($_SESSION["sess_paiesk_fragm_home"])) {
            unset($_SESSION["sess_paiesk_fragm_home"]);
        }
        $_SESSION["sess_paiesk_fragm_home"]=$args['fragment'];//isimenam nauja fragmenta
        $_SESSION["ateita_is_paieskos_home"]="taip";
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("ateita_is_paieskos_home", $_SESSION["ateita_is_paieskos_home"]);
        $this->addData("sess_paiesk_fragm_home", $_SESSION["sess_paiesk_fragm_home"]);
//                echo'<pre>';
//        var_dump($args['fragment']);die;

        $search_result = $this->getSearchResult($args['fragment']);
        $this->addData("skelbimai", $search_result);
        $this->view->render($response,'home_main.twig',$this->getData());
        return $response;
    }

    public function passSearchHome($request, $response, $args)
    {
        if($request->isPost()) {
            $postValues = $request->getParsedBody();
            if ($postValues["paieska"]){//paspausta post paieska netuscia
                $sess_paiesk_fragm_home = $postValues["paieska"];
            }else{
                //paspausta su tusciu paieskos lauku - nunulinam sesija ir pagrazinam namo
                if (isset($_SESSION["sess_paiesk_fragm_home"])) {
                    unset($_SESSION["sess_paiesk_fragm_home"]);
                }
                return $response->withRedirect("/",301);
            }

        }
        return $response->withRedirect("/paieska/$sess_paiesk_fragm_home",301);
    }


    private function defaultData()
    {
        $this->addData("pageTitle", "Projektas gyvunai");
    }

    private function addData($key, $value)
    {
        $this->data[$key] = $value;
    }

    private function getData()
    {
        return $this->data;
    }

    private function getAnketas()
    {
        $query = $this->db->query('SELECT * FROM Anketos');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $anketos = array();
        foreach ($results as $row){
            $tdo = new Anketa($this->db);
            $anketos[] = $tdo->create($row);
        }
        return $anketos;
    }
    private function getSearchResult($searchFragment)
    {
        $searchFragment = '%' . $searchFragment . '%';
        $query = $this->db->prepare("SELECT Skelbimai.*, Rusys.pavadinimas FROM Skelbimai INNER JOIN Rusys ON Rusys.id=Skelbimai.rusies_id WHERE 
            (Rusys.pavadinimas LIKE :searchFragment OR Skelbimai.aprasymas LIKE :searchFragment) ORDER BY Skelbimai.id DESC ");
        $query->bindParam(':searchFragment', $searchFragment, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);;
        $rowcount = $query->rowCount();
        $Skelbimai = array();
        if ($rowcount > 0) {
            foreach ($results as $row){
                $tdo = new Skelbimas($this->db);
                $Skelbimai[] = $tdo->create($row);
            }
        }
        return $Skelbimai;
    }


    private function addFlashMessage($key, $message)
    {
        $this->flash->addMessage($key, $message);
    }

    private function getFlashMessage($key)
    {
        return $this->flash->getMessage($key);
    }

}