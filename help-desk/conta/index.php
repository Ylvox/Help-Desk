<?php

require_once '../php/sql.php';

if(!$_SESSION['logged']){
    header('Location: ../restrito/');
}

//CARREGAMENTO DE MENU
// 0 - NAO LOGADO
// 1 - LOGADO ADM
// 2 - LOGADO SUP
$menu = array();
$menu[0] = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
    <h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../' class='btn btn-darker text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
        <a href='../listar-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='../historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
    </div>
    <h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../dashboard/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-chart-simple'></i> Dashboard</a>
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-user'></i> Conta</a>
        <a href='../gerenciamento/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-database'></i> Gerenciamento</a>
        <a href='../php/logout.php' class='btn btn-darker text-start my-2'><i class='fa-solid fa-arrow-right-from-bracket'></i> Sair</a>
    </div>
</div>";
$menu[1] = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
    <h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../' class='btn btn-darker text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
        <a href='../listar-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='../historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
    </div>
    <h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../dashboard/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-chart-simple'></i> Dashboard</a>
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-user'></i> Conta</a>
        <a href='../php/logout.php' class='btn btn-darker text-start my-2'><i class='fa-solid fa-arrow-right-from-bracket'></i> Sair</a>
    </div>
</div>";

$avisos = array();

if(@$_GET['senhaTemp']){
    $avisos[] = "<span class='text-danger'><li>Altere sua senha com urgência!</li></span>";
}

//Alteracao de senha
if(isset($_POST['btn-alterar-senha'])){
    $atual = md5($_POST['pwd_atual']);
    $nova = md5($_POST['pwd_nova']);
    $nova2 = md5($_POST['pwd_nova2']);

    $sql = "SELECT id FROM usuarios WHERE pwd = '$atual' and id = " . $_SESSION['id'];
    $result = mysqli_query($connect, $sql);
    if($atual == "d41d8cd98f00b204e9800998ecf8427e" || $nova == "d41d8cd98f00b204e9800998ecf8427e" || $nova2 == "d41d8cd98f00b204e9800998ecf8427e"){
        $avisos[] = "<span class='text-danger'><li>Preencha todos os campos!</li></span>";
    }else if(strlen($_POST['pwd_nova']) < 8 || strlen($_POST['pwd_nova2']) < 8){
        $avisos[] = "<span class='text-danger'><li>A nova senha deve possuir pelo menos 8 caracteres!</li></span>";
    }else if(mysqli_num_rows($result) == 1){
        if($nova == $nova2){
            $sql = "UPDATE usuarios SET pwd = '$nova' WHERE id = " . $_SESSION['id'];
            if(mysqli_query($connect, $sql)){
                $avisos[] = "<span class='text-success'><li>Senha alterada com sucesso!</li></span>";
            }else{
                $avisos[] = "<span class='text-danger'><li>Erro - MYSQL " . mysqli_errno($connect) . "</li></span>";
            }
        }else{
            $avisos[] = "<span class='text-danger'><li>As senhas não conferem!</li></span>";
        }
    }else{
        $avisos[] = "<span class='text-danger'><li>Senha atual incorreta!</li></span>";
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle?></title>
    <!-- Normalize CSS -->
    <link rel="stylesheet" type="text/css" href="../css/normalize.css">
    <!-- Bootstrap -->
    <script src=”https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js”
        integrity=”sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo”
        crossorigin=”anonymous”></script>
    <script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">
    <!-- Style CSS -->
    <link rel='stylesheet' href='../css/style.css'>
    <!-- FontAwesome -->
    <link href="../fontawesome/css/all.min.css" rel="stylesheet">
    <link href="../fontawesome/css/v4-shims.min.css" rel="stylesheet">
</head>

<body>
    <div class="fixed-top">
        <div class="py-3 bg-darker shadow">
            <a class="text-light ms-3 text-decoration-none" href="#"><?php echo $pageMenuTitle?></a>
        </div>
    </div>
    <div class="container-fluid vh-100">
        <div class="row pt-5 vh-100">
            <?php
            if ($_SESSION['type'] == "ADM") {
                echo $menu[0];
            } else {
                echo $menu[1];
            }
            ?>
            <div class="col-lg-10 col-md-9">
                <div class="my-4 mx-2 p-2 ps-3 text-light bg-blue rounded shadow">
                    <h1 class="mb-0">Configurações da conta</h1>
                </div>
                <div class="row mt-4 ms-2">
                    <div class=" col-xxl-3 col-lg-4 col-md-5 bg-light shadow rounded">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                            <h3 class="mt-3">Alterar senha</h3>
                            <?php
                            foreach ($avisos as $aviso) {
                                echo $aviso;
                            }
                            ?>
                            <div class="my-3">
                                <label for="pwd_atual">Senha atual:</label>
                                <input type="password" class="form-control" id="pwd_atual" name="pwd_atual">
                            </div>
                            <div class="mb-3">
                                <label for="pwd_nova">Nova senha:</label>
                                <input type="password" class="form-control" id="pwd_nova" name="pwd_nova">
                            </div>
                            <div class="mb-3">
                                <label for="pwd_nova2">Repetir senha:</label>
                                <input type="password" class="form-control" id="pwd_nova2" name="pwd_nova2">
                            </div>
                            <button type="submit" class="btn btn-primary mb-3" name="btn-alterar-senha">Alterar senha</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>

</html>