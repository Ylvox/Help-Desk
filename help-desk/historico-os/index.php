<?php

require_once '../php/sql.php';
require_once '../php/carregaHistorico.php';

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
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
    </div>
    <h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../restrito/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-shield-halved'></i> Restrito</a>
    </div>
</div>";
$menu[1] = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
    <h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../' class='btn btn-darker text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
        <a href='../listar-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
    </div>
    <h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../dashboard/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-chart-simple'></i> Dashboard</a>
        <a href='../conta/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-user'></i> Conta</a>
        <a href='../gerenciamento/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-database'></i> Gerenciamento</a>
        <a href='../php/logout.php' class='btn btn-darker text-start my-2'><i class='fa-solid fa-arrow-right-from-bracket'></i> Sair</a>
    </div>
</div>";
$menu[2] = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
    <h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../' class='btn btn-darker text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
        <a href='../listar-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
    </div>
    <h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../dashboard/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-chart-simple'></i> Dashboard</a>
        <a href='../conta/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-user'></i> Conta</a>
        <a href='../php/logout.php' class='btn btn-darker text-start my-2'><i class='fa-solid fa-arrow-right-from-bracket'></i> Sair</a>
    </div>
</div>";

$sql = "SELECT * FROM setores WHERE suporte = 'S' AND status = 'A' ORDER BY nome";
$s_setDestino = mysqli_query($connect, $sql);

$sql = "SELECT * FROM setores WHERE status = 'A' ORDER BY nome";
$s_setSolicitante = mysqli_query($connect, $sql);

$nome_set_priorizado;

foreach ($s_setDestino as $set) {
    if ($set['id'] == $id_set_priorizado) {
        $nome_set_priorizado = $set['nome'];
        break;
    } else if ($id_set_priorizado == 0) {
        $nome_set_priorizado = "";
    }
}

$tamanhoMenu = 2; // Tamanho menu de paginacao para cada lado quando tem varias paginas
$qnt = 10; // Quantidade maxima de resultados por pagina
$pagina = (isset($_GET['pagina']) && !empty($_GET['pagina'])) ? (int) $_GET['pagina'] : 1;
$inicio = ($qnt * $pagina) - $qnt;

$setor_pesquisa = @mysqli_escape_string($connect, $_GET['setor']);
$protocolo_pesquisa = @mysqli_escape_string($connect, $_GET['protocolo']);
$setor_solicitante = @mysqli_escape_string($connect, $_GET['setorSolic']);
if (isset($_GET['setor']) || isset($_GET['protocolo']) || isset($_GET['setorSolic'])) {
    if ($setor_pesquisa == 0 && $setor_solicitante == 0) {
        $sql = "SELECT * FROM vw_chamados WHERE protocolo like '%$protocolo_pesquisa%' AND status = 'Finalizado' ORDER BY data_finalizado DESC LIMIT $inicio,$qnt";
        $sqlQnt = "SELECT id_chamado FROM vw_chamados WHERE protocolo like '%$protocolo_pesquisa%' AND status = 'Finalizado'";
    }else if($setor_pesquisa == 0 && $setor_solicitante != 0){
        $sql = "SELECT * FROM vw_chamados WHERE protocolo like '%$protocolo_pesquisa%' AND setor_solicitante = '$setor_solicitante' AND status = 'Finalizado' ORDER BY data_finalizado DESC LIMIT $inicio,$qnt";
        $sqlQnt = "SELECT id_chamado FROM vw_chamados WHERE protocolo like '%$protocolo_pesquisa%' AND setor_solicitante = '$setor_solicitante' AND status = 'Finalizado'";
    }else if($setor_pesquisa != 0 && $setor_solicitante == 0){
        $sql = "SELECT * FROM vw_chamados WHERE protocolo like '%$protocolo_pesquisa%' AND setor_destino like '%$setor_pesquisa%' AND status = 'Finalizado' ORDER BY data_finalizado DESC LIMIT $inicio,$qnt";
        $sqlQnt = "SELECT id_chamado FROM vw_chamados WHERE protocolo like '%$protocolo_pesquisa%' AND setor_destino like '%$setor_pesquisa%' AND status = 'Finalizado'";
    }else {
        $sql = "SELECT * FROM vw_chamados WHERE setor_destino like '%$setor_pesquisa%' AND setor_solicitante = '$setor_solicitante' AND protocolo like '%$protocolo_pesquisa%' AND status = 'Finalizado' ORDER BY data_finalizado DESC LIMIT $inicio,$qnt";
        $sqlQnt = "SELECT id_chamado FROM vw_chamados WHERE setor_destino like '%$setor_pesquisa%' AND setor_solicitante = '$setor_solicitante' AND protocolo like '%$protocolo_pesquisa%' AND status = 'Finalizado'";
    }
} else {
    $sql = "SELECT * FROM vw_chamados WHERE setor_destino like '%$nome_set_priorizado%' AND status = 'Finalizado' ORDER BY data_finalizado DESC LIMIT $inicio,$qnt";
    $sqlQnt = "SELECT id_chamado FROM vw_chamados WHERE setor_destino like '%$nome_set_priorizado%' AND status = 'Finalizado'";
}
$resultOs = [];
if (mysqli_query($connect, $sql)) {
    $resultOs = mysqli_query($connect, $sql);
} else {
    echo "<script>alert('Ocorreu um erro ao carregar o banco de dados');</script>";
}
$resultQnt = mysqli_query($connect, $sqlQnt);
$qntSql = mysqli_num_rows($resultQnt);
$totalPaginas = ceil($qntSql/$qnt);

