<?php

require_once '../php/sql.php';
require_once '../php/Purifier/HTMLPurifier.auto.php';
require_once '../php/phpmailer/class.phpmailer.php';
require_once '../php/phpmailer/class.smtp.php';
require_once '../php/carregaHistorico.php';

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

$avisos = array();
$ids_atend = array();

//CARREGAMENTO DE MENU
// 0 - NAO LOGADO
// 1 - LOGADO ADM
// 2 - LOGADO SUP
$menu = array();
$menu[0] = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
    <h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='../' class='btn btn-darker text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='../historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
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
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='../historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
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
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='../historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
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
if (isset($_GET['setor']) || isset($_GET['protocolo'])) {
    if ($setor_pesquisa == 0) {
        $sql = "SELECT * FROM vw_chamados WHERE protocolo like '%$protocolo_pesquisa%' AND data_finalizado IS NULL ORDER BY data_abertura DESC LIMIT $inicio,$qnt";
        $sqlQnt = "SELECT id_chamado FROM vw_chamados WHERE protocolo like '%$protocolo_pesquisa%' AND setor_transferencia  IS NULL AND data_finalizado IS NULL";
    } else {
        $sql = "SELECT * FROM vw_chamados WHERE setor_destino like '%$setor_pesquisa%' AND protocolo like '%$protocolo_pesquisa%' AND data_finalizado IS NULL ORDER BY data_abertura DESC LIMIT $inicio,$qnt";
        $sqlQnt = "SELECT id_chamado FROM vw_chamados WHERE setor_destino like '%$setor_pesquisa%' AND protocolo like '%$protocolo_pesquisa%' AND setor_transferencia IS NULL AND data_finalizado IS NULL";
    }
} else {
    $sql = "SELECT * FROM vw_chamados WHERE setor_destino like '%$nome_set_priorizado%' AND data_finalizado IS NULL ORDER BY data_abertura DESC LIMIT $inicio,$qnt";
    $sqlQnt = "SELECT id_chamado FROM vw_chamados WHERE setor_destino like '%$nome_set_priorizado%' AND setor_transferencia IS NULL AND data_finalizado IS NULL";
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

function setorTransf($setores, $nome_setor)
{
    $conteudo = "";
    foreach ($setores as $setor) {
        if ($setor['nome'] != $nome_setor) {
            $conteudo .= "<option value='" . $setor['id'] . "'>" . $setor['nome'] . "</option>";
        }
    }
    return $conteudo;
}
function checkAnexo($anexo)
{
    if (empty($anexo)) {
        return "<span>Sem anexo</span>";
    } else {
        return "<a href='$anexo' target=”_blank”>$anexo</a>";
    }
}
function checkStatus($status, $tipo)
{
    if ($tipo == "texto") {
        if ($status == "Aguardando") {
            return "<span class='text-danger'>$status</span>";
        } else {
            return "<span class='text-warning'>$status</span>";
        }
    } else if ($tipo == "icone") {
        if ($status == "Aguardando") {
            return "<i class='fa-solid fa-rotate text-danger fs-5'></i>";
        } else {
            return "<i class='fa-solid fa-magnifying-glass text-warning fs-5'></i>";
        }
    }
}
function checkTransf($status, $id)
{
    if ($status == "Em atendimento") {
        return "<a class='btn btn-warning btn-sm' style='width:33px' data-bs-toggle='modal' data-bs-target='#modal_transf_" . $id . "'><i class='fa-solid fa-arrow-up-from-bracket'></i></a>";
    } else {
        return "<a class='btn btn-warning btn-sm disabled' style='width:33px'><i class='fa-solid fa-arrow-up-from-bracket'></i></a>";
    }
}
function checkAtend($status)
{
    if ($status == "Aguardando") {
        return "<button class='btn btn-success btn-sm' type='submit'
        style='width:33px' name='btn_os_iniciar'><i class='fa-solid fa-check'></i></button>";
    } else {
        return "<button class='btn btn-danger btn-sm' type='submit'
        style='width:33px' name='btn_os_finalizar'><i class='fa-solid fa-xmark'></i></button>";
    }
}
function checkLogin($status, $id_atend, $id_chamado, $setor, $tipo, $setores)
{
    if ($tipo == "acoes") {
        if (@$_SESSION['logged']) {
            return "<form action='./' method='POST'>" . checkTransf($status, $id_atend) . "
            <input type='hidden' name='id_chamado' value='$id_chamado'>
            <input type='hidden' name='id_atend' value='$id_atend'>
            <input type='hidden' name='setor_dest' value='$setor'>
            " . checkAtend($status) . "</form>";
        }
    } else if ($tipo == "modal") {
        if (@$_SESSION['logged']) {
            $conteudo = "
            <form action='./' method='post'>
            <div class='my-3'>
                <label for='os_obs'>Observações:</label>
                <textarea name='os_obs' id='os_obs' rows='5' class='form-control'
                    style='resize:none'></textarea>
                <input type='hidden' name='id_chamado' value='" . $id_chamado . "'>
                <input type='hidden' name='id_atend' value='" . $id_atend . "'>
                <input type='hidden' name='setor_dest' value='$setor'>
            </div>
            <div class='mb-3 text-center d-grid'>
            ";
            if ($status == "Aguardando") {
                $conteudo .= "
                        <button type='submit' class='btn btn-success' name='btn_os_iniciar'><i
                            class='fa-solid fa-check'></i> Iniciar atendimento</button>
                    </div>
                </form>
                ";
            } else {
                $conteudo .= "
                        <button type='submit' class='btn btn-primary' name='btn_os_obs'><i
                            class='fa-solid fa-check'></i> Atualizar</button>
                        <button type='submit' class='btn btn-danger mt-3' name='btn_os_finalizar'><i 
                            class='fa-solid fa-xmark'></i> Finalizar atendimento</button>
                    </div>
                </form>
                ";
            }
            $conteudo .= "<form action='./' method='POST' class='row'>
            <div class='col-lg-4 col-md-12'>
                <div class='input-group my-2'>
                    <span class='input-group-text' id='set_txt'>Setor</span>
                    <select name='repo_setor' id='setor_destino_$id_atend' aria-describedby='set_txt'
                        class='form-select'>
                        <option selected disabled>Selecione..</option>";
        
            foreach ($setores as $s_setor) {
                $conteudo .= "<option value='" . $s_setor['id'] . "'>" . $s_setor['nome'] . "</option>";
            }

            $conteudo .= "
                    </select>
                </div>
            </div>
            <div class='col-lg-4 col-md-12'>
                <div class='my-2 input-group' id='retornoPecas_$id_atend'>
                    <span class='input-group-text' id='peca_txt'>Peça/Material</span>
                    <select disabled name='repo_peca' id='novaReposicao_$id_atend' class='form-select'>
                        <option selected disabled>Selecione..</option>
                    </select>
                </div>
            </div>
            <div class='col-lg-4 col-md-12'>
                <div class='my-2 input-group'>
                    <span class='input-group-text' id='qnt_txt'>Quantidade</span>
                    <input type='number' disabled placeholder='Un/Cm/M' name='repo_unidade' class='form-control' id='repo_unidade_$id_atend'>
                </div>
            </div>
            <div class='col-12 d-grid text-center my-2'>
            <input type='hidden' name='btn_repo'>
            <input type='hidden' name='setor_dest' value='$setor'>
            <input type='hidden' name='id_chamado' value='" . $id_chamado . "'>
            <input type='hidden' name='id_atend' value='" . $id_atend . "'>
                <button type='submit' class='btn btn-warning' onClick='this.disabled=true; this.form.submit();'><i
                        class='fa-solid fa-pen-to-square'></i> Lançar Reposição</button>
            </div>
        </form>";
        } else {
            $conteudo = "
            <form action='./' method='post'>
                <div class='my-3'>
                    <label for='os_obs'>Observações:</label>
                    <textarea name='os_obs' id='os_obs' rows='5' class='form-control'
                        style='resize:none'></textarea>
                    <input type='hidden' name='id_chamado' value='" . $id_chamado . "'>
                    <input type='hidden' name='btn_os_obs'>
                </div>
                <div class='mb-3 text-center d-grid'>
                    <button type='submit' class='btn btn-primary' onClick='this.disabled=true; this.form.submit();'><i
                                class='fa-solid fa-check'></i> Atualizar</button>
                </div>
            </form>
            ";
        }
        return $conteudo;
    } else if ($tipo == "logado") {
        if (@$_SESSION['logged']) {
            return true;
        } else {
            return false;
        }
    }
}

//ATENDIMENTO DE CHAMADOS
if (isset($_POST['btn_os_iniciar'])) {
    $os_obs = $purifier->purify(@mysqli_escape_string($connect, $_POST['os_obs']));
    $id_chamado = $purifier->purify(@mysqli_escape_string($connect, $_POST['id_chamado']));
    $id_atend = $purifier->purify(@mysqli_escape_string($connect, $_POST['id_atend']));
    $nome_setor = $purifier->purify(@mysqli_escape_string($connect, $_POST['setor_dest']));
    $sql = "SELECT status FROM vw_chamados WHERE id_chamado = $id_chamado and id_atendimento = $id_atend and setor_destino = '$nome_setor'";
    $sql2 = "";
    if (checkLogin(null, null, null, null, "logado", null)) {
        if (mysqli_query($connect, $sql)) {
            $result = mysqli_query($connect, $sql);
            $testeStatus = mysqli_fetch_assoc($result);
            if (mysqli_num_rows($result) == 1) {
                if ($testeStatus['status'] == "Aguardando") {
                    if (!empty(@$_SESSION['id_setor'])) {
                        foreach ($s_setDestino as $setor_dest) {
                            if ($setor_dest['nome'] == $nome_setor) {
                                if ($_SESSION['id_setor'] == $setor_dest['id']) {
                                    if (empty($os_obs)) {
                                        $sql = "UPDATE chamados_atendimento SET id_usuario_atendimento = " . $_SESSION['id'] . ", data_atendimento = '" . date("Y-m-d H:i:s") . "', status = 'Em atendimento' WHERE id = $id_atend";
                                    } else {
                                        $sql = "UPDATE chamados_atendimento SET id_usuario_atendimento = " . $_SESSION['id'] . ", data_atendimento = '" . date("Y-m-d H:i:s") . "', status = 'Em atendimento' WHERE id = $id_atend";
                                        $sql2 = "INSERT INTO chamados_historico (id_chamado, id_usuario, conteudo, data_mensagem) VALUES ($id_chamado, " . $_SESSION['id'] . ", '$os_obs', '" . date("Y-m-d H:i:s") . "')";
                                        if (!mysqli_query($connect, $sql2)) {
                                            $avisos[] = "<script>alert('ERRO - " . mysqli_errno($connect) . " - Não foi possivel cadastrar sua mensagem')</script>";
                                        }
                                    }
                                    if (mysqli_query($connect, $sql)) {
                                        header("Refresh:0; url=./");
                                    } else {
                                        $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Erro - " . mysqli_errno($connect) . "</div>";
                                    }
                                } else {
                                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Você só pode atender chamados do seu setor!</div>";
                                }
                            }
                        }
                    } else {
                        $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Sessão inválida!</div>";
                    }
                } else {
                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Este chamado já está sendo atendido!</div>";
                }
            } else {
                $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>POST inválido</div>";
            }
        }
    } else {
        header('Location: ../restrito/');
    }
}

//FINALIZACAO DE CHAMADOS
if (isset($_POST['btn_os_finalizar'])) {
    $id_chamado = $purifier->purify(@mysqli_escape_string($connect, $_POST['id_chamado']));
    $id_atend = $purifier->purify(@mysqli_escape_string($connect, $_POST['id_atend']));
    $nome_setor = $purifier->purify(@mysqli_escape_string($connect, $_POST['setor_dest']));
    $sql = "SELECT status FROM vw_chamados WHERE id_chamado = $id_chamado and id_atendimento = $id_atend and setor_destino = '$nome_setor'";
    if (checkLogin(null, null, null, null, "logado", null)) {
        if (mysqli_query($connect, $sql)) {
            $result = mysqli_query($connect, $sql);
            $testeStatus = mysqli_fetch_assoc($result);
            if (mysqli_num_rows($result) == 1) {
                if ($testeStatus['status'] != "Finalizado" && $testeStatus['status'] == "Em atendimento") {
                    if (!empty(@$_SESSION['id_setor'])) {
                        foreach ($s_setDestino as $setor_dest) {
                            if ($setor_dest['nome'] == $nome_setor) {
                                if ($_SESSION['id_setor'] == $setor_dest['id']) {
                                    $data_atual = date("Y-m-d H:i:s");
                                    $sql = "UPDATE chamados SET data_finalizado = '$data_atual' WHERE id = $id_chamado";
                                    $sql2 = "UPDATE chamados_atendimento SET data_finalizado = '$data_atual', status = 'Finalizado' WHERE id = $id_atend";
                                    if (!mysqli_query($connect, $sql)) {
                                        $avisos[] = "<script>alert('ERRO - " . mysqli_errno($connect) . " - Erro ao atualizar banco de dados sql')</script>";
                                    }
                                    if (!mysqli_query($connect, $sql2)) {
                                        $avisos[] = "<script>alert('ERRO - " . mysqli_errno($connect) . " - Erro ao atualizar banco de dados sql2')</script>";
                                    }
                                    header("Refresh:0; url=./");
                                } else {
                                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Você só pode finalizar chamados do seu setor!</div>";
                                }
                            }
                        }
                    } else {
                        $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Sessão inválida!</div>";
                    }
                } else {
                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Este chamado já foi finalizado ou ainda não foi atendido!</div>";
                }
            } else {
                $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>POST inválido</div>";
            }
        }
    } else {
        header('Location: ../restrito/');
    }
}

//ATUALIZAR HISTORICO MENSAGENS DE CHAMADO
if (isset($_POST['btn_os_obs'])) {
    $id_chamado = $purifier->purify(@mysqli_escape_string($connect, $_POST['id_chamado']));
    $os_obs = $purifier->purify(@mysqli_escape_string($connect, $_POST['os_obs']));
    $data_atual = date("Y-m-d H:i:s");
    if (empty($os_obs) || empty($id_chamado)) {
        $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Preencha todos os campos!</div>";
    } else {
        $sql = "SELECT id FROM chamados WHERE id = $id_chamado";
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) == 1) {
            if(checkLogin(null, null, null, null, "logado", null)){
                $sql = "INSERT INTO chamados_historico (id_chamado, id_usuario, conteudo, data_mensagem) VALUES ($id_chamado, " . $_SESSION['id'] . ", '$os_obs', '$data_atual')";
            }else{
                $sql = "INSERT INTO chamados_historico (id_chamado, conteudo, data_mensagem) VALUES ($id_chamado, '$os_obs', '$data_atual')";
            }
            $sql2 = "SELECT id FROM chamados_historico where data_mensagem = '$data_atual' and id_chamado = $id_chamado";
            $result = mysqli_query($connect, $sql2);
            if(mysqli_num_rows($result) == 0){
                if (!mysqli_query($connect, $sql)) {
                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>ERRO - " . mysqli_errno($connect) . " - Falha ao atualizar chamado!</div>";
                }
            }else{
                header("Refresh:0; url=./");
            }
        } else {
            $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Chamado desconhecido.</div>";
        }
    }
}

