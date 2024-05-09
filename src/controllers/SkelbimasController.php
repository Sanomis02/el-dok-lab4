<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 1:42 PM
 */
include_once __DIR__ . '/../classes/Skelbimas.php';
include_once __DIR__ . '/../classes/Zinute.php';

class SkelbimasController
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

    public function viewAll($request, $response, $args)
    {
        if (isset($_SESSION["ateita_is_paieskos_skelbimas"])) {
            unset($_SESSION["ateita_is_paieskos_skelbimas"]);
        }

        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("sess_paiesk_fragm_skelbimas", $_SESSION["sess_paiesk_fragm_skelbimas"]);
//        var_dump($this->getAllSkelbimus());die;
        $this->addData("skelbimai", $this->getAllSkelbimus());
        /*----------patikrinimas rezultato------------*/
//        echo'<pre>';
//        var_dump($this->getData());die;
        /*--------------------------------------------*/
        $this->addData("successMessage",$this->getFlashMessage("success")[0]);
        $this->view->render($response,'skelbimai/view_all.twig',$this->getData());

        return $response;
    }

    public function viewOne($request, $response, $args)
    {
//        $this->addData("pageTitle","Gyvunas");
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("skelbimas", $this->getOneSkelbima($args['id']));
        $this->addData("zinutes", $this->getOneSkelbima($args['id'])->skelbimo_zinutes);
        $this->addData("prisijunges_vartotojas", $this->getDabarPrisijunges($_SESSION["prieiga"]));
//        $this->addData("nuotraukos", $this->getRelatedPhoto($args['id']));
//        var_dump(intval($this->getOneSkelbima($args['id'])->getPerziuros_kiekis())+1);die;
        //pridedam peržiūrą
        $perziuru_skaicius=intval($this->getOneSkelbima($args['id'])->getPerziuros_kiekis())+1;
        $this->replaceSkelbimoPerziuras($perziuru_skaicius, $args['id']);
        //reikia perduot, nes is karto nemato - gaunasi atsilikimas per 1
        $this->addData("perziuru_skaicius_naujas",$perziuru_skaicius);
        $this->view->render($response,'skelbimai/view_one.twig',$this->getData());
        return $response;
    }

    private function replaceSkelbimoPerziuras($naujas_perziuru_kiekis, $id )
    {
        $query= $this->db->prepare("UPDATE Skelbimai SET `perziuros_kiekis`=:naujas_perziuru_kiekis WHERE id=". $id);
        $query->bindParam(':naujas_perziuru_kiekis', $naujas_perziuru_kiekis, PDO::PARAM_INT);
        $query->execute();
    }



    private function getOneSkelbima($id)
    {
        $query = $this->db->query('SELECT * FROM Skelbimai WHERE id='.$id);
        $results = $query->fetch();
        $objektas = new Skelbimas($this->db);
        $objektas_uzkrautas = $objektas->create($results);

        return $objektas_uzkrautas;

    }
    private function getDabarPrisijunges($prieiga)
    {
        $query = $this->db->query("SELECT * FROM Vartotojai WHERE prieiga=" . "'" . $prieiga ."'");
        $results = $query->fetch();
        $objektas = new Vartotojas();
        $objektas_uzkrautas = $objektas->create($results);

        return $objektas_uzkrautas;

    }




    public function viewSearchSkelbima($request, $response, $args)
    {
//                echo'<pre>';
//        var_dump($_SESSION["paiesk_fragm_sesija"]);die;
        if (isset($_SESSION["sess_paiesk_fragm_skelbimas"])) {
            unset($_SESSION["sess_paiesk_fragm_skelbimas"]);
        }
        $_SESSION["sess_paiesk_fragm_skelbimas"]=$args['fragment'];//isimenam nauja fragmenta
        $_SESSION["ateita_is_paieskos_skelbimas"]="taip";
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("ateita_is_paieskos_skelbimas", $_SESSION["ateita_is_paieskos_skelbimas"]);
        $this->addData("sess_paiesk_fragm_skelbimas", $_SESSION["sess_paiesk_fragm_skelbimas"]);
        $search_result = $this->getSearchResult($args['fragment']);
        $this->addData("skelbimai", $search_result);
        $this->view->render($response,'skelbimai/view_all.twig',$this->getData());
        return $response;
    }

    private function getSearchResult($searchFragment)
    {
        $searchFragment = '%' . $searchFragment . '%';
        $query = $this->db->prepare("SELECT Skelbimai.*, Rusys.pavadinimas FROM Skelbimai INNER JOIN Rusys ON Rusys.id=Skelbimai.rusies_id WHERE 
                                  (Rusys.pavadinimas LIKE :searchFragment OR Skelbimai.aprasymas LIKE :searchFragment)");
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

    public function passSearchSkelbimas($request, $response, $args)
    {
        if($request->isPost()) {
            $postValues = $request->getParsedBody();
            if ($postValues["paieska"]){//paspausta post paieska netuscia
                $sess_paiesk_fragm_skelbimas = $postValues["paieska"];
            }else{
                //paspausta su tusciu paieskos lauku - nunulinam sesija ir pagrazinam namo
                if (isset($_SESSION["sess_paiesk_fragm_skelbimas"])) {
                    unset($_SESSION["sess_paiesk_fragm_skelbimas"]);
                }
                return $response->withRedirect("/skelbimai",301);
            }

        }
        return $response->withRedirect("/skelbimo/paieska/$sess_paiesk_fragm_skelbimas",301);
    }



    public function loadToFormForAdd($request, $response, $args)
    {

        if (($_SESSION["prieiga"]=="Administratorius")||($_SESSION["prieiga"]=="Kontrolierius")) {
            $this->defaultData();
            $this->addData("prieiga", $_SESSION["prieiga"]);
            $this->addData("rusys", $this->getAllRusys());
            $this->addData("vartotojai", $this->getAllVartotojai());
            $this->addData("anketos", $this->getAllAnketas());
            $this->addData("successMessage",$this->getFlashMessage("success")[0]);
            $this->addData("dangerMessage",$this->getFlashMessage("danger")[0]);
            date_default_timezone_set("Europe/Vilnius");
            $dabartine_data_plius_dienos=date("Y-m-d H:i", strtotime(date("Y-m-d H:i"). ' + 60 days'));//dabartine data su sek:date("Y-m-d H:i:s")
            $this->addData("dabartine_data_plius_dienos", $dabartine_data_plius_dienos);
            $this->view->render($response,'skelbimai/create_new.twig',$this->getData());
            return $response;
        } else {
            return $response->withRedirect('/login', 301);
        }

    }



    public function loadToFormForEdit($request, $response, $args)
    {
        if (($_SESSION["prieiga"]=="Administratorius")||($_SESSION["prieiga"]=="Kontrolierius")) {
            $this->defaultData();
            $this->addData("prieiga", $_SESSION["prieiga"]);
            $this->addData("skelbimas", $this->getOneSkelbima($args['id']));
            $this->addData("rusys", $this->getAllRusys());
            $this->addData("vartotojai", $this->getAllVartotojai());
            $this->addData("anketos", $this->getAllAnketas());
            $this->addData("successMessage", $this->getFlashMessage("success")[0]);
            $this->addData("dangerMessage", $this->getFlashMessage("danger")[0]);
            $this->view->render($response, 'skelbimai\edit_one.twig', $this->getData());
            return $response;
        } else {
            return $response->withRedirect('/login', 301);
        }
    }

    public function addNewFromForm($request, $response, $args)
    {
        if ($request->isPost()) {
            if ($_SESSION["prieiga"]) {
                $postValues = $request->getParsedBody();
//                var_dump($postValues);die;
                $anketa_id = $postValues["anketa_id"];
                $autorius_id = $postValues["autorius_id"];
                //-----gaunam gyvuno id pagal skelbimo anketa
                $rusies_id = $this->getAnketa($postValues["anketa_id"])->gyvuno_id;
                $ar_aktyvus = $postValues["ar_aktyvus"];
                date_default_timezone_set("Europe/Vilnius");
                $skelbimo_data = date("Y-m-d H:i");
                $galiojimo_laikas = $postValues["galiojimo_laikas"];
                $perziuros_kiekis = $postValues["perziuros_kiekis"];
                $aprasymas =  $postValues["aprasymas"] . " iš anketos: " . $this->getAnketa($postValues["anketa_id"])->getAprasymas();
                $naujo_skelbimo_id = $this->addNewRecordInSkelbimuTable(
                    $anketa_id,
                    $autorius_id,
                    $rusies_id,
                    $ar_aktyvus,
                    $skelbimo_data,
                    $galiojimo_laikas,
                    $perziuros_kiekis,
                    $aprasymas);
                $this->addFlashMessage("success", "Skelbimas pridėtas jo id {$naujo_skelbimo_id} ");

            }else{
                $this->addFlashMessage("danger", "Neapdorota post užklausa - Neturite teisiu pridėti naują skelbimą");
            }
        }
        return $response->withRedirect("/redaguoti-skelbima/{$naujo_skelbimo_id}", 301);
    }

    private function addNewRecordInSkelbimuTable(
        $anketa_id,
        $autorius_id,
        $rusies_id,
        $ar_aktyvus,
        $skelbimo_data,
        $galiojimo_laikas,
        $perziuros_kiekis,
        $aprasymas)
    {
        $sql="INSERT INTO Skelbimai(
                anketa_id,
                autorius_id,
                rusies_id,
                ar_aktyvus,
                skelbimo_data,
                galiojimo_laikas,
                perziuros_kiekis,
                aprasymas)
          VALUES(
                :anketa_id,
                :autorius_id,
                :rusies_id,
                :ar_aktyvus,
                :skelbimo_data,
                :galiojimo_laikas,
                :perziuros_kiekis,
                :aprasymas)";
        $status = $this->db->prepare($sql)->execute([
            "anketa_id" => $anketa_id,
            "autorius_id" => $autorius_id,
            "rusies_id" => $rusies_id,
            "ar_aktyvus" => $ar_aktyvus,
            "skelbimo_data" => $skelbimo_data,
            "galiojimo_laikas" => $galiojimo_laikas,
            "perziuros_kiekis" => $perziuros_kiekis,
            "aprasymas" => $aprasymas,
        ]);

        if ($status) {
            return $this->db->lastInsertId();
        }

    }




    public function rasytiNaujaZinute($request, $response, $args)
    {
        if ($request->isPost()) {
            if ($_SESSION["prieiga"]) {
                $postValues = $request->getParsedBody();
//                var_dump($postValues);die;
                $autoriaus_id = $postValues["autoriaus_id"];
                $skelbimo_id = $postValues["skelbimo_id"];
                date_default_timezone_set("Europe/Vilnius");
                $zinutes_data = date("Y-m-d H:i");
                $ar_uzblokuota = 0;
                $turinys =  $postValues["turinys"];
                $naujos_zinutes_id=$this->addNewRecordInZinuciuTable(
                    $autoriaus_id,
                    $skelbimo_id,
                    $zinutes_data,
                    $ar_uzblokuota,
                    $turinys);
                $this->addFlashMessage("success", "Žinute prie Skelbimo pridėta, jos id {$naujos_zinutes_id} ");

            }else{
                $this->addFlashMessage("danger", "Neapdorota post užklausa - Neturite teisiu pridėti naują žinutę");
            }
        }
        return $response->withRedirect("/skelbimas/{$skelbimo_id}", 301);
    }




    private function addNewRecordInZinuciuTable(
        $autoriaus_id,
        $skelbimo_id,
        $zinutes_data,
        $ar_uzblokuota,
        $turinys)
    {
        $sql="INSERT INTO Zinutes(
        autoriaus_id,
        skelbimo_id,
        zinutes_data,
        ar_uzblokuota,
        turinys)
          VALUES(
                :autoriaus_id,
                :skelbimo_id,
                :zinutes_data,
                :ar_uzblokuota,
                :turinys)";
        $status = $this->db->prepare($sql)->execute([
            "autoriaus_id" => $autoriaus_id,
            "skelbimo_id" => $skelbimo_id,
            "zinutes_data" => $zinutes_data,
            "ar_uzblokuota" => $ar_uzblokuota,
            "turinys" => $turinys,
        ]);

        if ($status) {
            return $this->db->lastInsertId();
        }

    }


















    private function getAllRusys()
    {
        $query = $this->db->query('SELECT * FROM Rusys');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $all_objects = array();
        foreach ($results as $row){
            $tdo = new Gyvunas();
            $all_objects[] = $tdo->sukurtiObjekta($row);
        }
        return $all_objects;
    }
    private function getAllVartotojai()
    {
        $query = $this->db->query('SELECT * FROM Vartotojai');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $all_objects = array();
        foreach ($results as $row){
            $tdo = new Vartotojas();
            $all_objects[] = $tdo->create($row);
        }
        return $all_objects;
    }


    private function getAllAnketas()
    {
        $query = $this->db->query('SELECT * FROM Anketos');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $all_objects = array();
        foreach ($results as $row){
            $tdo = new Anketa($this->db);
            $all_objects[] = $tdo->create($row);
        }
        return $all_objects;
    }


    public function updateFromForm($request, $response, $args)
    {
        if ($request->isPost()) {
            if ($_SESSION["prieiga"]) {
                $postValues = $request->getParsedBody();
//                var_dump($postValues);die;
                $anketa_id = $postValues["anketa_id"];
                $autorius_id = $postValues["autorius_id"];
                //-----gaunam gyvuno id pagal skelbimo anketa
                $rusies_id = $this->getAnketa($postValues["anketa_id"])->gyvuno_id;
                $ar_aktyvus = $postValues["ar_aktyvus"];
                $skelbimo_data = $postValues["skelbimo_data"];
                $galiojimo_laikas = $postValues["galiojimo_laikas"];
                $perziuros_kiekis = $postValues["perziuros_kiekis"];
                $aprasymas = $postValues["aprasymas"];

                $this->atnaujintiSkelbima($args["id"],
                    $anketa_id,
                    $autorius_id,
                    $rusies_id,
                    $ar_aktyvus,
                    $skelbimo_data,
                    $galiojimo_laikas,
                    $perziuros_kiekis,
                    $aprasymas);
                $this->addFlashMessage("success", "Skelbimas atnaujintas jo id {$args['id']} ");

            }else{
                $this->addFlashMessage("danger", "Neapdorota post užklausa - Neturite teisiu koreguoti id {$args['id']}");
            }
        }
        return $response->withRedirect("/redaguoti-skelbima/{$args['id']}", 301);
    }


    private function atnaujintiSkelbima($id,
                                        $anketa_id,
                                        $autorius_id,
                                        $rusies_id,
                                        $ar_aktyvus,
                                        $skelbimo_data,
                                        $galiojimo_laikas,
                                        $perziuros_kiekis,
                                        $aprasymas)
    {
        $query= $this->db->prepare("UPDATE Skelbimai SET
                    `anketa_id`=:anketa_id,
                    `autorius_id`=:autorius_id,
                    `rusies_id`=:rusies_id,
                    `ar_aktyvus`=:ar_aktyvus,
                    `skelbimo_data`=:skelbimo_data,
                    `galiojimo_laikas`=:galiojimo_laikas,
                    `perziuros_kiekis`=:perziuros_kiekis,
                    `aprasymas`=:aprasymas
               WHERE id=". $id);
        $query->execute([
            "anketa_id" => $anketa_id,
            "autorius_id" => $autorius_id,
            "rusies_id" => $rusies_id,
            "ar_aktyvus" => $ar_aktyvus,
            "skelbimo_data" => $skelbimo_data,
            "galiojimo_laikas" => $galiojimo_laikas,
            "perziuros_kiekis" => $perziuros_kiekis,
            "aprasymas" => $aprasymas,

        ]);
    }


    private function getAnketa($id)
    {
        $query = $this->db->query('SELECT * FROM Anketos WHERE id='.$id);
        $query->execute();
        $result = $query->fetch();
        $anketa = new Anketa($this->db);
        return $anketa->create($result);
    }



    private function getAllSkelbimus()
    {
        $query = $this->db->query('SELECT * FROM Skelbimai');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $all_objects = array();
        foreach ($results as $row){
            $tdo = new Skelbimas($this->db);
            $all_objects[] = $tdo->create($row);
        }
        return $all_objects;
    }




















    public function create($request, $response, $args)
    {
        $this->addData("pageTitle","Slim Todo - Create Task");
        $data = $this->getData();
        $this->view->render($response,'create.twig',$data);
        return $response;
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

    public function kurtiSkelbima($request, $response, $args)
    {
        $this->addData("pageTitle","Projektas gyvunai");
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $data = $this->getData();
        $this->view->render($response, 'skelbimai/create.twig', $data);
        return $response;
    }

    public function edit($request, $response, $args)
    {
        $this->addData("pageTitle","Slim Todo - Edit Task");
        $task = $this->getTask($args['id']);
        $this->addData("todo", $task);
        $this->addData("successMessage",$this->getFlashMessage("success")[0]);
        $this->view->render($response,'edit.twig', $this->getData());
        return $response;
    }

    public function update($request, $response, $args)
    {
        if($request->isPost()) {
            $postValues = $request->getParsedBody();
            $name = $postValues["task"];
            $details = $postValues["details"];
            $author = $postValues["author"];
            $this->updateTask($name, $details,$author, $args["id"]);
            $this->addFlashMessage("success","Task Updated");
            return $response->withRedirect("/edit/{$args['id']}",301);
        }

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

    public function istrintiSkelbima($request, $response, $args)
    {
        if($request->isPost() && $request->isXhr()) {
            $body = $request->getParsedBody();
            if(isset($body["skelbimoId"])){
                if($this->deleteSkelbima($body["skelbimoId"])){
                    $this->addFlashMessage("success", "Skelbimas ištrintas jo id {$body["skelbimoId"]} ");
                    $update_response = [
                        'update_result' =>	'gerai',
                        'update_message' =>	'transakcija nerezultatyvi išsaugojant prekę lentelėje: '
                            . ' Gal būt nieko šioje lentelėje nekeitėte ar nėra įrašo (action update)',
                    ];
                    echo json_encode($update_response,JSON_UNESCAPED_UNICODE);
                }else{
                    $this->addFlashMessage("danger", "Neapdorota post užklausa - neitrintas skelbimas id {$body["skelbimoId"]}");

                }
            }
        }
    }

    private function addData($key, $value)
    {
        $this->data[$key] = $value;
    }

    private function getData()
    {
        return $this->data;
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

    private function updateTask( $name, $details, $author, $id )
    {
        $query= $this->db->prepare("UPDATE slim_tasks SET `name`=:name,
            `details`=:details, `author`=:author WHERE task_id=". $id);
        $query->execute([
            "name" => $name,
            "details" => $details,
            "author" => $author,
        ]);
    }

    private function deleteSkelbima($id)
    {

        $query = $this->db->prepare("DELETE FROM Skelbimai WHERE id=:id");
        if($query->execute([
            "id" => $id,
        ])){
            return true;
        }
        return false;
    }
    private function defaultData()
    {
        $this->addData("pageTitle", "Projektas gyvunai");
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