function checkAnexo($anexo)
{
    if (empty($anexo)) {
        return "<span>Sem anexo</span>";
    } else {
        return "<a href='$anexo' target=”_blank”>$anexo</a>";
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
            if (@$_SESSION['logged']) {
                if ($_SESSION['type'] == "ADM") {
                    echo $menu[1];
                } else {
                    echo $menu[2];
                }
            } else {
                echo $menu[0];
            }
            ?>
            <!-- HISTORICO DE CHAMADOS -->

            <div class="col-lg-10 col-md-9">
                <div class="my-4 mx-2 p-2 ps-3 text-light bg-blue rounded shadow">
                    <h1 class="mb-0">Histórico O.S</h1>
                </div>

                <div class="bg-light my-4 mx-2 p-2 rounded-3 px-3 shadow">

                    <!-- PESQUISA DE CHAMADOS -->

                    <div class="row">
                        <div class="col-lg-10 col-md-12">
                            <h4>Pesquisar chamados por:</h4>
                            <form action="./" method="GET">
                                <div class="my-2 input-group">
                                    <span class="input-group-text">Protocolo:</span>
                                    <input type="text" class="form-control" aria-describedby="protoc_txt" maxlength="16"
                                        placeholder="0000000000000000" name="protocolo"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                    <span class="input-group-text">Chamado:</span>
                                    <select name="setor" id="setor_txt" aria-describedby="setor_txt"
                                        class="form-select">
                                        <?php
                                        $controle = false;
                                        if (($id_set_priorizado == 0 && empty($_GET['setor'])) || $_GET['setor'] == "0") {
                                            echo "<option selected value='0'>Todos</option>";
                                            $controle = true;
                                        } else {
                                            echo "<option value='0'>Todos</option>";
                                        }
                                        foreach ($s_setDestino as $setor) {
                                            if (!empty($_GET['setor']) && $_GET['setor'] == $setor['nome'] && !$controle) {
                                                echo "<option selected value='" . $setor['nome'] . "'>" . $setor['nome'] . "</option>";
                                                $controle = true;
                                            } else if ($setor['id'] == $id_set_priorizado && !$controle && empty($_GET['setor'])) {
                                                echo "<option selected value='" . $setor['nome'] . "'>" . $setor['nome'] . "</option>";
                                                $controle = true;
                                            } else {
                                                echo "<option value='" . $setor['nome'] . "'>" . $setor['nome'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <span class="input-group-text" id="setor_txt">Setor:</span>
                                    <select name="setorSolic" id="SetorSolic" aria-describedby="setor_txt" class="form-select">
                                    <?php
                                        $controle2 = false;
                                        if ((empty($_GET['setorSolic'])) || $_GET['setorSolic'] == "0") {
                                            echo "<option selected value='0'>Todos</option>";
                                            $controle2 = true;
                                        } else {
                                            echo "<option value='0'>Todos</option>";
                                        }
                                        foreach ($s_setSolicitante as $setor) {
                                            if (!empty($_GET['setorSolic']) && $_GET['setorSolic'] == $setor['nome'] && !$controle2) {
                                                echo "<option selected value='" . $setor['nome'] . "'>" . $setor['nome'] . "</option>";
                                                $controle2 = true;
                                            } else {
                                                echo "<option value='" . $setor['nome'] . "'>" . $setor['nome'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" class="btn btn-outline-primary">Pesquisar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <h6>Legenda:</h6>
                    <span class="me-2 text-success fs-5"><i class="fa-solid fa-check"></i></span>
                    <span class="me-2 text-success">Atendimento finalizado</span>
                    <p class="text-muted mt-3">Total de Ordens de Serviço finalizadas - <?php echo $qntSql;?></p>

                    <!-- TABELA HISTORICO CHAMADOS -->

                    <table class="table table-striped table-hover">
                        <thead>
                            <th>Protocolo</th>
                            <th>Estabelecimento</th>
                            <th>Data/Hora</th>
                            <th>Setor</th>
                            <th>Equipamento</th>
                            <th>Usuario Atendimento</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($resultOs as $os) {
                                echo "
                                <tr>
                                    <th><a href='' class='text-decoration-none' data-bs-toggle='modal'
                                            data-bs-target='#modal_historico_".$os['id_atendimento']."'>".$os['protocolo']."</a></th>
                                    <td>".$os['estabelecimento']."</td>
                                    <td>".$os['data_finalizado']."</td>
                                    <td>".$os['setor_solicitante']."</td>
                                    <td>".$os['equipamento']."</td>
                                    <td>".$os['usuario_atendimento']."</td>
                                    <td class='text-success fs-5'><i class='fa-solid fa-check'></i></td>
                                </tr>
                                ";
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
                            if ((!empty($setor_pesquisa) || !empty($protocolo_pesquisa) || !empty($setor_solicitante) || $setor_pesquisa == 0 || $setor_solicitante == 0)) {
                                $linkPri = "<li class=\"page-item\"><a href=\"?pagina=1&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa&setorSolic=$setor_solicitante\" class=\"page-link\"> Inicio </a></li>";
                                $linkAnt = "<li class=\"page-item\"><a href=\"?pagina=" . $pagina - 1 . "&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa&setorSolic=$setor_solicitante\" class=\"page-link\"> << </a></li>";
                                $linkUlt = "<li class=\"page-item\"><a href=\"?pagina=$totalPaginas&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa&setorSolic=$setor_solicitante\" class=\"page-link\"> Fim </a></li>";
                                $linkProx = "<li class=\"page-item\"><a href=\"?pagina=" . $pagina + 1 . "&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa&setorSolic=$setor_solicitante\" class=\"page-link\"> >> </a></li>";
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
                                        if (!empty($setor_pesquisa) || !empty($protocolo_pesquisa) || $setor_pesquisa == 0) {
                                            $linkPag = "<li class=\"page-item\"><a href=\"?pagina=$i&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa&setorSolic=$setor_solicitante\" class=\"page-link\"> $i </a></li>";
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
                    <?php 
                    //MODAL DE HISTORICO DE CHAMADO
                    foreach ($resultOs as $os) {
                        echo "
                                <div class='modal fade' id='modal_historico_" . $os['id_atendimento'] . "' tabindex='-1' aria-hidden='true'>
                                    <div class='modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h4 class='mb-0'>PROTOCOLO: " . $os['protocolo'] . "</h4>
                                            </div>
                                            <div class='modal-body m-0 fs-6'>
                                                <b>Estabelecimento: </b><span>" . $os['estabelecimento'] . "</span>
                                                <br>
                                                <b>Nome: </b><span>" . $os['nome_solicitante'] . "</span>
                                                <br>
                                                <b>Setor: </b><span>" . $os['setor_solicitante'] . "</span>
                                                <br>
                                                <b>Equipamento: </b><span>" . $os['equipamento'] . "</span>
                                                <br>
                                                <b>Ramal: </b><span>" . $os['ramal'] . "</span>
                                                <br>
                                                <b>Descrição: </b><span style='white-space: pre-line'>" . $os['descricao'] . "</span>
                                                <br>
                                                <b>Endereço IP: </b><span>" . $os['ip'] . "</span>
                                                <br>
                                                <b>Computador: </b><span>" . $os['computador'] . "</span>
                                                <br>
                                                <b>Data/Hora: </b><span>" . $os['data_abertura'] . "</span>
                                                <br>
                                                <b>Status: </b><span class='text-success'>".$os['status']."</span>
                                                <br>
                                                <b>Usuario Atendimento: </b><span>" . $os['usuario_atendimento'] . "</span>
                                                <br>
                                                <b>Inicio do atendimento: </b><span>" . $os['data_atendimento'] . "</span>
                                                <br>
                                                <b>Fim do atendimento: </b><span>" . $os['data_finalizado'] . "</span>
                                                <br>
                                                <b>Anexo: </b>" . checkAnexo($os['anexo']) . "
                                                <br>
                                                <hr>
                                                <h4>Historico:</h4>";
                                                carregaHistorico($os['id_chamado']);
                                            echo "</div>
                                            <div class='modal-footer'>
                                                <button type='button' class='btn btn-danger' data-bs-dismiss='modal'><i
                                                        class='fa-solid fa-xmark'></i> Fechar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>


</body>

</html>