//TRANSFERENCIA DE CHAMADOS

if (isset($_POST['btn-transf'])) {
    $id_chamado = $purifier->purify(@mysqli_escape_string($connect, $_POST['id_chamado']));
    $id_atend = $purifier->purify(@mysqli_escape_string($connect, $_POST['id_atend']));
    $id_setor = $purifier->purify(@mysqli_escape_string($connect, $_POST['transf_setor']));
    $transf_obs = $purifier->purify(@mysqli_escape_string($connect, $_POST['transf_obs']));
    $data_atual = date("Y-m-d H:i:s");
    if (checkLogin(null, null, null, null, "logado", null)) {
        if (empty($id_chamado) || empty($id_atend) || empty($id_setor)) {
            $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Preencha pelo menos o setor de destino!</div>";
        } else {
            $sql = "SELECT * FROM vw_chamados WHERE id_chamado = $id_chamado AND id_atendimento = $id_atend";
            $result = mysqli_query($connect, $sql);
            $testeTransf = mysqli_fetch_assoc($result);
            if (mysqli_num_rows($result) == 1) {
                $controle = false;
                foreach ($s_setDestino as $setor) {
                    if ($setor['id'] == $_SESSION['id_setor'] && $setor['nome'] == $testeTransf['setor_destino']) {
                        $controle = true;
                        break;
                    }
                }
                if ($controle && $testeTransf['status'] == "Em atendimento" && empty($testeTransf['setor_transferencia'])) {
                    if (empty($transf_obs)) {
                        $sql = "UPDATE chamados_atendimento SET id_set_transferencia = $id_setor, data_transferencia = '$data_atual', data_finalizado = '$data_atual', status = 'Finalizado' WHERE id = $id_atend";
                    } else {
                        $sql = "UPDATE chamados_atendimento SET id_set_transferencia = $id_setor, data_transferencia = '$data_atual', data_finalizado = '$data_atual', obs_transf = '$transf_obs', status = 'Finalizado' WHERE id = $id_atend";
                    }
                    $sql2 = "INSERT INTO chamados_atendimento (id_chamado, id_set_atual, data, status) VALUES ($id_chamado, $id_setor, '$data_atual', 'Aguardando')";
                    if (mysqli_query($connect, $sql) && mysqli_query($connect, $sql2)) {
                        $sql = "SELECT email, setor_id FROM vw_usuarios where setor_id = $id_setor AND status = 'A'";
                        $usuarios = mysqli_query($connect, $sql);
                        $emails = array();
                        $data_atual = date("Y-m-d");
                        $diaSemana = date('w', strtotime($data_atual));
        
                        if(($diaSemana == 0 || $diaSemana == 6) && $id_setor == 73){//SABADO OU DOMINGO PARA SOBREAVISO TI
                            $emails[] = "email@email.com";
                        }else{
                            foreach ($usuarios as $usuario) {//DIA DE SEMANA
                                if(!empty($usuario['email'])){
                                    $emails[] = $usuario['email'];
                                }
                            }
                        }

                        if(mysqli_query($connect, $sql)){
                            $sql = "SELECT * FROM vw_chamados where id_chamado = $id_chamado AND data_transferencia is null AND status = 'Aguardando'";
                            if($result = mysqli_query($connect, $sql)){
                                $dados = mysqli_fetch_assoc($result);
                                $conteudo = "
                                <span>Segue chamado TRANSFERIDO no Help-Desk:</span><br/><br/>
                                <span style='font-weight: bold;'>Protocolo:</span> ".$dados['protocolo']."<br/><br/>
                                <span style='font-weight: bold;'>Nome:</span> ".$dados['nome_solicitante']."<br/>
                                <span style='font-weight: bold;'>Ramal:</span> ".$dados['ramal']."<br/>
                                <span style='font-weight: bold;'>Estabelecimento:</span> ".$dados['estabelecimento']."<br/>
                                <span style='font-weight: bold;'>Setor solicitante:</span> ".$dados['setor_solicitante']."<br/>
                                <span style='font-weight: bold;'>Equipamento:</span> ".$dados['equipamento']."<br/>
                                <span style='font-weight: bold;'>Ip:</span> ".$dados['ip']."<br/>
                                <span style='font-weight: bold;'>Computador:</span> ".$dados['computador']."<br/><br/>
                                <span style='font-weight: bold; white-space: pre-line;'>Descrição:</span><br/><br/>
                                ".$dados['descricao']."<br/><br/>
                                <span style='font-weight: bold; white-space: pre-line;'>Observação transf:</span><br/><br/>
                                $transf_obs<br/><br/>
                                <span style='font-weight: bold;'>EMAIL DE ENVIO AUTOMÁTICO!</span>";
                                $disparoEmail = new PHPMailer\PHPMailer\PHPMailer();
                                $disparoEmail->IsSMTP();
                                $disparoEmail->Host = '10.41.4.75';
                                $disparoEmail->Port = 587;
                                $disparoEmail->SMTPAutoTLS = false;
                                $disparoEmail->SMTPAuth = true;
                                $disparoEmail->Username = $loginEmail;
                                $disparoEmail->Password = $pwdEmail;
                                $disparoEmail->From = $loginEmail;
                                $disparoEmail->FromName = $fromNameEmail;
                                foreach ($emails as $email) {
                                    $disparoEmail->ClearAllRecipients();
                                    $disparoEmail->AddAddress($email);
                                    $disparoEmail->IsHTML(true);
                                    $disparoEmail->CharSet = 'UTF-8';
                                    $disparoEmail->Subject = "Chamado Transferido - ".$dados['protocolo'];
                                    $disparoEmail->Body = '<span style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">' . $conteudo . '</span>';
                                    $disparoEmail->Send();
                                }
                            }else{
                                $avisos[] = "<script>alert('ERRO - " . mysqli_errno($connect) . " - s_vw_c')</script>";
                            }
                        }else{
                            $avisos[] = "<script>alert('ERRO - " . mysqli_errno($connect) . " - s_user)</script>";
                        }
                        header("Refresh:0; url=./?statusTransf=1");
                    } else {
                        $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>ERRO - " . mysqli_errno($connect) . " - Pode ter acontecido uma falha na criação ou na atualização da transferencia do chamado, contate um administrador! C$id_chamado-A$id_atend</div>";
                    }
                } else {
                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Este chamado já foi finalizado ou não foi atendido ainda!</div>";
                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Você só pode transferir chamados do seu setor!</div>";
                }
            } else {
                $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>POST Inválido!</div>";
            }
        }
    } else {
        header('Location: ../restrito/');
    }
}

