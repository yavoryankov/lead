<?php

include "classes/jdb.class.php";
include "classes/client.class.php";
include "classes/credit.class.php";


$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
$credit= new Credit();

$re=["ok"=>0, "msg"=>"General error!"];

switch ($action) {
    case "create":
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
		$s = filter_input(INPUT_POST, 's', FILTER_VALIDATE_INT);
		$v = filter_input(INPUT_POST, 'v', FILTER_VALIDATE_INT);
		if ($name !== false && $s !== false && $v !== false) {
			$re=$credit->createCredit($name, $v, $s);
		} else {
			$re=["ok"=>0, "msg"=>"Проблем с данните! $name / $s / $v "];
		}
        break;
    case "pay":
        $no = filter_input(INPUT_POST, 'no', FILTER_SANITIZE_SPECIAL_CHARS);
		$s = filter_input(INPUT_POST, 's', FILTER_VALIDATE_FLOAT);
		if ($no !== false && $s !== false) {
			$re=$credit->addPayment($no, $s);
		} else {
			$re=["ok"=>0, "msg"=>"Проблем с данните! $no / $s "];
		}
        break;
    case "check":
        $no = filter_input(INPUT_POST, 'no', FILTER_SANITIZE_SPECIAL_CHARS);
		if ($no !== false) {
			$info=$credit->getCredit($no);
			if(count($info)){
				$re=["ok"=>1, "msg"=>"Намерен", "credit"=>$info];
			} else {
				$re=["ok"=>0, "msg"=>"Невалиден код!"];
			}
		} else {
			$re=["ok"=>0, "msg"=>"Проблем с данните! $name / $no "];
		}
        break;
    default:
      $re=$credit->createList();
}

echo json_encode($re);

//print_r($credit);

/*
$no = filter_input(INPUT_POST, 'no', FILTER_SANITIZE_SPECIAL_CHARS);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

$cl=new Client();
print_r($cl);
echo $cl->addClient("Test Tesh toto");
print_r($cl);
print_r( $cc->addPayment("00000013", 50));
*/

//print_r($cc);
