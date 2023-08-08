<?php

ini_set("session.gc_maxlifetime", 43200);

@session_name('helpdesk');
@session_start();

$setoresMan = array(); //ADD IDS DOS SETORES PARA O TIPO DE CONTA MANUTENCAO PARA QUE POSSAM VER O DASHBOARD - INT
$idsProibidos = array();//ADD IDS DE CONTA QUE NÃO PODEM VER O DASHBOARD CASO TENHA ALGUEM QUE QUEIRA APOSTAR CORRIDA DE CHAMADOS - INT

$loginEmail = "email@email.com";
$pwdEmail = "SenhaEmail123";
$fromNameEmail = "Help-Desk";

$pageTitle = "Help Desk | Empresa";
$pageMenuTitle = "Help Desk | Empresa";

$id_set_priorizado;
if(!empty(@$_SESSION['id_setor'])){
    $id_set_priorizado = $_SESSION['id_setor'];
}else{
    $id_set_priorizado = 0; //ALTERE PARA ID DO SETOR PRIORIZADO DE VISUALIZACAO (0) PARA TODOS
}

@header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
@header("Cache-Control: post-check=0, pre-check=0", false);
@header("Pragma: no-cache");

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

$serverip = "ip-servidor-wow";
$username = "usuario_bd";
$password = "senha_bd";
$db_name = "db_helpdesk";

$connect = mysqli_connect($serverip, $username, $password, $db_name);

if(mysqli_connect_error()){
    echo "Erro - MYSQL ".mysqli_connect_error();
}

?>