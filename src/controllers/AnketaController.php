<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 1:42 PM
 */
include_once __DIR__ . '/../classes/Anketa.php';
include_once __DIR__ . '/../classes/Nuotrauka.php';



class AnketaController
{
    protected $view;
    protected $db;
    protected $app;
    protected $flash;
    protected $path_upload_gyvunu;
    private $data=array();

    public function __construct(\Slim\Views\Twig $view, PDO $db, \Slim\Flash\Messages $flash, $path_upload_gyvunu)
    {
        $this->view = $view;
        $this->db = $db;
        $this->flash = $flash;
        $this->path_upload_gyvunu = $path_upload_gyvunu;
    }

    public function loadToFormForAdd($request, $response, $args)
    {

        if (($_SESSION["prieiga"]=="Administratorius")||($_SESSION["prieiga"]=="Kontrolierius")) {
            $this->defaultData();
            $this->addData("prieiga", $_SESSION["prieiga"]);
            $this->addData("rusys", $this->getAllRusys());
            $this->addData("vartotojai", $this->getAllVartotojai());
            $this->addData("successMessage",$this->getFlashMessage("success")[0]);
            $this->addData("dangerMessage",$this->getFlashMessage("danger")[0]);
            date_default_timezone_set("Europe/Vilnius");
            $dabartine_data_sulaiku=date("Y-m-d H:i");//dabartine data su sek:date("Y-m-d H:i:s")
            $this->addData("dabartine_data", $dabartine_data_sulaiku);
            $this->view->render($response,'anketos/create_new.twig',$this->getData());
            return $response;
        } else {
            return $response->withRedirect('/login', 301);
        }

    }

    public function addFromForm($request, $response, $args)
    {
        if ($request->isPost()) {
            $postValues = $request->getParsedBody();
            $gyvuno_amzius = $postValues["gyvuno_amzius"];
            $ar_Rastas = $postValues["ar_Rastas"];
            $autorius_id = $postValues["autorius_id"];
//                $pagr_nuotraukos_id = $postValues["pagr_nuotraukos_id"];
            $miestas = $postValues["miestas"];
            $rajonas = $postValues["rajonas"];
            $gatve = $postValues["gatve"];
            $gyvuno_vardas = $postValues["gyvuno_vardas"];
            $lytis = $postValues["lytis"];
            $apskritis = $postValues["apskritis"];
            $fk_Naudotojasid = $postValues["fk_Naudotojasid"];
            $fk_rusies_id = $postValues["fk_rusies_id"];
            $aprasymas = $postValues["aprasymas"];
            $dingimo_data = $postValues["dingimo_data"];
            $naujos_anketos_id = $this->addNewRecordInAnketosTable(
                $gyvuno_amzius,
                $ar_Rastas,
                $autorius_id,
//                    $pagr_nuotraukos_id,
                $miestas,
                $rajonas,
                $gatve,
                $gyvuno_vardas,
                $lytis,
                $apskritis,
                $fk_Naudotojasid,
                $fk_rusies_id,
                $aprasymas,
                $dingimo_data);
            $this->addFlashMessage("success", "Gyvuno anketa sukurta, jo id {$naujos_anketos_id} jo vardas {$gyvuno_vardas}");


        }
        return $response->withRedirect("/redaguoti-anketa/{$naujos_anketos_id}", 301);
    }

    private function addNewRecordInAnketosTable(
        $gyvuno_amzius,
        $ar_Rastas,
        $autorius_id,
        $miestas,
        $rajonas,
        $gatve,
        $gyvuno_vardas,
        $lytis,
        $apskritis,
        $fk_Naudotojasid,
        $fk_rusies_id,
        $aprasymas,
        $dingimo_data)
    {
        $sql="INSERT INTO Anketos(
                gyvuno_amzius,
                ar_Rastas,
                autorius_id,
                miestas,
                rajonas,
                gatve,
                gyvuno_vardas,
                lytis,
                apskritis,
                fk_Naudotojasid,
                fk_rusies_id,
                aprasymas,
                dingimo_data)
          VALUES(
                :gyvuno_amzius,
                :ar_Rastas,
                :autorius_id,
                :miestas,
                :rajonas,
                :gatve,
                :gyvuno_vardas,
                :lytis,
                :apskritis,
                :fk_Naudotojasid,
                :fk_rusies_id,
                :aprasymas,
                :dingimo_data)";
        $status = $this->db->prepare($sql)->execute([
            "gyvuno_amzius" => $gyvuno_amzius,
            "ar_Rastas" => $ar_Rastas,
            "autorius_id" => $autorius_id,
            "miestas" => $miestas,
            "rajonas" => $rajonas,
            "gatve" => $gatve,
            "gyvuno_vardas" => $gyvuno_vardas,
            "lytis" => $lytis,
            "apskritis" => $apskritis,
            "fk_Naudotojasid" => $fk_Naudotojasid,
            "fk_rusies_id" => $fk_rusies_id,
            "aprasymas" => $aprasymas,
            "dingimo_data" => $dingimo_data,
        ]);

