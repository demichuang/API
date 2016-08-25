<?php
require_once("Connect.php");
header("Content-Type:text/html; charset=utf-8");

$action = $_GET['action'];
$name = $_GET['username'];
$money = $_GET['money'];
$transid = $_GET['transid'];

class Api extends Connect
{
    function addUser($name)
    {
        $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `username` = '$name'");
        $stmt->execute();
        $rowNum = $stmt->rowCount();

        if ($rowNum == 1) {
            $tempArray = array("result" => false, "data" => ["action" => "addUser", "Error" => "Account Repeat"]);
            echo json_encode($tempArray);
        }

        if ($rowNum != 1) {
            $stmt = $this->db->prepare("INSERT `user`(`username`, `balance`) VALUES ('$name', '0')");
            $stmt->execute();

            $tempArray = array("result" => true, "data" => ["action" => "addUser", "username" => $name]);
            echo json_encode($tempArray);
        }
    }

    function getBalance($name)
    {
        $stmt = $this->db->prepare("SELECT `balance` FROM `user` WHERE `username` = '$name'");
        $stmt->execute();
        $balance = $stmt->fetchColumn();

        $tempArray = array("result" => true, "data" => ["action" => "getBalance", "balance" => $balance]);
        echo json_encode($tempArray);
    }

    function transferIn($name, $money, $transid)
    {
        $stmt = $this->db->prepare("SELECT * FROM `record` WHERE `transid` = '$transid'");
        $stmt->execute();
        $rowNum = $stmt->rowCount();

        if ($rowNum == 1) {
            $tempArray = array("result" => false, "data" => ["action" => "transferIn", "Error" => "Transid Repeat"]);
            echo json_encode($tempArray);
        }

        if ($rowNum != 1) {
            $stmt = $this->db->prepare("UPDATE `user` SET `balance` = `balance`+ $money WHERE `username` = '$name'");
            $stmt->execute();
            $stmt = $this->db->prepare("INSERT `record`(`username`, `transid`, `transfer`) VALUES ('$name', '$transid','input:$money')");
            $stmt->execute();

            $tempArray = array("result" => true, "data" => ["action" => "transferIn", "username" => $name, "transid" => $transid, "money" => $money]);
            echo json_encode($tempArray);
        }
    }

    function transferOut($name, $money, $transid)
    {
        $stmt = $this->db->prepare("SELECT * FROM `record` WHERE `transid` = '$transid'");
        $stmt->execute();
        $rowNum = $stmt->rowCount();

        if ($rowNum == 1) {
            $tempArray = array("result" => false, "data" => ["action" => "transferOut", "Error" => "Transid Repeat"]);
            echo json_encode($tempArray);
        }

        if ($rowNum != 1) {
            $stmt = $this->db->prepare("SELECT `balance` FROM `user` WHERE `username` = '$name'");
            $stmt->execute();
            $balance = $stmt->fetchColumn();

            if ($balance >= $money) {
                $stmt = $this->db->prepare("UPDATE `user` SET `balance` = `balance`- $money WHERE `username` = '$name'");
                $stmt->execute();
                $stmt = $this->db->prepare("INSERT `record`(`username`, `transid`, `transfer`) VALUES ('$name', '$transid','output:$money')");
                $stmt->execute();

                $tempArray = array("result" => true, "data" => ["action" => "transferOut", "username" => $name, "transid" => $transid, "money" => $money]);
                echo json_encode($tempArray);
            }

            if ($balance < $money) {
                $tempArray = array("result" => false, "data" => ["action" => "transferOut", "Error" => "Money not enough"]);
                echo json_encode($tempArray);
            }
        }
    }

    function getStatus($transid)
    {
        $stmt = $this->db->prepare("SELECT `transfer` FROM `record` WHERE `transid` = '$transid'");
        $stmt->execute();
        $status = $stmt->fetchColumn();

        $tempArray = array("result" => true, "data" => ["action" => "getStatus", "transid" => $transid, "Transid_action" => $status]);
        echo json_encode($tempArray);
    }
}

$myApi = new Api();


if (isset($_GET["action"])) {
    if ($_GET["action"] == "addUser") {
        $myApi->addUser($name);
    }

    if ($_GET["action"] == "getBalance") {
        $myApi->getBalance($name);
    }

    if ($_GET["action"] == "transferIn") {
        $myApi->transferIn($name, $money, $transid);
    }

    if ($_GET["action"] == "transferOut") {
        $myApi->transferOut($name, $money, $transid);
    }

    if($_GET["action"] == "getStatus") {
        $myApi->getStatus($transid);
    }
}
