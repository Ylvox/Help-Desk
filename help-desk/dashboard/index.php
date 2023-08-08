<?php

require_once '../php/sql.php';

if (!$_SESSION['logged']) {
  header('Location: ../restrito/');
}
if(isset($_GET['setor'])){
  if(!($_SESSION['type'] == "ADM" || $_SESSION['type'] == "MOD") && $_SESSION['id_setor'] != $_GET['setor']){
    if(!($_SESSION['type'] == "MAN" && in_array($_GET['setor'], $setoresMan))){
      header('Location: ../php/logout.php');
    }
  }
}
if(!empty($idsProibidos)){
  if(in_array(@$_SESSION['id'], $idsProibidos)){
    header('Location: ../listar-os');
  }
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
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-chart-simple'></i> Dashboard</a>
        <a href='../conta/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-user'></i> Conta</a>
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
        <a href='./' class='btn btn-darker active text-start my-2'><i class='fa-solid fa-chart-simple'></i> Dashboard</a>
        <a href='../conta/' class='btn btn-darker text-start my-2'><i class='fa-solid fa-user'></i> Conta</a>
        <a href='../php/logout.php' class='btn btn-darker text-start my-2'><i class='fa-solid fa-arrow-right-from-bracket'></i> Sair</a>
    </div>
</div>";

if (@$_SESSION['type'] == "ADM" || @$_SESSION['type'] == "MOD") {
  $sql = "SELECT * FROM setores WHERE suporte = 'S' AND status = 'A' ORDER BY nome";
}else if(@$_SESSION['type'] == "MAN"){
  $sql = manSelect($setoresMan);
}else{
  $sql = "SELECT * FROM setores WHERE suporte = 'S' AND status = 'A' AND id = ".$_SESSION['id_setor']." ORDER BY nome";
}

$setores = mysqli_query($connect, $sql);

function manSelect ($s_ids){
  $ids = "";
  foreach ($s_ids as $id) {
    $ids .= $id . ",";
  }
  $sql = "SELECT * FROM setores WHERE suporte = 'S' AND status = 'A' AND id IN (".rtrim($ids, ",").") ORDER BY nome";
  return $sql;
}

//Pega dados para o dashboard
if(isset($_GET['setor']) && !empty($_GET['setor'])){
  $id = @mysqli_escape_string($connect, $_GET['setor']);
  $sql_7d = "SELECT * FROM vw_atend_7d where id_set_atual = $id LIMIT 7";
  $sql_7m = "SELECT * FROM vw_atend_7m where id_set_atual = $id LIMIT 7";
  $sql_u = "SELECT * FROM vw_atend_usuarios where id_set_atual = $id";
  $sql_s = "SELECT * FROM vw_atend_setor where id_set_atual = $id LIMIT 10";
  $sql_criado = "SELECT * FROM vw_atend_criado where id_set_atual = $id";
  $sql_aguard = "SELECT * FROM vw_atend_aguard where id_set_atual = $id";
  $sql_atual = "SELECT * FROM vw_atend_atual where id_set_atual = $id";
  $sql_fim = "SELECT * FROM vw_atend_fim where id_set_atual = $id";
}else{
  $id = @mysqli_escape_string($connect, $_GET['setor']);
  $sql_7d = "SELECT * FROM vw_atend_7d where id_set_atual = $id_set_priorizado LIMIT 7";
  $sql_7m = "SELECT * FROM vw_atend_7m where id_set_atual = $id_set_priorizado LIMIT 7";
  $sql_u = "SELECT * FROM vw_atend_usuarios where id_set_atual = $id_set_priorizado";
  $sql_s = "SELECT * FROM vw_atend_setor where id_set_atual = $id_set_priorizado LIMIT 10";
  $sql_criado = "SELECT * FROM vw_atend_criado where id_set_atual = $id_set_priorizado";
  $sql_aguard = "SELECT * FROM vw_atend_aguard where id_set_atual = $id_set_priorizado";
  $sql_atual = "SELECT * FROM vw_atend_atual where id_set_atual = $id_set_priorizado";
  $sql_fim = "SELECT * FROM vw_atend_fim where id_set_atual = $id_set_priorizado";
}
$result_7d = mysqli_query($connect, $sql_7d);
$result_7m = mysqli_query($connect, $sql_7m);
$result_u = mysqli_query($connect, $sql_u);
$result_s = mysqli_query($connect, $sql_s);

$result_criado = mysqli_query($connect, $sql_criado);
$result_aguard = mysqli_query($connect, $sql_aguard);
$result_atual = mysqli_query($connect, $sql_atual);
$result_fim = mysqli_query($connect, $sql_fim);

$os_criado = mysqli_fetch_assoc($result_criado);
$os_aguard = mysqli_fetch_assoc($result_aguard);
$os_atual = mysqli_fetch_assoc($result_atual);
$os_fim = mysqli_fetch_assoc($result_fim);

//carregaDados(result_7d, result_7m, result_u, result_s, tipo='data' ou 'label');
function carregaDados($r7d, $r7m, $ru, $rs, $tipo){
  $label_7d = array();
  $label_7m = array();
  $label_u = array();
  $label_s = array();
  $data_7d = array();
  $data_7m = array();
  $data_u = array();
  $data_s = array();
  if($tipo == "label"){
    if(!empty($r7d)){
      foreach ($r7d as $r) {
        $label_7d[] = $r['data'];
      }
      return json_encode($label_7d);
    }else if(!empty($r7m)){
      foreach ($r7m as $r) {
        $label_7m[] = $r['data'];
      }
      return json_encode($label_7m);
    }else if(!empty($ru)){
      foreach ($ru as $r) {
        $label_u[] = $r['quantidade']." - ".$r['nome'];
      }
      return json_encode($label_u);
    }else if(!empty($rs)){
      foreach ($rs as $r) {
        $label_s[] = $r['quantidade']." - ".$r['nome'];
      }
      return json_encode($label_s);
    }
  }else if($tipo == "data"){
    if(!empty($r7d)){
      foreach ($r7d as $r) {
        $data_7d[] = $r['quantidade'];
      }
      return json_encode($data_7d);
    }else if(!empty($r7m)){
      foreach ($r7m as $r) {
        $data_7m[] = $r['quantidade'];
      }
      return json_encode($data_7m);
    }else if(!empty($ru)){
      foreach ($ru as $r) {
        $data_u[] = $r['quantidade'];
      }
      return json_encode($data_u);
    }else if(!empty($rs)){
      foreach ($rs as $r) {
        $data_s[] = $r['quantidade'];
      }
      return json_encode($data_s);
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
      if ($_SESSION['type'] == "ADM") {
        echo $menu[0];
      } else {
        echo $menu[1];
      }
      ?>
      <div class="col-lg-10 col-md-9">
        <div class="my-4 mx-2 p-2 ps-3 text-light bg-blue rounded shadow">
          <h1 class="mb-0">Dashboard</h1>
        </div>
        <div class="col-12">
          <div class="d-flex justify-content-evenly">
            <div class="rounded-3 text-center py-3 px-4 bg-light shadow">
              <h4>Total O.S</h4>
              <hr>
              <div>
                <h2><?php echo (!empty($os_criado['quantidade'])) ? $os_criado['quantidade'] : 0 ?></h2>
                <span>Criadas</span>
              </div>
            </div>
            <div class="rounded-3 text-center py-3 px-4 bg-light shadow">
              <h4>Total O.S</h4>
              <hr>
              <div>
                <h2><?php echo (!empty($os_aguard['quantidade'])) ? $os_aguard['quantidade'] : 0 ?></h2>
                <span>Em aberto</span>
              </div>
            </div>
            <div class="rounded-3 text-center py-3 px-4 bg-light shadow">
              <h4>Total O.S</h4>
              <hr>
              <div>
                <h2><?php echo (!empty($os_atual['quantidade'])) ? $os_atual['quantidade'] : 0 ?></h2>
                <span>Em andamento</span>
              </div>
            </div>
            <div class="rounded-3 text-center py-3 px-4 bg-light shadow">
              <h4>Total O.S</h4>
              <hr>
              <div>
                <h2><?php echo (!empty($os_fim['quantidade'])) ? $os_fim['quantidade'] : 0 ?></h2>
                <span>Finalizadas</span>
              </div>
            </div>
          </div>
        </div>
        <div class="row col-12 my-4">
          <div class="col-lg-4 col-md-12"></div>
          <div class="col-lg-4 col-md-12 bg-light rounded-3 shadow p-3">
            <form action="">
              <div class="input-group">
                <span class="input-group-text" id="protoc_txt">Suporte</span>
                <select name="setor" id="setor" aria-describedby="setor_txt" class="form-select">
                  <?php
                  $controle = false;
                  foreach ($setores as $setor) {
                      if (!empty($_GET['setor']) && $_GET['setor'] == $setor['id'] && !$controle) {
                          echo "<option selected value='" . $setor['id'] . "'>" . $setor['nome'] . "</option>";
                          $controle = true;
                      } else if ($setor['id'] == $id_set_priorizado && !$controle && empty($_GET['setor'])) {
                          echo "<option selected value='" . $setor['id'] . "'>" . $setor['nome'] . "</option>";
                          $controle = true;
                      } else {
                          echo "<option value='" . $setor['id'] . "'>" . $setor['nome'] . "</option>";
                      }
                  }
                  ?>
                </select>
                <button type="submit" class="btn btn-outline-primary">Filtrar</button>
              </div>
            </form>
          </div>
          <div class="col-lg-4 col-md-12"></div>
        </div>
        <div class="row my-4 mx-2 bg-light shadow rounded-3">
          <div class=" col-lg-6 col-md-12">
            <canvas id="total_7dias"></canvas>
          </div>
          <div class=" col-lg-6 col-md-12">
            <canvas id="total_7meses"></canvas>
          </div>
          <div class="col-lg-6 col-md-12">
            <canvas id="atendimentos_user"></canvas>
          </div>
          <div class="col-lg-6 col-md-12">
            <canvas id="chamados_setor"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    const ctx = document.getElementById('total_7dias');
    //carregaDados(result_7d, result_7m, result_u, result_s, tipo='data' ou 'label');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo carregaDados($result_7d, null, null, null, 'label')?>,
        datasets: [{
          label: 'Total',
          data: <?php echo carregaDados($result_7d, null, null, null, 'data')?>,
          borderWidth: 2
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          title: {
            display: true,
            text: 'Total de atendimentos nos ultimos 7 dias'
          }
        }
      }
    });

    const ctx2 = document.getElementById('total_7meses');
    //carregaDados(result_7d, result_7m, result_u, result_s, tipo='data' ou 'label');
    new Chart(ctx2, {
      type: 'line',
      data: {
        labels: <?php echo carregaDados(null, $result_7m, null, null, 'label')?>,
        datasets: [{
          label: 'Total',
          data: <?php echo carregaDados(null, $result_7m, null, null, 'data')?>,
          borderWidth: 2
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          title: {
            display: true,
            text: 'Total de atendimentos nos ultimos 7 meses'
          }
        }
      }
    });

    const ctx4 = document.getElementById('atendimentos_user');
    //carregaDados(result_7d, result_7m, result_u, result_s, tipo='data' ou 'label');
    new Chart(ctx4, {
      type: 'bar',
      data: {
        labels: <?php echo carregaDados(null, null, $result_u, null, 'label')?>,
        datasets: [{
          label: 'Total',
          data: <?php echo carregaDados(null, null, $result_u, null, 'data')?>,
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          title: {
            display: true,
            text: 'Atendimentos por usuario'
          }
        }
      }
    });

    const ctx5 = document.getElementById('chamados_setor');
    //carregaDados(result_7d, result_7m, result_u, result_s, tipo='data' ou 'label');
    new Chart(ctx5, {
      type: 'bar',
      data: {
        labels: <?php echo carregaDados(null, null, null, $result_s, 'label')?>,
        datasets: [{
          label: 'Total',
          data: <?php echo carregaDados(null, null, null, $result_s, 'data')?>,
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          title: {
            display: true,
            text: 'Chamados por setor'
          }
        }
      }
    });
  </script>


</body>

</html>