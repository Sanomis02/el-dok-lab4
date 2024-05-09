<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 1:42 PM
 */
include_once __DIR__ . '/../classes/Gyvunas.php';



class GyvunasController
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

    public function loadToFormForAdd($request, $response, $args)
    {
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("successMessage",$this->getFlashMessage("success")[0]);
        $data = $this->getData();
        $this->view->render($response,'gyvunai\create_new.twig',$data);
        return $response;
    }

    public function addFromForm($request, $response, $args)
    {
        if($request->isPost()){
            $postValues = $request->getParsedBody();
            $pavadinimas = $postValues["pavadinimas"];
            $this->addNewRecord($pavadinimas);
            $this->addFlashMessage("success","gyvuno rūšis ukurta jo pavadinimas {$pavadinimas}");
            return $response->withRedirect('/sukurti-gyvuna',301);
        }
    }

    public function loadToFormForEdit($request, $response, $args)
    {
//        $this->addData("pageTitle","Redaguoti gyvuną");
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $gyvunas = $this->getOneRecord($args['id']);
        $this->addData("todo", $gyvunas);
        $this->addData("successMessage",$this->getFlashMessage("success")[0]);
        $this->view->render($response,'gyvunai\edit_one.twig', $this->getData());
        return $response;
    }

    public function updateFromForm($request, $response, $args)
    {
        if($request->isPost()) {
            $postValues = $request->getParsedBody();
            $pavadinimas = $postValues["pavadinimas"];
            $this->atnaujintiGyvuna($pavadinimas, $args["id"]);
            $this->addFlashMessage("success","Gyvunas atnaujintas jo id {$args['id']} jo pavadinimas {$pavadinimas}");
            return $response->withRedirect("/redaguoti-gyvuna/{$args['id']}",301);
        }

    }


    public function viewAll($request, $response, $args)
    {
//        $gyvunas = new Gyvunas();
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("todos", $this->getAllRecords());
        $this->addData("successMessage",$this->getFlashMessage("success")[0]);
        $this->view->render($response,'gyvunai/view_all.twig',$this->getData());
        return $response;
    }


    public function viewOne($request, $response, $args)
    {
//        $this->addData("pageTitle","Gyvunas");
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $gyvunas = $this->getOneRecord($args['id']);
        $this->addData("todo", $gyvunas);
        $this->view->render($response,'gyvunai/view_one.twig',$this->getData());
        return $response;
    }

    public function quickAddOneFromForm($request, $response, $args)
    {
        if($request->isPost() && $request->isXhr()) {
            $body = $request->getParsedBody();
            if(isset($body["pavadinimas"])){
                $this->addNewRecord($body["pavadinimas"]);
            }
        }else{
            return $response->write("Tai negerai - neturėtumėte būti čia");
        }
    }

    public function deleteFromForm($request, $response, $args)
    {
        if($request->isPost() && $request->isXhr()) {
            $body = $request->getParsedBody();
            if(isset($body["gyvunoId"])){
//                print_r($body["gyvunoId"]);die;
                $this->deleteOne($body["gyvunoId"]);
            }
        }
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

    private function getAllRecords()
    {
        $query = $this->db->query('SELECT * FROM Rusys');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $gyvunai = array();
        foreach ($results as $row){
            $gyvunas = new Gyvunas();
            $gyvunai[] = $gyvunas->sukurtiObjekta($row);
        }
        return $gyvunai;
    }

    private function getOneRecord($id)
    {
        $query = $this->db->query('SELECT * FROM Rusys WHERE id='.$id);
        $query->execute();
        $result = $query->fetch();
        $gyvunas = new Gyvunas();
        return $gyvunas->sukurtiObjekta($result);
    }

    private function addNewRecord($pavadinimas)
    {
        $query= $this->db->prepare("INSERT INTO Rusys(pavadinimas)
          VALUES(:pavadinimas)");
        $query->execute([
           "pavadinimas" => $pavadinimas
        ]);
    }

    private function atnaujintiGyvuna($pavadinimas, $id )
    {
        $query= $this->db->prepare("UPDATE Rusys SET `pavadinimas`=:pavadinimas WHERE id=". $id);
        $query->execute([
            "pavadinimas" => $pavadinimas
        ]);
    }

    private function deleteOne($id)
    {
        $query = $this->db->prepare("DELETE FROM Rusys WHERE id=:id");
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