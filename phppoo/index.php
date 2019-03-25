<?php

class Personnage
{
    private $_nom;
    private $_degats;
    private $_id;
    

    const CEST_MOI = 1; // Constante renvoyÃ©e par la mÃ©thode `frapper` si on se frappe soi-mÃªme.
    const PERSONNAGE_TUE = 2; // Constante renvoyÃ©e par la mÃ©thode `frapper` si on a tuÃ© le personnage en le frappant.
    const PERSONNAGE_FRAPPE = 3; // Constante renvoyÃ©e par la mÃ©thode `frapper` si on a bien frappÃ© le personnage.

    public function frapper(Personnage $perso)
    {
        if($perso->id() == $this_id){
            return self::CEST_MOI;
        }
    }

    public function recevoirDegats()
    {

    $this->_degats+=10;
    if($this_degats >= 100){
        return self::PERSONNAGE_TUE;
    }
    
    return self::PERSONNAGE_FRAPPE;
    }

// Liste des getters

    public function degats()
    {

        $this->_degats;
    }

    public function nom()
    {

        $this->_nom;
    }

    public function id()
    {

        $this->_id;
    }

// Liste des setters

    public function setDegats($degats)
    {

        $degats = (int) $degats;
        if ($degats >= 0 && $degats <= 100) {

            $this->_degats = $degats;
        }
    }

    public function setId($id)
    {
        $id = (int) $id;
        if ($id > 0) {
            $this->_id = $id;
        }
    }

    public function setNom($nom)
    {

        if (is_string($nom)) {
            $this->_nom = $nom;
        }
    }

    public function __construct(array $data)
    {

        $this->hydrate($data);
    }

    public function hydrate(array $data)
    {

        foreach ($data as $key => $value) {

            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }  
        }
    }
    public function nomValide()
  {
    return !empty($this->_nom);
  }  
}


    
//Class Person
//{
//
//    private $id;
//    public $lastname;
//
//    public function __construct($id, $lastname)
//    {
//        $this->id = $id;
//        $this->lastname = $lastname;
//    }
//
//    public function setId($id)
//    {
//        $this->id = $id;
//    }
//
//    public function hyd($data)
//    {
//        if (array_key_exists("id", $data)) {
//            $this->id = $data["id"];
//        }
//        if (array_key_exists("lastname", $data)) {
//            $this->lastname = $data["lastname"];
//        }
//    }
//
//    public function id()
//    {
//        return $this->id;
//    }
//}
//
//$p = new Person(16, "Percereau");
//$p->setId(16);
////$p->setLastname("Percereau");
//
//print $p->id();
//$p->hyd(["id" => 16, "lastname" => "Percereau"]);
