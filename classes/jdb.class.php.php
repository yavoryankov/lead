<?php

class JDb {
	
	private static $constructed = false;
	private $data_path="./data/";
	public $tbls=["clients"=>[], 
					"credits"=>[], 
					"clientcredits"=>[],
					"payments"=>[]];
	
	protected $lihva=7.9;
	protected $period_min=3;
	protected $period_max=120;
	protected $max_value=80000;
	protected $code_len=8; //дължина на кода на кредита
    
	public function __construct() {
		 if (!self::$constructed) {
			$this->loadData();
            self::$constructed = true;
        }
    }

    private function loadData() {
		foreach($this->tbls as $k=>$v){
			$this->tbls[$k]=$this->loadFile($this->data_path.$k.".json");
		}
    }
	
	private function loadFile($file){
		if (file_exists($file)) {
			$jsonData = file_get_contents($file);
			$data = json_decode($jsonData, true);
		} else {
			$data = [];
			$jsonData = json_encode($data);
			file_put_contents($file, $jsonData);
		}
		return $data;
	}
	
	protected function saveData($file, $data){
		$file=$this->data_path.$file.".json";
		$jsonData = json_encode($data);
		file_put_contents($file, $jsonData);
	}
	

	

	
}