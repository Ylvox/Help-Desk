<?php
require_once './php/sql.php';
require_once './php/Purifier/HTMLPurifier.auto.php';
require_once './php/phpmailer/class.phpmailer.php';
require_once './php/phpmailer/class.smtp.php';

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

$avisos = array();

//CARREGAMENTO DE MENU
// 0 - NAO LOGADO
// 1 - LOGADO ADM
// 2 - LOGADO SUP
$menu = array();
$menu[0] = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
    <h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
        <a href='./listar-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='./historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
    </div>
    <h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='./restrito/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-shield-halved'></i> Restrito</a>
    </div>
</div>";
$menu[1] = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
    <h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
        <a href='./listar-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='./historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
    </div>
    <h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='./dashboard/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-chart-simple'></i> Dashboard</a>
        <a href='./conta/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-user'></i> Conta</a>
        <a href='./gerenciamento/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-database'></i> Gerenciamento</a>
        <a href='./php/logout.php' class='btn btn-darker text-start my-2'><i class='fa-solid fa-arrow-right-from-bracket'></i> Sair</a>
    </div>
</div>";
$menu[2] = "<div class='col-lg-2 col-md-3 bg-dark border-end shadow'>
    <h6 class='text-muted mt-4 ps-3'>MODULOS</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-regular fa-life-ring'></i> Nova O.S</a>
        <a href='./listar-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-list-check'></i> Listar O.S</a>
        <a href='./historico-os/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-bars-progress'></i> Historico O.S</a>
    </div>
    <h6 class='text-muted mt-4 ps-3'>ADMINISTRAÇÃO</h6>
    <div class='d-grid gap-2 me-5 ms-2'>
        <a href='./dashboard/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-chart-simple'></i> Dashboard</a>
        <a href='./conta/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-user'></i> Conta</a>
        <a href='./php/logout.php' class='btn btn-darker text-start my-2'><i class='fa-solid fa-arrow-right-from-bracket'></i> Sair</a>
    </div>
</div>";
//SELECTS DE DADOS FORMS
//estabelecimentos
$sql = "SELECT * FROM estabelecimentos WHERE status = 'A' ORDER BY nome";
$s_estabelecimentos = mysqli_query($connect, $sql);
//setores solicitantes
$sql = "SELECT * FROM setores WHERE status = 'A' ORDER BY nome";
$s_setores = mysqli_query($connect, $sql);
//Setor destino
$sql = "SELECT * FROM setores WHERE suporte = 'S' AND status = 'A' ORDER BY nome";
$s_setDestino = mysqli_query($connect, $sql);

