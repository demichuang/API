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
        $rowNum = $stmt->rowCount();

        if ($rowNum != 1) {
            $tempArray = array("result" => false, "data" => ["action" => "getBalance", "Error" => "No this user."]);
            echo json_encode($tempArray);
        }

        if ($rowNum == 1) {
            $balance = $stmt->fetchColumn();
            $tempArray = array("result" => true, "data" => ["action" => "getBalance", "balance" => $balance]);
            echo json_encode($tempArray);
        }
    }

    function in($name, $money, $transid)
    {
        $stmt = $this->db->prepare("SELECT `balance` FROM `user` WHERE `username` = '$name'");
        $stmt->execute();
        $rowNum = $stmt->rowCount();

        if ($rowNum != 1) {
            $tempArray = array("result" => false, "data" => ["action" => "in", "Error" => "No this user."]);
            echo json_encode($tempArray);
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM `record` WHERE `transid` = '$transid'");
        $stmt->execute();
        $rowNum = $stmt->rowCount();

        if ($rowNum == 1) {
            $tempArray = array("result" => false, "data" => ["action" => "in", "Error" => "Transid Repeat"]);
            echo json_encode($tempArray);
        }

        if ($rowNum != 1) {
            if ($money <= 0 || $money > 1000000) {
                $tempArray = array("result" => false, "data" => ["action" => "in", "Error" => "money enter error"]);
                echo json_encode($tempArray);
                exit;
            }
            $stmt = $this->db->prepare("UPDATE `user` SET `balance` = `balance`+ $money WHERE `username` = '$name'");
            $stmt->execute();
            $stmt = $this->db->prepare("INSERT `record`(`username`, `transid`, `transfer`) VALUES ('$name', '$transid', 'input:$money')");
            $stmt->execute();

            $tempArray = array("result" => true, "data" => ["action" => "in", "username" => $name, "transid" => $transid, "money" => $money]);
            echo json_encode($tempArray);
        }
    }

    function out($name, $money, $transid)
    {
        $stmt = $this->db->prepare("SELECT `balance` FROM `user` WHERE `username` = '$name'");
        $stmt->execute();
        $rowNum = $stmt->rowCount();

        if ($rowNum != 1) {
            $tempArray = array("result" => false, "data" => ["action" => "out", "Error" => "No this user."]);
            echo json_encode($tempArray);
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM `record` WHERE `transid` = '$transid'");
        $stmt->execute();
        $rowNum = $stmt->rowCount();

        if ($rowNum == 1) {
            $tempArray = array("result" => false, "data" => ["action" => "out", "Error" => "Transid Repeat"]);
            echo json_encode($tempArray);
        }

        if ($rowNum != 1) {
            if ($money <= 0 || $money > 1000000) {
                $tempArray = array("result" => false, "data" => ["action" => "out", "Error" => "money enter error"]);
                echo json_encode($tempArray);
                exit;
            }

            $stmt = $this->db->prepare("SELECT `balance` FROM `user` WHERE `username` = '$name'");
            $stmt->execute();
            $balance = $stmt->fetchColumn();

            if ($balance >= $money) {
                $stmt = $this->db->prepare("UPDATE `user` SET `balance` = `balance`- $money WHERE `username` = '$name'");
                $stmt->execute();
                $stmt = $this->db->prepare("INSERT `record`(`username`, `transid`, `transfer`) VALUES ('$name', '$transid', 'output:$money')");
                $stmt->execute();

                $tempArray = array("result" => true, "data" => ["action" => "out", "username" => $name, "transid" => $transid, "money" => $money]);
                echo json_encode($tempArray);
            }

            if ($balance < $money) {
                $tempArray = array("result" => false, "data" => ["action" => "out", "Error" => "Money not enough"]);
                echo json_encode($tempArray);
            }
        }
    }

    function getStatus($transid)
    {
        $stmt = $this->db->prepare("SELECT `transfer` FROM `record` WHERE `transid` = '$transid'");
        $stmt->execute();
        $rowNum = $stmt->rowCount();

        if ($rowNum != 1) {
            $tempArray = array("result" => false, "data" => ["action" => "getStatus", "Error" => "No this transid."]);
            echo json_encode($tempArray);
        }

        if ($rowNum == 1) {
            $status = $stmt->fetchColumn();
            $tempArray = array("result" => true, "data" => ["action" => "getStatus", "transid" => $transid, "Transid_action" => $status]);
            echo json_encode($tempArray);
        }
    }
}

$myApi = new Api();

if (!isset($_GET["action"])) {
    $tempArray = array("result" => false, "data" => ["action" => "", "Error" => "No Action Enter"]);
    echo json_encode($tempArray);
}

if (isset($_GET["action"])) {
    if ($_GET["action"] == "addUser") {
        if (!isset($name)) {
            $tempArray = array("result" => false, "data" => ["action" => "addUser", "Error" => "You don't have name parameter."]);
            echo json_encode($tempArray);
        }

        if (isset($name)) {
            $myApi->addUser($name);
        }
    }

    if ($_GET["action"] == "getBalance") {
        if (!isset($name)) {
            $tempArray = array("result" => false, "data" => ["action" => "getBalance", "Error" => "You don't have name parameter."]);
            echo json_encode($tempArray);
        }

        if (isset($name)) {
            $myApi->getBalance($name);
        }
    }

    if ($_GET["action"] == "in") {
        if (!isset($name) || !isset($money) || !isset($transid)) {
            $tempArray = array("result" => false, "data" => ["action" => "in", "Error" => "parameter missing"]);
            echo json_encode($tempArray);
        }

        if (isset($name) && isset($money) && isset($transid)) {
            $myApi->in($name, $money, $transid);
        }
    }

    if ($_GET["action"] == "out") {
        if (!isset($name) || !isset($money) || !isset($transid)) {
            $tempArray = array("result" => false, "data" => ["action" => "out", "Error" => "parameter missing"]);
            echo json_encode($tempArray);
        }

        if (isset($name) && isset($money) && isset($transid)) {
            $myApi->out($name, $money, $transid);
        }
    }

    if($_GET["action"] == "getStatus") {
        if (!isset($transid)) {
            $tempArray = array("result" => false, "data" => ["action" => "getStatus", "Error" => "You don't have transid parameter."]);
            echo json_encode($tempArray);
        }

        if (isset($transid)) {
            $myApi->getStatus($transid);
        }
    }
}
