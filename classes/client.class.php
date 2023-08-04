<?php
//client class

class Client extends JDb {
	
	public function __construct() {
        parent::__construct();
    }
	
	public function getClientCode($name){
		return md5(mb_strtoupper(str_replace([" ", ",", "."], "", $name)));
	}
	
	public function addClient($name){
		$code=$this->getClientCode($name);
		if(!isset(parent::$tbls["clients"][$code])){
			parent::$tbls["clients"][$code]=$name;
			$this->saveData("clients", parent::$tbls["clients"]);
		}
		return $code;
	}
	
}
 