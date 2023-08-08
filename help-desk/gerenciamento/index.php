<?php

require_once '../php/sql.php';
require_once '../php/Purifier/HTMLPurifier.auto.php';

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

$avisos = array();

if ($_SESSION['logged']) {
    if ($_SESSION['type'] != "ADM") {
        header('Location: ../listar-os/');
    }
} else
    header('Location: ../restrito/');
//CARREGAMENTO DE MENU
// 0 - NAO LOGADO
// 1 - LOGADO ADM
// 2 - LOGADO SUP
$menu = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
    <h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../' class='btn btn-darker text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
        <a href='../listar-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='../historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
    </div>
    <h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../dashboard/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-chart-simple'></i> Dashboard</a>
        <a href='../conta' class='btn btn-darker text-start my-2'><i class='fa-solid fa-user'></i> Conta</a>
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-database'></i> Gerenciamento</a>
        <a href='../php/logout.php' class='btn btn-darker text-start my-2'><i class='fa-solid fa-arrow-right-from-bracket'></i> Sair</a>
    </div>
</div>";

//sql para dados de setores suporte
$sql = "SELECT * FROM setores";
$sql_setores = mysqli_query($connect, $sql);

//sql para dados contas criadas e controle paginacao
$tamanhoMenu = 2; // Tamanho menu de paginacao para cada lado quando tem varias paginas
$qnt = 10; // Quantidade maxima de resultados por pagina
$pagina = (isset($_GET['pagina'])) ? (int) $_GET['pagina'] : 1;
$inicio = ($qnt * $pagina) - $qnt;
$nomeConta = $purifier->purify(@mysqli_escape_string($connect, $_GET['pNomeConta']));
$sql = "SELECT * FROM vw_usuarios order by nome LIMIT $inicio,$qnt";
$sqlSize = "SELECT id FROM vw_usuarios";
if (!empty($nomeConta)) {
    $sql = "SELECT * FROM vw_usuarios where nome LIKE '%$nomeConta%' order by nome LIMIT $inicio,$qnt";
    $sqlSize = "SELECT id FROM vw_usuarios WHERE nome LIKE '%$nomeConta%'";
}
$sql_usuarios = mysqli_query($connect, $sql);
$tamanhoQuery = mysqli_query($connect, $sqlSize);
$tamanho = mysqli_num_rows($tamanhoQuery);
$totalPaginas = ceil($tamanho/$qnt);

//Reset senha
if (isset($_POST['btn-reset-conta'])) {
    $id_conta = $purifier->purify(@mysqli_escape_string($connect, $_POST['id-reset']));
    $sql = "UPDATE usuarios SET pwd = '81dc9bdb52d04dc20036dbd8313ed055' WHERE usuarios.id = '$id_conta'";
    if (mysqli_query($connect, $sql)) {
        $avisos[] = "<span class='text-success'><li>Senha de " . $_POST['nome-reset'] . " resetada com sucesso!</li></span>";
    } else {
        $avisos[] = "<span class='text-danger'><li>Erro - " . mysqli_errno($connect) . "</li></span>";
    }
}

