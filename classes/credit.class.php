<?php
//client class

class Credit extends JDb {
	
	
	public function __construct() {
        parent::__construct();
		$this->cl=new Client();
    }
	
	
	private function calcVnoska($suma, $months){
		$interestRate = $this->lihva/12/100;
		$m_payment = round(($suma*$interestRate)/(1-pow(1+$interestRate,-$months)),2);
		return $m_payment;
	}
	
	
	public function createList(){
		$re=[];
		$crediti=parent::$tbls["credits"]["data"];
		foreach($crediti as $no=>$info){
			$client=parent::$tbls["clients"][$info["client"]];
			$re[]=[
				"code"=>$no,
				"name"=>$client,
				"sum"=>$info["suma"],
				"srok"=>$info["months"],
				"msum"=>$info["vnoska"],			
			];
		}
		return $re;
	}
	
	public function addPayment($no, $s){
		$re=["ok"=>0, "msg"=>"Невалидни данни"];
		
		if(isset(parent::$tbls["credits"]["data"][$no])){
			$credit=parent::$tbls["credits"]["data"][$no];
			$total=$credit["total"];
			if($s>$total){
				$re=["ok"=>0, "msg"=>"Вноската е по-голяма от кредита"];				
			} else {
			
				if(isset(parent::$tbls["payments"][$no])){
					foreach(parent::$tbls["payments"][$no] as $vn){
						$total-=$vn["v"];
					}
					if($s>$total){
						$re=["ok"=>0, "msg"=>"Вноската е по-голяма от остатъка по кредита"];				
					} else {
						parent::$tbls["payments"][$no][]=["v"=>$s, "t"=>time()];
						parent::saveData("payments", parent::$tbls["payments"]);
						$re=["ok"=>1, "msg"=>"Вноската е въведена"];
					}
				} else {
					//първа вноска
					parent::$tbls["payments"][$no][]=["v"=>$s, "t"=>time()];
					parent::saveData("payments", parent::$tbls["payments"]);
					$re=["ok"=>1, "msg"=>"Вноската е въведена"];
				}
			}
		} else {
			$re=["ok"=>0, "msg"=>"Невалиден кредит"];
		}
		return $re;
	}
	
	
	public function getCredit($no){
		$re=[];
		$crediti=parent::$tbls["credits"]["data"];
		if(isset(parent::$tbls["credits"]["data"][$no])){
			$re=parent::$tbls["credits"]["data"][$no];
			$re["vnoska"]=number_format($re["vnoska"],2, ".", "");
			$re["name"]=parent::$tbls["clients"][$re["client"]];
		}
		return $re;
	}
	
	
	public function createCredit($name, $months, $suma){
		if($months<$this->period_min){
			return ["ok"=>0, "msg"=>"Месеците трябва да са поне 3"];
		}
		if($months>$this->period_max){
			return ["ok"=>0, "msg"=>"Максималния период на кредита е 120 месеца $months "];
		}
		if($suma>$this->max_value){
			return ["ok"=>0, "msg"=>"Максималната сума на кредит е 80 000 лева"];
		}
		$client_code=$this->cl->addClient($name);
		$ostatuk=$this->max_value;
		if(isset(parent::$tbls["clientcredits"][$client_code])){
			foreach(parent::$tbls["clientcredits"][$client_code] as $c){
				$currentcredit=parent::$tbls["credits"]["data"][$c];
				$ostatuk-=$currentcredit["suma"];
			}
		}
		if($suma>$ostatuk){
			return ["ok"=>0, "msg"=>"Клиента има и други кредити и може да вземе до $ostatuk лв. "];
		}
		
		$mesechna_vnoska=$this->calcVnoska($suma, $months);
		$obshtasuma=$mesechna_vnoska*$months;
		if(!isset(parent::$tbls["credits"]["last"])){
			parent::$tbls["credits"]["last"]=0;
		}
		parent::$tbls["credits"]["last"]++;
		$creditno=str_pad(parent::$tbls["credits"]["last"], $this->code_len, '0', STR_PAD_LEFT);
		parent::$tbls["credits"]["data"][$creditno]=[
			"client"=>$client_code,
			"suma"=>$suma,
			"months"=>$months,
			"total"=>$obshtasuma,
			"vnoska"=>$mesechna_vnoska
		];
		
		parent::saveData("credits", parent::$tbls["credits"]);		
		parent::$tbls["clientcredits"][$client_code][]=$creditno;
		parent::saveData("clientcredits", parent::$tbls["clientcredits"]);
		return ["ok"=>1, "msg"=>"Кредита е въведен с код $creditno"];
	}
}
 