//REPOSICAO DE PECAS EM CHAMADOS
if(isset($_POST['btn_repo'])){
    $id_setor = $purifier->purify(@mysqli_escape_string($connect, $_POST['repo_setor']));
    $id_peca = $purifier->purify(@mysqli_escape_string($connect, $_POST['repo_peca']));
    $quantidade = $purifier->purify(@mysqli_escape_string($connect, $_POST['repo_unidade']));
    $setor_dest = $purifier->purify(@mysqli_escape_string($connect, $_POST['setor_dest']));
    $id_chamado = $purifier->purify(@mysqli_escape_string($connect, $_POST['id_chamado']));
    $id_atend = $purifier->purify(@mysqli_escape_string($connect, $_POST['id_atend']));
    $data_atual = date("Y-m-d H:i:s");
    $sql = "SELECT status FROM vw_chamados WHERE id_chamado = $id_chamado and id_atendimento = $id_atend and setor_destino = '$setor_dest'";
    if(checkLogin(null, null, null, null, "logado", null)){
        if(!(empty($id_setor) || empty($id_peca) || empty($quantidade) || empty($setor_dest))){
            if(mysqli_query($connect, $sql)){
                $result = mysqli_query($connect, $sql);
                if(mysqli_num_rows($result) == 1){
                    $testeSql = mysqli_fetch_assoc($result);
                    foreach ($s_setDestino as $setor) {
                        if($setor['nome'] == $setor_dest){
                            if($setor['id'] == $_SESSION['id_setor']){
                                if($testeSql['status'] == "Em atendimento"){
                                    $sql = "INSERT INTO chamados_substituicao (id_chamado, id_peca, id_usuario, quantidade, data) VALUES ($id_chamado, $id_peca, ".$_SESSION['id'].", $quantidade, '$data_atual')";
                                    if(mysqli_query($connect, $sql)){
                                        $avisos[] = "<div class='alert alert-success text-start p-2 m-2' role='alert'><i class='fa-solid fa-check fs-4 me-3'></i>Reposição cadastrada.</div>";
                                    }else{
                                        $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>ERRO - ".mysqli_errno($connect)." - Falha no cadastro da reposição.</div>";
                                    }
                                }else{
                                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Chamado ja finalizado ou não atendido!</div>";
                                }
                            }else{
                                $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Você só pode repor peças de chamados do seu setor!</div>";
                            }
                        }
                    }
                }else{
                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>POST Inválido!</div>";
                }
            }else{
                $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>ERRO - ".mysqli_errno($connect)."</div>";
            }
        }else{
            $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Preencha todos os campos!</div>";
        }
    }else{
        header('Location: ../restrito/');
    }
}