//Abertura de chamados
function gerarProtocolo()
{ //GERACAO DE PROTOCOLO
    return date("dmYHi") . rand(1000, 9999);
}
function checkProtocolo(string $prot)
{ // CHECA PROTOCOLO SE POR ALGUM MOTIVO MIRACULOSO ELE JA EXISTE
    require './php/sql.php';
    $sql = "SELECT id FROM chamados WHERE protocolo = '$prot'";
    $result = mysqli_query($connect, $sql);
    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

//ABERTURA DE CHAMADO
if (isset($_POST['btn-abrirChamado'])) {
    $nome = $purifier->purify(@mysqli_escape_string($connect, $_POST['novaos_nome']));
    $ramal = $purifier->purify(@mysqli_escape_string($connect, $_POST['novaos_ramal']));
    $estabelecimento = $purifier->purify(@mysqli_escape_string($connect, $_POST['novaos_estabelecimento']));
    $set_solic = $purifier->purify(@mysqli_escape_string($connect, $_POST['novaos_setor_solicitante']));
    $set_dest = $purifier->purify(@mysqli_escape_string($connect, $_POST['novaos_setor_destino']));
    $equipamento = $purifier->purify(@mysqli_escape_string($connect, $_POST['novaos_equipamento']));
    $desc = $purifier->purify(@mysqli_escape_string($connect, $_POST['novaos_desc']));
    $ip = $purifier->purify(@mysqli_escape_string($connect, $_SERVER['REMOTE_ADDR']));
    $computador = $purifier->purify(@mysqli_escape_string($connect, gethostbyaddr($_SERVER['REMOTE_ADDR'])));

    $protocolo = gerarProtocolo();

    while (checkProtocolo($protocolo)) { //ALTERA PROTOCOLO CASO ELE JA EXISTA
        $protocolo = gerarProtocolo();
    }

    if (empty($nome) || empty($ramal) || empty($estabelecimento) || empty($set_solic) || empty($set_dest) || empty($desc) || empty($equipamento)) {
        $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Todos os campos são obrigatórios!</div>";
    } else {
        $target_dir = "uploads/";
        $arcType = strtolower(pathinfo(@$_FILES["uploadPDF"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . $protocolo . '.' . $arcType;
        $linkAnexo = "(Sem anexo)";
        $uploadCheck = false;
        if (!file_exists(@$_FILES["uploadPDF"]["tmp_name"]) || !is_uploaded_file(@$_FILES["uploadPDF"]["tmp_name"])) {
            $sql = "INSERT INTO Chamados (protocolo, nome_solicitante, descricao, data_abertura, id_equipamento, id_set_solicitante, id_set_destino, id_estabelecimento, ramal, computador, ip) VALUES ('$protocolo', '$nome', '$desc', '" . date("Y-m-d H:i:s") . "', $equipamento, $set_solic, $set_dest, $estabelecimento, '$ramal', '$computador', '$ip')";
            $uploadCheck = true;
        } else {
            if (file_exists($target_file)) {
                $uploadCheck = false;
                $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Arquivo ja existente!</div>";
            } else if ($_FILES["uploadPDF"]["size"] > 20971520) {
                $uploadCheck = false;
                $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Arquivo maior que 20MB!</div>";
            } else if (!($arcType == "pdf" || $arcType == "jpeg" || $arcType == "jpg")) {
                $uploadCheck = false;
                $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Arquivo deve estar em formato PDF ou JPEG!</div>";
            } else {
                if (move_uploaded_file($_FILES["uploadPDF"]["tmp_name"], $target_file)) {
                    $uploadCheck = true;
                    //--------------------------------------------------------------------------------------------------------------------------------------------------------
                    //--------------------------------------- IMPORTANTE - ALTERAR O LINK PARA O LINK DO HELPDESK CASO MUDE O ENDERECO ---------------------------------------
                    //--------------------------------------------------------------------------------------------------------------------------------------------------------
                    $linkAnexo = "http://alterar-link/help-desk/$target_file";
                    $sql = "INSERT INTO Chamados (protocolo, nome_solicitante, descricao, data_abertura, id_equipamento, id_set_solicitante, id_set_destino, id_estabelecimento, ramal, computador, ip, anexo) VALUES ('$protocolo', '$nome', '$desc', '" . date("Y-m-d H:i:s") . "', $equipamento, $set_solic, $set_dest, $estabelecimento, '$ramal', '$computador', '$ip', '$linkAnexo')";
                } else {
                    $uploadCheck = false;
                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Ocorreu um erro ao fazer o upload do seu arquivo!</div>";
                }
            }
        }
        if ($uploadCheck) {
            if (mysqli_query($connect, $sql)) {
                $avisos[] = "<div class='alert alert-success text-start p-2 m-2 fs-5 fw-bold' role='alert'>Chamado aberto Protocolo: $protocolo</div>";
                $sql = "SELECT * FROM chamados WHERE protocolo = '$protocolo'";
                $result = mysqli_query($connect, $sql);
                $chamado = mysqli_fetch_array($result);
                $sql = "INSERT INTO Chamados_atendimento (id_chamado, id_set_atual, data, status) values (" . $chamado['id'] . "," . $chamado['id_set_destino'] . ", '".date("Y-m-d H:i:s")."', 'Aguardando')";
                mysqli_query($connect, $sql);

                //$avisos[] = $sql . " - " . mysqli_errno($connect); //DEBUG

                $sql = "SELECT email, setor_id FROM vw_usuarios where setor_id = ".$chamado['id_set_destino']." AND status = 'A'";
                $usuarios = mysqli_query($connect, $sql);
                $emails = array();
                $data_atual = date("Y-m-d");
                $diaSemana = date('w', strtotime($data_atual));

                if(($diaSemana == 0 || $diaSemana == 6) && $chamado['id_set_destino'] == 73){//SABADO OU DOMINGO PARA SOBREAVISO TI
                    $emails[] = "email@email.com";
                }else{
                    foreach ($usuarios as $usuario) {//DIA DE SEMANA
                        if(!empty($usuario['email'])){
                            $emails[] = $usuario['email'];
                        }
                    }
                }

                if(mysqli_query($connect, $sql)){
                    $sql = "SELECT * FROM vw_chamados WHERE protocolo = '$protocolo'";
                    if($result = mysqli_query($connect, $sql)){
                        $dados = mysqli_fetch_assoc($result);
                        $conteudo = "
                        <span>Segue chamado aberto no Help-Desk:</span><br/><br/>
                        <span style='font-weight: bold;'>Protocolo:</span> $protocolo<br/><br/>
                        <span style='font-weight: bold;'>Nome:</span> $nome<br/>
                        <span style='font-weight: bold;'>Ramal:</span> $ramal<br/>
                        <span style='font-weight: bold;'>Estabelecimento:</span> ".$dados['estabelecimento']."<br/>
                        <span style='font-weight: bold;'>Setor solicitante:</span> ".$dados['setor_solicitante']."<br/>
                        <span style='font-weight: bold;'>Equipamento:</span> ".$dados['equipamento']."<br/>
                        <span style='font-weight: bold;'>Ip:</span> $ip<br/>
                        <span style='font-weight: bold;'>Computador:</span> $computador<br/><br/>
                        <span style='font-weight: bold; white-space: pre-line;'>Descricao:</span><br/><br/>
                        $desc<br/><br/>
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
                            $disparoEmail->Subject = "Novo Chamado - $protocolo";
                            $disparoEmail->Body = '<span style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">' . $conteudo . '</span>';
                            $disparoEmail->Send();
                        }
                    }else{
                        $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Erro - " . mysqli_errno($connect) . " - s_vw_c$protocolo</div>";
                    }
                }else{
                    $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Erro - " . mysqli_errno($connect) . " - s_user</div>";
                }
            } else {
                $avisos[] = "<div class='alert alert-danger text-start p-2 m-2' role='alert'><i class='fa-solid fa-triangle-exclamation fs-4 me-3'></i>Erro - " . mysqli_errno($connect) . " - i</div>";
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
    <link rel="stylesheet" type="text/css" href="./css/normalize.css">
    <!-- Bootstrap -->
    <script src=”https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js”
        integrity=”sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo”
        crossorigin=”anonymous”></script>
    <script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./bootstrap/css/bootstrap.min.css">
    <!-- Style CSS -->
    <link rel='stylesheet' href='css/style.css'>
    <!-- FontAwesome -->
    <link href="./fontawesome/css/all.min.css" rel="stylesheet">
    <link href="./fontawesome/css/v4-shims.min.css" rel="stylesheet">
</head>

<body>
    <div class="fixed-top">
        <div class="py-3 bg-darker shadow">
            <a class="text-light ms-3 text-decoration-none" href="./"><?php echo $pageMenuTitle?></a>
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
            <div class="col-lg-10 col-md-9">
                <div class="my-4 mx-2 p-2 ps-3 text-light bg-blue rounded shadow">
                    <h1 class="mb-0">Nova Ordem de Serviço</h1>
                </div>
                <div class='alert alert-danger text-center mx-2 my-4 p-2' role='alert'><i
                        class='fa-solid fa-triangle-exclamation fs-2 me-3'></i><span class="fs-3">Chamados referente ao
                        TASY devem ser abertos no sistema TASY!!!</span></div>
                <?php
                foreach ($avisos as $aviso) {
                    echo $aviso;
                }
                ?>
                <form action="./" method="POST" enctype="multipart/form-data">
                    <div class="row mx-2 bg-light shadow rounded">
                        <div class="col-lg-6 col-md-12">
                            <label for="novaos_nome">Nome solicitante:</label>
                            <input type="text" class="form-control" id="novaos_nome" name="novaos_nome" maxlength="60">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label for="novaos_ramal">Ramal:</label>
                            <input type="text"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                class="form-control" id="novaos_ramal" name="novaos_ramal" maxlength="4">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label for="novaos_estabelecimento">Estabelecimento:</label>
                            <select name="novaos_estabelecimento" id="novaos_estabelecimento" class="form-select">
                                <option selected disabled>Selecione..</option>
                                <?php
                                $opt_estabelecimentos = "";
                                foreach ($s_estabelecimentos as $estab) {
                                    $opt_estabelecimentos .= "<option value='" . $estab['id'] . "'>" . $estab['nome'] . "</option>";
                                }
                                echo $opt_estabelecimentos;
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label for="novaos_setor_solicitante">Setor solicitante:</label>
                            <select name="novaos_setor_solicitante" id="novaos_setor_solicitante" class="form-select">
                                <option selected disabled>Selecione..</option>
                                <?php
                                $opt_setores = "";
                                foreach ($s_setores as $setor) {
                                    $opt_setores .= "<option value='" . $setor['id'] . "'>" . $setor['nome'] . "</option>";
                                }
                                echo $opt_setores;
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label for="novaos_setor_destino">Setor destino:</label>
                            <select name="novaos_setor_destino" id="novaos_setor_destino" class="form-select">
                                <option selected disabled value="0">Selecione..</option>
                                <?php
                                $opt_setoresDest = "";
                                foreach ($s_setDestino as $setor) {
                                    $opt_setoresDest .= "<option value='" . $setor['id'] . "'>" . $setor['nome'] . "</option>";
                                }
                                echo $opt_setoresDest;
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6" id="retornoEquipamentos">
                            <label for="novaos_equipamento">Equipamento:</label>
                            <select name="novaos_equipamento" id="novaos_equipamento" class="form-select" disabled>
                                <option selected disabled>Selecione..</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="novaos_desc">Descrição:</label>

                            <!-- data-limit-rows="true" para habilitar limite de linhas -->
                            
                            <textarea data-limit-rows="false" name="novaos_desc" id="novaos_desc" rows="8" class="form-control"
                                style="resize:none"></textarea>
                        </div>
                        <div class='col-12'>
                            <label for='uploadPDF' class='form-label'>Upload de PDF ou JPEG:</label>
                            <input type='file' class='form-control' name='uploadPDF' id='uploadPDF'
                                accept='application/pdf,image/jpeg'>
                        </div>
                        <div class="text-center d-grid my-4">
                            <input type='hidden' name='btn-abrirChamado'></input>
                            <button type="submit" class="btn btn-success"
                                onClick="this.disabled=true; this.form.submit();"><i
                                    class="fa-regular fa-circle-check"></i>
                                Criar chamado</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="./js/jQuery/jquery.min.js"></script>
    <script>
        function buscaEquipamentos(setor) {
            acao = 'buscaEquipamentos';
            $.ajax({
                url: './php/script.php',
                type: 'POST',
                dataType: 'json',
                async: true,
                data: {
                    acao: acao,
                    setor: setor,
                },
                beforeSend: function () { },
                success: function (retorno) {
                    $('#retornoEquipamentos').empty();
                    $('#retornoEquipamentos').html(retorno.mensagem);
                }
            });
        }
        $(document).ready(function () {
            $("#novaos_setor_destino").change(function () {
                var val = $(this).val();
                if (val == 0) {
                    $('#novaos_equipamento').val(0);
                    $('#novaos_equipamento').prop('disabled', true);
                } else {
                    $('#novaos_equipamento').empty();
                    $('#novaos_equipamento').prop('disabled', false);
                    buscaEquipamentos(val);
                }
            })
        })
        $(document).ready(function () {
            $('textarea[data-limit-rows=true]').on('keypress', function (event) {
                var textarea = $(this),
                    numberOfLines = (textarea.val().match(/\n/g) || []).length + 1,
                    maxRows = parseInt(textarea.attr('rows'));

                if (event.which === 13 && numberOfLines === maxRows) {
                    return false;
                }
            });
        });
    </script>


</body>

</html>