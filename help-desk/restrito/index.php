<?php

require_once '../php/sql.php';

//MENU

$menu = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
<h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
<div class='d-grid gap-2 me-5 ms-2'>
    <a href='../' class='btn btn-darker text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
    <a href='../listar-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
    <a href='../historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
</div>
<h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
<div class='d-grid gap-2 me-5 ms-2'>
    <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-shield-halved'></i> Restrito</a>
</div>
</div>";

//INICIO LOGIN

if (@$_SESSION['logged']) {
    header('Location: ../listar-os/');
} elseif (isset($_POST['btn-login'])) {
    $erros = array();
    $user = @mysqli_escape_string($connect, $_POST['login']);
    $pwd = md5(mysqli_escape_string($connect, $_POST['pwd_login']));
    if (empty($user) or empty($pwd)) {
        $erros[] = "";
    } else {
        $sql = "SELECT id from usuarios where login = '$user'";
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) > 0) {
            $sql = "SELECT * FROM usuarios where login = '$user' and pwd = '$pwd'";
            $result = mysqli_query($connect, $sql);
            if (mysqli_num_rows($result) == 1) {
                $userData = mysqli_fetch_array($result);
                if ($userData['status'] == "A") {
                    $_SESSION['logged'] = true;
                    $_SESSION['id'] = $userData['id'];
                    $_SESSION['name'] = $userData['nome'];
                    $_SESSION['type'] = $userData['tipo'];
                    $_SESSION['id_setor'] = $userData['id_setor_suporte'];
                    if($pwd == "81dc9bdb52d04dc20036dbd8313ed055"){
                        header('Location: ../conta/?senhaTemp=true');
                    }else{
                        header('Location: ../listar-os/');
                    }
                }else{
                    $erros[] = "<li class='text-danger mb-3'>Conta desativada!</li>";
                }
            }else{
                $erros[] = "<li class='text-danger mb-3'>Senha incorreta!</li>";
            }
        } else {
            $erros[] = "<li class='text-danger mb-3'>Usuario inexistente!</li>";
        }
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
            echo $menu;
            ?>
            <div class="col-lg-10 col-md-9">
                <div class="my-4 mx-2 p-2 ps-3 text-light bg-blue rounded shadow">
                    <h1 class="mb-0">Formulário de login</h1>
                </div>
                <div class="row mt-4 ms-2">
                    <div class=" col-xxl-3 col-lg-4 col-md-5 bg-light shadow rounded">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <div class="my-3">
                                <label for="login">Usuario:</label>
                                <input type="text" class="form-control" id="login" name="login">
                            </div>
                            <div class="mb-3">
                                <label for="pwd_login">Senha:</label>
                                <input type="password" class="form-control" id="pwd_login" name="pwd_login">
                            </div>
                            <?php
                                if (!empty($erros)) {
                                    foreach ($erros as $erro) {
                                        echo $erro;
                                    }
                                }
                            ?>
                            <button type="submit" class="btn btn-primary mb-3" name="btn-login">Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>

</html>