//-------------------------------------------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------INICIO HTML----------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
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
            <!-- LISTAR OS -->

            <div class="col-lg-10 col-md-9">
                <div class="my-4 mx-2 p-2 ps-3 text-light bg-blue rounded shadow">
                    <h1 class="mb-0">Listar O.S</h1>
                </div>

                <!-- PESQUISA DE CHAMADOS -->

                <div class="bg-light my-4 mx-2 p-2 rounded-3 px-3 shadow">
                    <div class="row">
                        <div class="col-lg-8 col-md-12">
                            <h4>Pesquisar chamados por:</h4>
                            <form action="./" method="GET">
                                <div class="my-2 input-group">
                                    <span class="input-group-text">Protocolo:</span>
                                    <input type="text" class="form-control" aria-describedby="protoc_txt" maxlength="16"
                                        placeholder="0000000000000000" name="protocolo"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                    <span class="input-group-text">Setor:</span>
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
                                    <button type="submit" class="btn btn-outline-primary">Pesquisar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <h6>Legenda:</h6>
                    <?php

                    if (checkLogin(null, null, null, null, "logado", null)) {
                        echo "
                        <span class='me-2'><button class='btn btn-warning btn-sm' style='width:33px'><i
                        class='fa-solid fa-arrow-up-from-bracket'></i></button> Transferir</span>
                        <span class='me-2'><button class='btn btn-success btn-sm' style='width:33px'><i
                                    class='fa-solid fa-check'></i></button> Iniciar Atendimento</span>
                        <span class='me-2'><button class='btn btn-danger btn-sm' style='width:33px'><i
                                    class='fa-solid fa-xmark'></i></button> Finalizar Atendimento</span>
                        <span class='me-2 text-danger fs-5'><i class='fa-solid fa-rotate'></i></span><span
                            class='me-2'>Aguardando Atendimento</span>
                        <span class='me-2 text-warning fs-5'><i class='fa-solid fa-magnifying-glass'></i></span><span>Em
                            Atendimento</span>
                        ";
                    } else {
                        echo "
                        <span class='me-2 text-danger fs-5'><i class='fa-solid fa-rotate'></i></span><span
                            class='me-2'>Aguardando Atendimento</span>
                        <span class='me-2 text-warning fs-5'><i class='fa-solid fa-magnifying-glass'></i></span><span>Em
                            Atendimento</span>
                        ";
                    }

                    ?>
                    <p class="text-muted mt-3">Total de Ordens de Serviço -
                        <?php echo $qntSql; ?>
                    </p>

                    <?php
                    foreach ($avisos as $aviso) {
                        echo $aviso;
                    }
                    ?>

                    <!-- TABELA LISTAR CHAMADOS -->

                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Protocolo</th>
                                <th>Estabelecimento</th>
                                <th>Data/Hora</th>
                                <th>Setor</th>
                                <th>Equipamento</th>
                                <th>Usuario Atendimento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($resultOs as $os) {
                                if (empty($os['setor_transferencia']) && $os['status'] != "Finalizado") {
                                    echo "
                                        <tr>
                                            <th><a href='' class='text-decoration-none' data-bs-toggle='modal'
                                                data-bs-target='#modal_os_" . $os['id_atendimento'] . "'>" . $os['protocolo'] . "</a></th>
                                            <td>" . $os['estabelecimento'] . "</td>
                                            <td>" . $os['data_abertura'] . "</td>
                                            <td>" . $os['setor_solicitante'] . "</td>
                                            <td>" . $os['equipamento'] . "</td>
                                            <td>" . $os['usuario_atendimento'] . "</td>
                                            <td class='text-danger fs-5'>" . checkStatus($os['status'], "icone") . "</td>
                                            <td>
                                                " . checkLogin($os['status'], $os['id_atendimento'], $os['id_chamado'], $os['setor_destino'], "acoes", null) . "
                                            </td>
                                        </tr>
                                        ";
                                }
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
                            if (!empty($setor_pesquisa) || !empty($protocolo_pesquisa) || $setor_pesquisa == 0) {
                                $linkPri = "<li class=\"page-item\"><a href=\"?pagina=1&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa\" class=\"page-link\"> Inicio </a></li>";
                                $linkAnt = "<li class=\"page-item\"><a href=\"?pagina=" . $pagina - 1 . "&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa\" class=\"page-link\"> << </a></li>";
                                $linkUlt = "<li class=\"page-item\"><a href=\"?pagina=$totalPaginas&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa\" class=\"page-link\"> Fim </a></li>";
                                $linkProx = "<li class=\"page-item\"><a href=\"?pagina=" . $pagina + 1 . "&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa\" class=\"page-link\"> >> </a></li>";
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
                                            $linkPag = "<li class=\"page-item\"><a href=\"?pagina=$i&setor=$setor_pesquisa&protocolo=$protocolo_pesquisa\" class=\"page-link\"> $i </a></li>";
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
                    // MODAL TRANSFERENCIA DE CHAMADOS
                    foreach ($resultOs as $os) {
                        if ($os['status'] == "Em atendimento" && empty($os['setor_transferencia'])) {
                            echo "
                                <div class='modal fade' id='modal_transf_" . $os['id_atendimento'] . "' tabindex='-1' aria-hidden='true'>
                                    <div class='modal-dialog modal-dialog-centered'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h3>PROTOCOLO: " . $os['protocolo'] . "</h3>
                                            </div>
                                            <div class='modal-body'>
                                                <form action='./' method='POST'>
                                                    <div class='my-3'>
                                                        <label for='transf_setor'>Setor destino:</label>
                                                        <select name='transf_setor' id='transf_setor' class='form-select'>
                                                            <option selected disabled>Selecione..</option>
                                                            " . setorTransf($s_setDestino, $os['setor_destino']) . "
                                                        </select>
                                                        <label for='transf_obs'>Observações:</label>
                                                        <textarea name='transf_obs' id='transf_obs' rows='5' class='form-control'
                                                            style='resize:none'></textarea>
                                                        <input type='hidden' name='id_chamado' value='" . $os['id_chamado'] . "'>
                                                        <input type='hidden' name='id_atend' value='" . $os['id_atendimento'] . "'>
                                                        <input type='hidden' name='btn-transf'>
                                                    </div>
                                                    <div class='mb-3 text-center d-grid'>
                                                        <button type='submit' class='btn btn-warning' onClick='this.disabled=true; this.form.submit();'><i class='fa-solid fa-arrow-up-from-bracket'></i> Transferir</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='button' class='btn btn-danger' data-bs-dismiss='modal'><i
                                                        class='fa-solid fa-xmark'></i> Fechar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ";
                        }
                        //MODAL DESCRICAO DE CHAMADOS
                        if (empty($os['setor_transferencia'])) {
                            echo "
                                <div class='modal fade' id='modal_os_" . $os['id_atendimento'] . "' tabindex='-1' aria-hidden='true'>
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
                                                <b>Status: </b>" . checkStatus($os['status'], "texto") . "
                                                <br>
                                                <b>Usuario Atendimento: </b><span>" . $os['usuario_atendimento'] . "</span>
                                                <br>
                                                <b>Inicio do atendimento: </b><span>" . $os['data_atendimento'] . "</span>
                                                <br>
                                                <b>Anexo: </b>" . checkAnexo($os['anexo']) . "
                                                <br>
                                                <hr>
                                                <h4>Historico:</h4>";
                                                carregaHistorico($os['id_chamado']);

                                                echo checkLogin($os['status'], $os['id_atendimento'], $os['id_chamado'], $os['setor_destino'], "modal", $s_setDestino) . "
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='button' class='btn btn-danger' data-bs-dismiss='modal'><i
                                                        class='fa-solid fa-xmark'></i> Fechar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                                $ids_atend[] = $os['id_atendimento'];
                        }
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="../js/jQuery/jquery.min.js"></script>
    <script>
        function buscaPecas(setor, id_at) {
            acao = 'buscaPecas';
            $.ajax({
                url: '../php/script.php',
                type: 'POST',
                dataType: 'json',
                async: true,
                data: {
                    acao: acao,
                    setor: setor
                },
                beforeSend: function () { },
                success: function (retorno) {
                    $('#retornoPecas_' + id_at).empty();
                    $('#retornoPecas_' + id_at).html(retorno.mensagem);
                }
            });
        }
        $(document).ready(function () {
            var val;
            <?php 
            foreach ($ids_atend as $id) {
                echo "
                $('#setor_destino_$id').change(function () {
                    val2 = $(this).val();
                    if (val2 == 0) {
                        $('#novaReposicao_$id').val(0);
                        $('#novaReposicao_$id').prop('disabled', true);
                    } else {
                        $('#novaReposicao_$id').empty();
                        $('#novaReposicao_$id').prop('disabled', false);
                        $('#repo_unidade_$id').prop('disabled', false);
                        buscaPecas(val2, $id);
                    }
                })    
              ";
            }
            ?>
        })
    </script>

</html>