        if ($status) {
            return $this->db->lastInsertId();
        }

    }



    public function loadToFormForEdit($request, $response, $args)
    {
        if (($_SESSION["prieiga"]=="Administratorius")||($_SESSION["prieiga"]=="Kontrolierius")) {
            $this->defaultData();
            $this->addData("prieiga", $_SESSION["prieiga"]);
            $this->addData("anketa", $this->getOneAnketa($args['id']));
            $this->addData("rusys", $this->getAllRusys());
            $this->addData("vartotojai", $this->getAllVartotojai());
            $this->addData("nuotraukos", $this->getRelatedPhoto($args['id']));
            $this->addData("successMessage", $this->getFlashMessage("success")[0]);
            $this->addData("dangerMessage", $this->getFlashMessage("danger")[0]);
            $this->view->render($response, 'anketos\edit_one.twig', $this->getData());
            return $response;
        } else {
            return $response->withRedirect('/login', 301);
        }
    }

    public function updateFromForm($request, $response, $args)
    {
        if ($request->isPost()) {
            if ($_SESSION["prieiga"]) {
                $postValues = $request->getParsedBody();
//                var_dump($postValues);die;
                $gyvuno_amzius = $postValues["gyvuno_amzius"];
                $ar_Rastas = $postValues["ar_Rastas"];
                $autorius_id = $postValues["autorius_id"];
//      kai nebuna pagrindines nuotraukos          $pagr_nuotraukos_id = $postValues["pagr_nuotraukos_id"];
                if(!$postValues["pagr_nuotraukos_id"]==""){
                    $pagr_nuotraukos_id = $postValues["pagr_nuotraukos_id"];
                }else{
                    $pagr_nuotraukos_id = null;
                }
//                var_dump($pagr_nuotraukos_id);die;
                $miestas = $postValues["miestas"];
                $rajonas = $postValues["rajonas"];
                $gatve = $postValues["gatve"];
                $gyvuno_vardas = $postValues["gyvuno_vardas"];
                $lytis = $postValues["lytis"];
                $apskritis = $postValues["apskritis"];
                $fk_Naudotojasid = $postValues["fk_Naudotojasid"];
                $fk_rusies_id = $postValues["fk_rusies_id"];
                $aprasymas = $postValues["aprasymas"];
                $dingimo_data = $postValues["dingimo_data"];
                $this->atnaujintiAnketa($args["id"],
                    $gyvuno_amzius,
                    $ar_Rastas,
                    $autorius_id,
                    $pagr_nuotraukos_id,
                    $miestas,
                    $rajonas,
                    $gatve,
                    $gyvuno_vardas,
                    $lytis,
                    $apskritis,
                    $fk_Naudotojasid,
                    $fk_rusies_id,
                    $aprasymas,
                    $dingimo_data);
                $this->addFlashMessage("success", "Gyvunas atnaujintas jo id {$args['id']} jo vardas {$gyvuno_vardas}");

            }else{
                $this->addFlashMessage("danger", "Neapdorota post užklausa - Neturite teisiu koreguoti id {$args['id']}");
            }
        }
        return $response->withRedirect("/redaguoti-anketa/{$args['id']}", 301);
    }

    public function uploadMainPhoto($request, $response, $args)
    {
        if($request->isPost()) {
            $postValues = $request->getParsedBody();
            $directory = $this->path_upload_gyvunu;

            $anketos_rusies_santraupa=$postValues["rusies_santraupa"];//ateina is twig hidden lauko
            $uploadedFiles = $request->getUploadedFiles();
            $uploadedFile = $uploadedFiles['kelimo_laukas'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {//failo kelimas Slim-e
                $filename = $this->moveUploadedFile($directory, $uploadedFile,$args['id'],$anketos_rusies_santraupa);
                /*-----gavome naujai ikeltos nuotraukos pvd---------*/
                /*-----gaunam naujos nuotraukos id, pridedami nauja irasa nuotrauku lenteleje su nauju pavd-------------*/
                $new_photo_id = $this->addNewRecordInPhotoTable($args['id'],$filename);
//                $old_photo_id = $postValues["pagr_nuotraukos_id"];
                /*-----antnaujinam pagrindine nuotrauka anketose--------------------*/
                $this->updateMainPhotoInAnketos($args['id'],$new_photo_id);
                $this->addFlashMessage("success", "Nuotrauka įkelta. Į bazę įrašyta. Jos vardas: " . $filename);
//var_dump($new_photo_id);die;
                //                $response->w/rite('uploaded ' . $filename . '<br/>');
            }else{//neikele
                $this->addFlashMessage("danger", "Neapdorota nuotraukos kėlimo post užklausa - matomai neteisingai užpildyta forma ar vidinės failo kėlimo klaidos, anketos id {$args['id']}");
            }
        }
        return $response->withRedirect("/redaguoti-anketa/{$args['id']}",301);
    }
    public function makeMainPhoto($request, $response, $args)
    {
        if($request->isPost()) {
            $postValues = $request->getParsedBody();
            $anketos_id = $postValues["anketos_id"];
            /*-----antnaujinam pagrindine nuotrauka anketose--------------------*/
            if($this->updateMainPhotoInAnketos($anketos_id,$args['id'])){
                $this->addFlashMessage("success", "Nuotrauka padaryta pagrindine.");
            }else{//nepakeite
                $this->addFlashMessage("danger", "Dėl nenumatytos klaidos, nepadaryta pagrindine nuotrauka, kurios id {$args['id']}");
            }
        }
        return $response->withRedirect("/redaguoti-anketa/{$anketos_id}",301);
    }

    function moveUploadedFile($directory, Slim\Http\UploadedFile $uploadedFile,$anketos_id, $rusies_santraupa)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
//        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        date_default_timezone_set("Europe/Vilnius");//kad rodytu musu laika
        $basename = $rusies_santraupa . "_" . $anketos_id . "_" . date("Y-m-d_H-i-s") ; // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }

    private function addNewRecordInPhotoTable($anketos_id, $nuotraukos_pav)
    {
        $sql="INSERT INTO gyvunu_nuotraukos(anketos_id, nuotraukos_pav)
          VALUES(:anketos_id, :nuotraukos_pav)";
        $status = $this->db->prepare($sql)->execute([
            "anketos_id" => $anketos_id,
            "nuotraukos_pav" => $nuotraukos_pav,
        ]);

        if ($status) {
            return $this->db->lastInsertId();
        }

    }

    private function updateMainPhotoInAnketos($anketos_id, $naujas_pgr_nuotr_id)
    {
        $sql="UPDATE Anketos SET `pagr_nuotraukos_id`=:pagr_nuotraukos_id
             WHERE id=". $anketos_id;
        $status = $this->db->prepare($sql)->execute([
            "pagr_nuotraukos_id" => $naujas_pgr_nuotr_id,
        ]);
        if ($status){
            return true;
        }
        return false;
    }

    public function viewAll($request, $response, $args)
    {
        if (isset($_SESSION["ateita_is_paieskos_anketa"])) {
            unset($_SESSION["ateita_is_paieskos_anketa"]);
        }

        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("sess_paiesk_fragm_anketa", $_SESSION["sess_paiesk_fragm_anketa"]);
        $this->addData("anketos", $this->getAllAnketas());
        /*----------patikrinimas rezultato------------*/
//        echo'<pre>';
//        var_dump($this->getData());die;
        /*--------------------------------------------*/
        $this->addData("successMessage",$this->getFlashMessage("success")[0]);
        $this->view->render($response,'anketos/view_all.twig',$this->getData());

        return $response;
    }

    public function viewSearchAnketa($request, $response, $args)
    {
//                echo'<pre>';
//        var_dump($_SESSION["paiesk_fragm_sesija"]);die;
        if (isset($_SESSION["sess_paiesk_fragm_anketa"])) {
            unset($_SESSION["sess_paiesk_fragm_anketa"]);
        }
        $_SESSION["sess_paiesk_fragm_anketa"]=$args['fragment'];//isimenam nauja fragmenta
        $_SESSION["ateita_is_paieskos_anketa"]="taip";
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("ateita_is_paieskos_anketa", $_SESSION["ateita_is_paieskos_anketa"]);
        $this->addData("sess_paiesk_fragm_anketa", $_SESSION["sess_paiesk_fragm_anketa"]);
        $search_result = $this->getSearchResult($args['fragment']);
        $this->addData("anketos", $search_result);
        $this->view->render($response,'anketos/view_all.twig',$this->getData());
        return $response;
    }

    private function getSearchResult($searchFragment)
    {
        $searchFragment = '%' . $searchFragment . '%';
        $query = $this->db->prepare("SELECT Anketos.*, Rusys.pavadinimas FROM Anketos INNER JOIN Rusys ON Rusys.id=Anketos.fk_rusies_id WHERE 
                                  (Anketos.gyvuno_vardas LIKE :searchFragment OR Anketos.aprasymas LIKE :searchFragment OR Rusys.pavadinimas LIKE :searchFragment)");
        $query->bindParam(':searchFragment', $searchFragment, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);;
        $rowcount = $query->rowCount();
        $anketos = array();
        if ($rowcount > 0) {
            foreach ($results as $row){
                $tdo = new Anketa($this->db);
                $anketos[] = $tdo->create($row);
            }
        }
        return $anketos;
    }

    public function passSearchAnketa($request, $response, $args)
    {
        if($request->isPost()) {
            $postValues = $request->getParsedBody();
            if ($postValues["paieska"]){//paspausta post paieska netuscia
                $sess_paiesk_fragm_anketa = $postValues["paieska"];
            }else{
                //paspausta su tusciu paieskos lauku - nunulinam sesija ir pagrazinam namo
                if (isset($_SESSION["sess_paiesk_fragm_anketa"])) {
                    unset($_SESSION["sess_paiesk_fragm_anketa"]);
                }
                return $response->withRedirect("/anketos",301);
            }

        }
        return $response->withRedirect("/anketos/paieska/$sess_paiesk_fragm_anketa",301);
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


    public function viewOne($request, $response, $args)
    {
//        $this->addData("pageTitle","Gyvunas");
        $this->defaultData();
        $this->addData("prieiga", $_SESSION["prieiga"]);
        $this->addData("anketa", $this->getOneAnketa($args['id']));
        $this->addData("nuotraukos", $this->getRelatedPhoto($args['id']));
        $this->view->render($response,'anketos/view_one.twig',$this->getData());
        return $response;
    }


    private function getOneAnketa($id)
    {
        $query = $this->db->query('SELECT * FROM Anketos WHERE id='.$id);
        $results = $query->fetch();
        $objektas = new Anketa($this->db);
        $objektas_uzkrautas = $objektas->create($results);

        return $objektas_uzkrautas;

    }

    private function getRelatedPhoto($id)
    {
        $query = $this->db->query('SELECT * FROM gyvunu_nuotraukos WHERE anketos_id='.$id);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $relate_records = array();
        foreach ($results as $row){
            $tdo = new Nuotrauka();
            $relate_records[] = $tdo->sukurtiObjekta($row);
        }
        return $relate_records;
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


    private function addNewRecord($pavadinimas)
    {
        $query= $this->db->prepare("INSERT INTO Rusys(pavadinimas)
          VALUES(:pavadinimas)");
        $query->execute([
            "pavadinimas" => $pavadinimas
        ]);
    }

    private function atnaujintiAnketa($id,
                                      $gyvuno_amzius,
                                      $ar_Rastas,
                                      $autorius_id,
                                      $pagr_nuotraukos_id,
                                      $miestas,
                                      $rajonas,
                                      $gatve,
                                      $gyvuno_vardas,
                                      $lytis,
                                      $apskritis,
                                      $fk_Naudotojasid,
                                      $fk_rusies_id,
                                      $aprasymas,
                                      $dingimo_data)
    {
        $query= $this->db->prepare("UPDATE Anketos SET 
                   `gyvuno_amzius`=:gyvuno_amzius,
                   `ar_Rastas`=:ar_Rastas,
                   `autorius_id`=:autorius_id,
                   `pagr_nuotraukos_id`=:pagr_nuotraukos_id,
                   `miestas`=:miestas,
                   `rajonas`=:rajonas,
                   `gatve`=:gatve,
                   `gyvuno_vardas`=:gyvuno_vardas,
                   `lytis`=:lytis,
                   `apskritis`=:apskritis,
                   `fk_Naudotojasid`=:fk_Naudotojasid,
                   `fk_rusies_id`=:fk_rusies_id,
                   `aprasymas`=:aprasymas,
                   `dingimo_data`=:dingimo_data
               WHERE id=". $id);
        $query->execute([
            "gyvuno_amzius" => $gyvuno_amzius,
            "ar_Rastas" => $ar_Rastas,
            "autorius_id" => $autorius_id,
            "pagr_nuotraukos_id" => $pagr_nuotraukos_id,
            "miestas" => $miestas,
            "rajonas" => $rajonas,
            "gatve" => $gatve,
            "gyvuno_vardas" => $gyvuno_vardas,
            "lytis" => $lytis,
            "apskritis" => $apskritis,
            "fk_Naudotojasid" => $fk_Naudotojasid,
            "fk_rusies_id" => $fk_rusies_id,
            "aprasymas" => $aprasymas,
            "dingimo_data" => $dingimo_data,
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