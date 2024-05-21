<?php
/**
 * Created by PhpStorm.
 * User: shady
 * Date: 12/28/18
 * Time: 1:42 PM
 */

class PdoWrapper
{
   private $pdo;
   private $host;
   private $dbname;
   protected $app;

   public function __construct($user, $password)
   {
        try {
            $host = 'animals_mysql';
            $dbname = 'animals';
            $pdo = new PDO('mysql:host=animals_mysql;dbname=animals', $user, $password);
        } catch(PDOException $e) {
            $this->addFlashMessage("danger", "Neteisingi prisijungimo duomenys");
        }

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