<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 1:42 PM
 */

//session_start();


include_once __DIR__.'/../classes/Vartotojas.php';
include_once __DIR__.'/../PdoWrapper.php';

class VartotojasController
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


    public function login($request, $response, $args)
    {
        $this->addData("pageTitle","Reklamos projektai");
        $this->addData("loginFailedMessage",$this->getFlashMessage("danger")[0]);
        $data = $this->getData();
        $this->view->render($response,'login.twig',$data);
        return $response;
    }

    public function authenticateUser($request, $response, $args)
    {
        if (isset($_SESSION["prijungtas"])) {
            unset($_SESSION["prijungtas"]);
        }

        if($request->isPost()){

            $authIsFormos = $request->getParsedBody();
            $vardas = $authIsFormos["name"];
            $slaptazodis = $authIsFormos["slaptazodis"];

            try {
                $this->db = new PDO('mysql:host=animals_mysql;dbname=animals', $vardas, $slaptazodis);
                $_SESSION["prijungtas"] = 1;

                $_SESSION['vardas'] = $vardas;
                $_SESSION['slaptazodis'] = $slaptazodis;

                return $response->withRedirect('/',301);
            } catch(PDOException $e) {
                $this->addFlashMessage("danger", "Neteisingi prisijungimo duomenys");
                return $response->withRedirect('/login', 301);
            }
        }
    }

    public function iseiti($request, $response, $args)
    {
        unset($_SESSION["prieiga"]);
        unset($_SESSION['vardas']);
        unset($_SESSION['slaptazodis']);
        return $response->withRedirect('/', 301);
    }

    private function checkVartotoja($pastas, $slaptazodis)
    {
        $query = $this->db->prepare('SELECT * FROM Vartotojai WHERE `pastas`=:pastas AND `slaptazodis`=:slaptazodis');
        $query->execute([
            "pastas"=>$pastas,
            "slaptazodis"=>$slaptazodis]);
        $result = $query->fetch();

        if($result["id"] >= 1)
        {
            $tikrinamas = new Vartotojas();
            return $tikrinamas->create($result);
        }
        else
            return NULL;
    }

    public function create($request, $response, $args)
    {
        $this->addData("pageTitle","Slim Todo - Create Task");
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $data = $this->getData();
        $this->view->render($response,'create.twig',$data);
        return $response;
    }


    public function loadToFormForAdd($request, $response, $args)
    {
        $this->defaultData();
        $this->addData("successMessage",$this->getFlashMessage("success")[0]);
        $data = $this->getData();
        $this->view->render($response,'vartotojai\create_new.twig',$data);
        return $response;
    }




    public function addFromForm($request, $response, $args)
    {
        if($request->isPost()) {
            $postValues = $request->getParsedBody();
            $vardas = $postValues["vardas"];
            $pastas = $postValues["pastas"];
            $slaptazodis = $postValues["slaptazodis"];
            $this->createVartotojas($vardas, $pastas, $slaptazodis);

            $_SESSION["prieiga"] = "Prisiregistraves";

            $this->addFlashMessage("success", "Sekmingai uzregistruota");
            return $response->withRedirect('/', 301);

        }
    }

    public function add($request, $response, $args)
    {
        if($request->isPost()){
            $postValues = $request->getParsedBody();
            $name = $postValues["task"];
            $details = $postValues["details"];
            $author = $postValues["author"];
            $this->createTask($name, $details, $author);
            $this->addFlashMessage("success","Task Created");
            return $response->withRedirect('/',301);
        }
    }

    public function edit($request, $response, $args)
    {
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $vartotojas = $this->getVartotoja($args['id']);
        $this->addData("vartotojas", $vartotojas);
        $this->addData("successMessage",$this->getFlashMessage("success")[0]);
        $this->view->render($response,'vartotojai/edit_one.twig', $this->getData());
        return $response;
    }
    public function update($request, $response, $args)
    {
        if($request->isPost()) {
            $postValues = $request->getParsedBody();
            $ar_gali_skelbt = $postValues["ar_gali_skelbt"];
            $vardas = $postValues["vardas"];
            $pastas = $postValues["pastas"];
            $slaptazodis = $postValues["slaptazodis"];
            $prieiga = $postValues["prieiga"];
            $this->updateVartotoja($ar_gali_skelbt, $vardas, $pastas, $slaptazodis, $prieiga, $args["id"]);
            $this->addFlashMessage("success","Vartotojas atnaujintas");
            return $response->withRedirect("/redaguoti-vartotoja/{$args['id']}",301);
        }

    }

    public function viewAll($request, $response, $args)
    {
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("vartotojai", $this->getVartotojus());
        $this->addData("successMessage",$this->getFlashMessage("success")[0]);
        $this->view->render($response,'vartotojai/view_all.twig',$this->getData());
        return $response;
    }

    public function view($request, $response, $args)
    {
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $vartotojas = $this->getVartotoja($args['id']);
        $this->addData("vartotojas", $vartotojas);
        $this->view->render($response,'vartotojai/view_one.twig',$this->getData());
        return $response;
    }

    public function quickAdd($request, $response, $args)
    {
        if($request->isPost() && $request->isXhr()) {
            $body = $request->getParsedBody();
            if(isset($body["task"])){
                $this->createTask($body["task"],$body["task"],"");
            }
        }else{
            return $response->write("You Shouldn't be here");
        }
    }

    public function delete($request, $response, $args)
    {
        if($request->isPost() && $request->isXhr()) {
            $body = $request->getParsedBody();
            if(isset($body["taskId"])){
                $this->deleteTask($body["taskId"]);
            }
        }
    }

    private function defaultData()
    {
        $this->addData("pageTitle", "Projektas Gūvūnų radimas");
    }

    private function addData($key, $value)
    {
        $this->data[$key] = $value;
    }

    private function getData()
    {
        return $this->data;
    }

    private function getVartotojus()
    {
        $query = $this->db->query('SELECT * FROM Vartotojai');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $vartotojai = array();
        foreach ($results as $row){
            $tdo = new Vartotojas();
            $vartotojai[] = $tdo->create($row);
        }
        return $vartotojai;
    }

    private function getVartotoja($id)
    {
        $query = $this->db->query('SELECT * FROM Vartotojai WHERE id='.$id);
        $query->execute();
        $result = $query->fetch();
        $vartotojas = new Vartotojas();
        return $vartotojas->create($result);
    }

    private function createTask($name, $details, $author)
    {
        $query= $this->db->prepare("INSERT INTO slim_tasks(name, details, author)
          VALUES(:name, :details, :author)");
        $query->execute([
           "name" => $name,
           "details" => $details,
           "author" => $author
        ]);
    }

    private function createVartotojas($vardas, $pastas, $slaptazodis)
    {
        $query= $this->db->prepare("INSERT INTO Vartotojai(ar_gali_skelbt, vardas, pastas, slaptazodis, prieiga)
          VALUES(0, :vardas, :pastas, :slaptazodis, :prieiga)");
        $query->execute([
            "vardas" => $vardas,
            "pastas" => $pastas,
            "slaptazodis" => $slaptazodis,
            "prieiga" => "Prisiregistraves"
        ]);
    }

    private function updateVartotoja( $ar_gali_skelbt, $vardas, $pastas, $slaptazodis, $prieiga, $id )
    {
        $query= $this->db->prepare("UPDATE Vartotojai SET `ar_gali_skelbt`=:ar_gali_skelbt,`vardas`=:vardas,`pastas`=:pastas, `slaptazodis`=:slaptazodis, `prieiga`=:prieiga WHERE id=". $id);
        $query->execute([
            "ar_gali_skelbt" => $ar_gali_skelbt,
            "vardas" => $vardas,
            "pastas" => $pastas,
            "slaptazodis" => $slaptazodis,
            "prieiga" => $prieiga,

        ]);
    }

    private function deleteTask($id)
    {
        $query = $this->db->prepare("DELETE FROM slim_tasks WHERE task_id=:id");
        $query->execute([
            "id" => $id,
        ]);
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