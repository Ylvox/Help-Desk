<?php 
function carregaHistorico($id){
    require 'sql.php';
    $sql = "SELECT * FROM vw_historico WHERE id_chamado = " . $id;
    if ($result = mysqli_query($connect, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            foreach ($result as $hist) {
                if (empty($hist['peca']) && empty($hist['setor_transferencia'])) { //Mensagens
                    if (empty($hist['usuario_atendimento'])) { //Mensagem sem conta
                        echo "
                            <p>
                                <span class='text-muted'>" . $hist['data'] . "</span>
                                <h6 class='text-secondary'>ATUALIZAÇÕES: </h6>
                                <span class='text-primary'>Usuario</span>
                                <span>" . $hist['conteudo'] . "</span>
                            </p>
                            <hr>
                        ";
                    } else {
                        echo "
                            <p>
                                <span class='text-muted'>" . $hist['data'] . "</span>
                                <h6 class='text-secondary'>ATUALIZAÇÕES: </h6>
                                <span class='text-primary'>" . $hist['usuario_atendimento'] . "</span>
                                <span>" . $hist['conteudo'] . "</span>
                            </p>
                            <hr>
                        ";
                    }
                } else if (!empty($hist['peca'])) { //Reposicao de pecas
                    echo "
                        <p>
                            <span class='text-muted'>" . $hist['data'] . "</span><br>
                            <h6 class='text-info'>REPOSIÇÃO: </h6>
                            <span>" . $hist['peca'] . " - " . $hist['quantidade'] . " " . $hist['unidade'] . "</span>
                        </p>
                        <hr>
                    ";
                } else if (!empty($hist['setor_destino']) && !empty($hist['setor_transferencia'])) { //Transferencia de chamados
                    echo "
                        <p>
                            <span class='text-muted'>" . $hist['data'] . "</span>
                            <h6 class='text-warning'>TRANSFERENCIA: </h6>
                            <span>" . $hist['setor_destino'] . " > " . $hist['setor_transferencia'] . "</span>
                            <br>
                            <span class='text-warning'>Observações: </span><br>
                            <span>" . $hist['conteudo'] . "</span>
                        </p>
                        <hr>
                    ";
                }
            }
        } else {
            echo "<p><span class='text-muted fs-5 text-center'>Sem histórico</span></p>";
        }
    } else {
        echo "<p><span class='text-danger fs-5 text-center'>Erro ao carregar histórico - " . mysqli_errno($connect) . "</span></p>";
    }
}
?>