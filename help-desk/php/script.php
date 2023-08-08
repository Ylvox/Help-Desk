<?php
require_once 'sql.php';
if ($_POST) {
    $acao = (isset($_POST['acao']) && $_POST['acao'] != '') ? $_POST['acao'] : null;

    switch ($acao) {
        // SCRIPT BUSCA DE EQUIPAMENTOS POR SETOR DESTINO SELECIONADO
        case 'buscaEquipamentos':
            if (isset($_POST['setor']) && !empty($_POST['setor']))
                $setor = @mysqli_escape_string($connect, $_POST['setor']);

            $sql = "SELECT * FROM equipamentos WHERE id_setor = '$setor' AND status = 'A' ORDER BY nome";
            $s_equipamentos = mysqli_query($connect, $sql);

            $conteudo = "
                <label for='novaos_equipamento'>Equipamento:</label>
                <select name='novaos_equipamento' id='novaos_equipamento' class='form-select'>
                <option selected disabled>Selecione..</option>";
            foreach ($s_equipamentos as $eqp) {
                $conteudo .= "<option value='" . $eqp['id'] . "'>" . $eqp['nome'] . "</option>";
            }
            $conteudo .= "</select>";

            echo json_encode(array('mensagem' => $conteudo));
            break;
        case 'buscaPecas':
            if (isset($_POST['setor']) && !empty($_POST['setor']))
                $setor = @mysqli_escape_string($connect, $_POST['setor']);

            $sql = "SELECT * FROM pecas WHERE id_setor = $setor and status = 'A' ORDER BY nome";
            $s_pecas = mysqli_query($connect, $sql);
            $conteudo = "
            <span class='input-group-text' id='peca_txt'>Peça/Material</span>
                    <select name='repo_peca' id='' aria-describedby='peca_txt'
                        class='form-select'>
                        <option selected disabled>Selecione..</option>
            ";
            foreach ($s_pecas as $peca) {
                $conteudo .= "<option value='" . $peca['id'] . "'>" . $peca['nome'] . "</option>";
            }
            $conteudo .= "</select>";

            echo json_encode(array('mensagem' => $conteudo));
            break;
        default:
            break;
    }
} else {
    session_destroy();
    header('Location: ../');
}
?>