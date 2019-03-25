<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class Characters{
    
         protected $atout,
            $degats,
            $id,
            $nom,
            $timeEndormi,
            $type;
                
         const MySelf = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soit-même.
         const Kill = 2;// Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant.
         const Hit = 3; // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage.
         const Magic = 4;// Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on a bien ensorcelé un personnage.
         const NoMagic = 5; // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on veut jeter un sort alors que la magie du magicien est à 0.
         const Sleep = 6; // Constante renvoyée par la méthode `frapper` si le personnage qui veut frapper est endormi.
         
         public function __construct(array $data){
             
             $this->hydrate($data);
             $this->type = strtolower(static::class);  
         }
         
         public function IsSleeping(){
             
             return $this-> timeEndormi > time();
         }
         
         public function Hit(Characters $perso){
             if($perso->id == $this->id){
                 return self::MySelf;
             }
             if($this->IsSleeping()){
                 return self::Sleep;
             }
             // On indique au personnage qu'il doit recevoir des dégâts.
    // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE.
             return $perso->ReceiveDammage();
         }
         
         public function hydrate(array $data){
             
             foreach ($data as $key => $value){
                 
                 $method = 'set'.ucfirst($key);
                 if (method_exists($this,$method)){
                     
                     $this->$method($value);
                 }
             }
         }
         public function ValidName(){
             
             return !empty($this->nom);
         }
         
         public function ReceiveDammage(){
             
             $this->degats += 5;
              // Si on a 100 de dégâts ou plus, on supprime le personnage de la BDD.
             if($this->degats >= 100){
                 return self::Kill;
             }
             else{
                 return self::Hit;
             }
         }
          public function Awake(){
           
            $seconds = $this->timeEndormi;
            $seconds -= time();
            
            $hours = floor($seconds / 3600);
            $seconds -= $hours * 3600;
            $minutes = floor($seconds / 60);
            $seconds -= $minutes * 60;
            
            $hours .= $hours <= 1 ? 'hour':'hours';
            $minutes .= $minutes<=1 ? 'minute':'minutes';
            $seconds.= $seconds <= 1 ? 'second':'seconds';
            
            return $hours.' '.$minutes.' '.$seconds;
            
        }
         public function asset(){
             
             return $this->atout;
         }
         public function dammage(){
             
             return $this->degats;
         }
         
         public function id(){
             
             return $this->id;
         }
         public function name(){
             
             $this->nom;
         }
         
         public function sleepingTime(){
             
             $this-> timeEndormi;
         }
         public function type(){
             
             $this->type;
         }
         
         public function setAsset($atout){
             
             $atout = (int)$atout;
             if($atout >= 0 && $atout <= 100){
                 
                 $this->asset = $atout;
             }
         }
         public function setDammage($degats){
             
             $degats = (int) $degats;
             if($degats >= 0 && $degats<= 100){
                 
                 $this->dammage = $degats;
             }
         }
         
         public function setId($id){
             
            $id =(int)$id;
            if($id>0){
                
                $this->id = $id;
            }
         }
         public function setName($nom){
             
             if(is_string($nom)){
                 
                 $this->name = $nom;
             }
         }
         
         public function setSleepingTime($time){
             
             $this->timeEndormi =(int)$time;
         }
    }
    
    
    class Warrior extends Characters{
        
        public function receiveDammage(){
            
            if($this->degats >= 0 && $this->degats <=25){
                
                $this->atout = 4;
            }
            
            elseif ($this->degats > 25 && $this->degats <= 50){
                
                $this->atout = 3;
            }
            elseif($this->degats>50 && $this->degats<= 75){
                
                $this->atout=2;
            }
            elseif($this->degats > 75 && $this->degats <= 90){
                
                $this->atout = 1;
            }
            else{
                $this->atout=0;
            }
            $this->degats += 5 - $this->atout;
            
            if($this->degats >= 100){
                
                return self::Kill;
            }
            return self::Hit;
        }
    }
   
class Magician extends Characters{
    
    public function Magic(Characters $perso){
        if($this->degats >= 0 && $this->degats <= 25){
            
            $this->atout = 4;
        }
        elseif($this->degats > 25 && $this->degats <= 50){
            
            $this->atout = 3;
        }
        elseif($this->degats >50 && $this->degats <= 75){
            
            $this->atout =2;
        }
        elseif($this->degats > 75 && $this->degats <= 90){
            
            $this->atout = 1;
        }
        else{
            $this->atout=0;
        }
        
        if($perso->id == $this->id){
            
            return self::MySelf;
        }
        
        if($this->atout == 0){
            
            return self::NoMagic;
        }
        
        if($this->IsSleeping()){
            
            return self::Sleep;
        }
        
        $perso-> timeEndormi = time() + ($this->atout*6)*3600;
        return self::Magic;
    }
}

class CharactersManager{
    
    private $db;
    
    public function __construct($db){
        
        $this->db = $db;
    }
    
    public function add(Characters $perso){
        
        $q = $this->db->prepare('INSERT INTO personnages_v2(nom, type) VALUES (:nom, :type)');
        $q->bindValue(':nom', $perso->nom());
        $q->bindValue(':type', $perso->type());
        $q->execute();
        
        $perso->hydrate([
            'id '=>$this->db->lastInsertId(),
            'degats'=> 0,
            'atout'=> 0
        ]);
    }
    
    public function count(){
        
        return $this->db->query('SELECT COUNT(*) FROM personnages_v2')->fetchColumn();
    }
    public function delete(Characters $perso){
        
        $this->db->exec('DELETE FROM personnage_v2 WHERE id='.$perso->id());
        
    }
        public function exists($info){
            
            if(is_int($info)){
                
                return(bool) $this->db->query('SELECT COUNT(*) FROM personnages_v2 WHERE nom= :nom');
                $q->execute([':nom'=>$info]);
                return (bool) $q->fetchColumn();
            }
        }
        public function get($info){
            
            if(is_int($info)){
                $q = $this->db->query('SELECT id,nom,degats,timeEndormi, type, atout FROM personnages_v2 WHERE id='.$info);
                $perso = $q->fetch(PDO::FETCH_ASSOC);
            }
            
            switch ($perso['type']){
                
                case 'guerrier': return new Warrior($perso);
                case 'magician': return new Magician($perso);
                default: return null;
            
            }  
        }
        public function getList($nom){
            
            $persos = [];
            $q = $this->db->prepare('SELECT id,nom,degats,timeEndormi,type,atout FROM ersonnage_v2 WHERE nom <> :nom ORDER BY nom');
            $q->execute([':nom'=>$nom]);
            
            while ($data = $q->fetch(PDO::FETCH_ASSOC)){
                
                switch ($data['type'])
                {
                    case 'guerrier': $persos[] = new Warrior($data);
                    break;
                    case 'magician': $persos[] = new Magician($data);
                    break;
                    
                }
            }
            return $persos;
        }
        
        public function update(Characters $perso){
            
             $q->bindValue(':degats', $perso->dammage(), PDO::PARAM_INT);
             $q->bindValue(':timeEndormi', $perso->IsSleeping(), PDO::PARAM_INT);
             $q->bindValue(':atout', $perso->asset(), PDO::PARAM_INT);
             $q->bindValue(':id', $perso->id(), PDO::PARAM_INT);
    
             $q->execute();
        }
}