//Desativar conta
if (isset($_POST['btn-desativar-conta'])) {
    $id_conta = $purifier->purify(@mysqli_escape_string($connect, $_POST['id-reset']));
    $sql = "UPDATE usuarios SET status = 'I' WHERE usuarios.id = '$id_conta'";
    $nome = $_POST['nome-reset'];
    if (mysqli_query($connect, $sql)) {
        header("Refresh:0; url=./?status=i&nome=$nome");
    } else {
        $avisos[] = "<span class='text-danger'><li>Erro - " . mysqli_errno($connect) . "</li></span>";
    }
} else if (isset($_POST['btn-ativar-conta'])) {
    $id_conta = $purifier->purify(@mysqli_escape_string($connect, $_POST['id-reset']));
    $sql = "UPDATE usuarios SET status = 'A' WHERE usuarios.id = '$id_conta'";
    $nome = $_POST['nome-reset'];
    if (mysqli_query($connect, $sql)) {
        header("Refresh:0; url=./?status=a&nome=$nome");
    } else {
        $avisos[] = "<span class='text-danger'><li>Erro - " . mysqli_errno($connect) . "</li></span>";
    }
}
//aviso get de alteraçao de status conta - TRATAMENTO DE AVISOS GET
if (isset($_GET['status']) && isset($_GET['nome'])) {
    if ($_GET['status'] == "i") {
        $avisos[] = "<span class='text-success'><li>Conta de " . $_GET['nome'] . " DESATIVADA com sucesso!</li></span>";
    } else if ($_GET['status'] == "a") {
        $avisos[] = "<span class='text-success'><li>Conta de " . $_GET['nome'] . " ATIVADA com sucesso!</li></span>";
    }
} else if (isset($_GET['status'])) {
    if ($_GET['status'] == "newaccok") {
        $avisos[] = "<span class='text-success'><li>Conta criada com sucesso!</li></span>";
    }
    if ($_GET['status'] == "editok") {
        $avisos[] = "<span class='text-success'><li>Dados da conta de " . @$_GET['nomeAnt'] . " atualizado com sucesso!</li></span>";
    }
}
//Criacao de contas
if (isset($_POST['btn-criarConta'])) {
    $nome = $purifier->purify(@mysqli_escape_string($connect, $_POST['nome_conta']));
    $login = $purifier->purify(@mysqli_escape_string($connect, $_POST['login_conta']));
    $email = $purifier->purify(@mysqli_escape_string($connect, $_POST['email_conta']));
    $tipo = $purifier->purify(@mysqli_escape_string($connect, $_POST['tipo_conta']));
    $setor = $purifier->purify(@mysqli_escape_string($connect, $_POST['setor_conta']));
    if (empty($nome) || empty($login) || empty($tipo) || empty($setor)) {
        $avisos[] = "<span class='text-danger'><li>Preencha todos os campos!</li></span>";
    } else {
        $sql = "select id from usuarios where login = '$login'";
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) > 0) {
            $avisos[] = "<span class='text-danger'><li>Ja existe uma conta com esse login!</li></span>";
        } else {
            if(empty($email)){
                $sql = "INSERT INTO usuarios (nome, login, pwd, email, status, tipo, id_setor_suporte) values('$nome', '$login', '81dc9bdb52d04dc20036dbd8313ed055', NULL, 'A', '$tipo', $setor)";
            }else{
                $sql = "INSERT INTO usuarios (nome, login, pwd, email, status, tipo, id_setor_suporte) values('$nome', '$login', '81dc9bdb52d04dc20036dbd8313ed055', '$email', 'A', '$tipo', $setor)";
            }
            if (mysqli_query($connect, $sql)) {
                header("Refresh:0; url=./?status=newaccok");
            } else {
                $avisos[] = "<span class='text-danger'><li>Erro - " . mysqli_errno($connect) . "</li></span>";
            }
        }
    }
}
//Atualizaçao de dados em conta
if (isset($_POST['btn-update-conta'])) {
    $nome = $purifier->purify(@mysqli_escape_string($connect, $_POST['editar_conta_nome']));
    $email = $purifier->purify(@mysqli_escape_string($connect, $_POST['editar_conta_email']));
    $tipo = $purifier->purify(@mysqli_escape_string($connect, $_POST['editar_conta_tipo']));
    $setor_sup = $purifier->purify(@mysqli_escape_string($connect, $_POST['editar_conta_setor']));
    $id_conta = $purifier->purify(@mysqli_escape_string($connect, $_POST['id-update']));
    $nome_antes = $purifier->purify(@mysqli_escape_string($connect, $_POST['nome-update']));
    $erros = 0;
    if (empty($nome) || empty($tipo) || empty($setor_sup)) {
        $avisos[] = "<span class='text-danger'><li>Preencha todos os campos!</li></span>";
    } else {
        $sql = "SELECT * FROM vw_usuarios WHERE email = '$email' and id != $id_conta";
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) > 0) {
            $avisos[] = "<span class='text-danger'><li>Uma conta com esse email ja existe!</li></span>";
            $erros++;
        }
        $sql = "SELECT * FROM vw_usuarios WHERE nome = '$nome' and id != $id_conta";
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) > 0) {
            $avisos[] = "<span class='text-danger'><li>Uma conta com esse nome ja existe!</li></span>";
            $erros++;
        }
        if ($erros == 0) {
            if(empty($email)){
                $sql = "UPDATE usuarios SET nome = '$nome', email = NULL, tipo = '$tipo', id_setor_suporte = $setor_sup WHERE id = $id_conta";
            }else{
                $sql = "UPDATE usuarios SET nome = '$nome', email = '$email', tipo = '$tipo', id_setor_suporte = $setor_sup WHERE id = $id_conta";
            }
            if (mysqli_query($connect, $sql)) {
                if ($id_conta == $_SESSION['id'] && $tipo == "SUPORTE") {
                    $_SESSION['type'] = "SUPORTE";
                }
                header("Refresh:0; url=./?status=editok&nomeAnt=$nome_antes");
            } else {
                $avisos[] = "<span class='text-danger'><li>Erro - " . mysqli_errno($connect) . "</li></span>";
            }
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
            <?php echo $menu; ?>
            <!-- GERENCIAMENTO -->

            <div class="col-lg-10 col-md-9">
                <div class="my-4 mx-2 p-2 ps-3 text-light bg-blue rounded shadow">
                    <h1 class="mb-0">Gerenciamento de contas</h1>
                </div>
                <div class="row mx-2 mb-4">

                    <!-- ----------COLUNA VISUALIZAÇAO DE CONTAS---------- -->

                    <div class="col-lg-8 col-md-12">
                        <h3>Contas</h3>
                        <div class="row">
                            <div class="col-sm-12 col-md-7 col-lg-5 my-2">
                                <form action="" method="GET">
                                    <input type="text" name="pNomeConta" class="form-control"
                                        placeholder="Pesquisar por nome">
                                </form>
                            </div>
                        </div>

                        <?php
                        foreach ($avisos as $aviso) {
                            echo $aviso;
                        }
                        ?>
                        <span class="text-muted">Total de contas encontradas -
                            <?php echo $tamanho ?>
                        </span>

                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Login</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                foreach ($sql_usuarios as $row) {
                                    echo "<tr>
                                        <th><a href='' class='text-decoration-none' data-bs-toggle='modal'
                                                data-bs-target='#modal_conta_" . $row['id'] . "'><i class='fa-solid fa-user-gear'></i></a>
                                        </th>
                                        <td>" . $row['nome'] . "</td>
                                        <td>" . $row['login'] . "</td>
                                        <td>" . $row['email'] . "</td>
                                        <td>" . $row['tipo'] . "</td>
                                        </tr>";
                                }

                                ?>
                            </tbody>
                        </table>
                        <ul class="pagination justify-content-center">
                            <?php 
                            // --------------------- Print de paginacao ---------------------
                            $linkPri = "<li class=\"page-item\"><a href=\"?pagina=1\" class=\"page-link\"> Inicio </a></li>";
                            $linkAnt = "<li class=\"page-item\"><a href=\"?pagina=" . $pagina - 1 . "\" class=\"page-link\"> << </a></li>";
                            $linkUlt = "<li class=\"page-item\"><a href=\"?pagina=$totalPaginas\" class=\"page-link\"> Fim </a></li>";
                            $linkProx = "<li class=\"page-item\"><a href=\"?pagina=" . $pagina + 1 . "\" class=\"page-link\"> >> </a></li>";
                            if (!empty($nomeConta)) {
                                $linkPri = "<li class=\"page-item\"><a href=\"?pagina=1&pNomeConta=$nomeConta\" class=\"page-link\"> Inicio </a></li>";
                                $linkAnt = "<li class=\"page-item\"><a href=\"?pagina=" . $pagina - 1 . "&pNomeConta=$nomeConta\" class=\"page-link\"> << </a></li>";
                                $linkUlt = "<li class=\"page-item\"><a href=\"?pagina=$totalPaginas&pNomeConta=$nomeConta\" class=\"page-link\"> Fim </a></li>";
                                $linkProx = "<li class=\"page-item\"><a href=\"?pagina=" . $pagina + 1 . "&pNomeConta=$nomeConta\" class=\"page-link\"> >> </a></li>";
                            }

                            if ($pagina > 1) {
                                echo $linkPri;
                                echo $linkAnt;
                            } else {
                                echo "<li class=\"page-item disabled\"><a href=\"\" class=\"page-link\"> Inicio </a></li>";
                                echo "<li class=\"page-item disabled\"><a href=\"\" class=\"page-link\"> << </a></li>";
                            }

                            for ($i = 1; $i <= $totalPaginas; $i++) {
                                if ($i >= ($pagina - $tamanhoMenu) && $i <= ($pagina + $tamanhoMenu)) {
                                    if ($i == $pagina) {
                                        echo "<li class=\"page-item disabled\"><a href=\"\" class=\"page-link\"> $i </a></li>";
                                    } else {
                                        if (!empty($nomeConta)) {
                                            $linkPag = "<li class=\"page-item\"><a href=\"?pagina=$i&pNomeConta=$nomeConta\" class=\"page-link\"> $i </a></li>";
                                        } else {
                                            $linkPag = "<li class=\"page-item\"><a href=\"?pagina=$i\" class=\"page-link\"> $i </a></li>";
                                        }
                                        echo $linkPag;
                                    }
                                }
                            }
                            if ($pagina < $totalPaginas) {
                                echo $linkProx;
                                echo $linkUlt;
                            } else {
                                echo "<li class=\"page-item disabled\"><a href=\"\" class=\"page-link\"> >> </a></li>";
                                echo "<li class=\"page-item disabled\"><a href=\"\" class=\"page-link\"> Fim </a></li>";
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- ----------MODAL EDIÇAO DE CONTAS---------- -->

                    <?php

                    foreach ($sql_usuarios as $row) {
                        //Carregando select tipo conta
                        if ($row['tipo'] == "ADM") {
                            $option = "<option selected value='ADM'>Administrador</option> <option value='SUPORTE'>Suporte</option>";
                        } else {
                            $option = "<option value='ADM'>Administrador</option> <option selected value='SUPORTE'>Suporte</option>";
                        }
                        //carregando select setor da conta
                        $option_set = "";
                        foreach ($sql_setores as $row_set) {
                            if ($row_set['suporte'] == "S" && $row_set['status'] == "A") {
                                if ($row['setor'] == $row_set['nome']) {
                                    $option_set .= "<option selected value='" . $row['setor_id'] . "'>" . $row['setor'] . "</option>";
                                } else {
                                    $option_set .= "<option value='" . $row_set['id'] . "'>" . $row_set['nome'] . "</option>";
                                }
                            }
                        }
                        // Carregando botao status de conta
                        if ($row['status'] == "A") {
                            $button_status = "<button name='btn-desativar-conta' type='submit' class='btn btn-danger'><i class='fa-solid fa-user-large-slash'></i> Desativar conta</button>";
                        } else {
                            $button_status = "<button name='btn-ativar-conta' type='submit' class='btn btn-success'><i class='fa-solid fa-user-check'></i> Ativar conta</button>";
                        }
                        echo "<div class='modal fade' id='modal_conta_" . $row['id'] . "' tabindex='-1' aria-hidden='true'>
                        <div class='modal-dialog modal-dialog-centered'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h3>ID: " . $row['id'] . "</h3>
                                </div>
                                <div class='modal-body'>
                                    <form action='" . $_SERVER['PHP_SELF'] . "' method='POST'>
                                        <div class='my-3'>
                                            <label for='editar_conta_nome'>Nome:</label>
                                            <input type='text' id='editar_conta_nome' name='editar_conta_nome' class='form-control'
                                                value='" . $row['nome'] . "'>
                                        </div>
                                        <div class='mb-3'>
                                            <label for='editar_conta_email'>Email:</label>
                                            <input type='text' id='editar_conta_email' name='editar_conta_email' class='form-control'
                                                value='" . $row['email'] . "'>
                                        </div>
                                        <div class='mb-3'>
                                            <label for='editar_conta_tipo'>Tipo:</label>
                                            <select id='editar_conta_tipo' name='editar_conta_tipo' class='form-select'>" . $option . "</select>
                                        </div>
                                        <div class='mb-3'>
                                            <label for='editar_conta_setor'>Setor Suporte:</label>
                                            <select id='editar_conta_setor' name='editar_conta_setor' class='form-select'>
                                                " . $option_set . "
                                            </select>
                                        </div>
                                        <div class='mb-3'>
                                            <input type='hidden' name='id-update' value='" . $row['id'] . "'>
                                            <input type='hidden' name='nome-update' value='" . $row['nome'] . "'>
                                            <button name='btn-update-conta' type='submit' class='btn btn-success'><i class='fa-solid fa-floppy-disk'></i> Atualizar</button>
                                        </div>
                                    </form>
                                    <hr>
                                    <h5>Opções:</h5>
                                    <div class='mt-3'>
                                        <form action='" . $_SERVER['PHP_SELF'] . "' method='POST'>
                                            <input type='hidden' name='id-reset' value='" . $row['id'] . "'>
                                            <input type='hidden' name='nome-reset' value='" . $row['nome'] . "'>
                                            <button name='btn-reset-conta' type='submit' class='btn btn-primary'><i class='fa-solid fa-key'></i> Resetar senha</button>
                                            " . $button_status . "
                                        </form>
                                    </div>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-danger' data-bs-dismiss='modal'><i class='fa-solid fa-xmark'></i> Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>";
                    }
                    ?>

                    <!-- ----------COLUNA CRIAÇAO DE CONTAS---------- -->

                    <div class="col-lg-4 col-md-12 border-start">
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                            <h3>Criar uma nova conta</h3>
                            <div class="mb-3">
                                <label for="nome_conta">Nome:</label>
                                <input type="text" class="form-control" id="nome_conta" name="nome_conta">
                            </div>
                            <div class="mb-3">
                                <label for="login_conta">Login:</label>
                                <input type="text" class="form-control" id="login_conta" name="login_conta">
                            </div>
                            <div class="mb-3">
                                <label for="email_conta">Email:</label>
                                <input type="email" class="form-control" id="email_conta" name="email_conta">
                            </div>
                            <div class="mb-3">
                                <label for="tipo_conta">Tipo:</label>
                                <select id="tipo_conta" class="form-select" name="tipo_conta">
                                    <?php 
                                    //----------------------------------------------------------------------------------------------------
                                    //----------- ADD OPCAO CASO MODIFIQUE O CODIGO E O BD ADICIONANDO UM NOVO TIPO DE CONTA -------------
                                    //----------------------------------------------------------------------------------------------------
                                    ?>
                                    <option disabled selected>Selecione..</option>
                                    <option value="ADM">Administrador</option>
                                    <option value="SUPORTE">Suporte</option>
                                    <option value="MAN">Manutenção</option>
                                    <option value="MOD">Moderador</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="setor_conta">Setor de suporte:</label>
                                <select name="setor_conta" id="setor_conta" class="form-select">
                                    <option selected disabled>Selecione..</option>
                                    <?php
                                    foreach ($sql_setores as $row) {
                                        if ($row['suporte'] == "S" && $row['status'] == "A") {
                                            echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="text-center d-grid mb-3">
                                <button type="submit" class="btn btn-primary" name="btn-criarConta"><i
                                        class="fa-solid fa-user-plus"></i>
                                    Criar conta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>

</html>