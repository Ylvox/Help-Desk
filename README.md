# Help Desk

> Sistema de HelpDesk com transferência de chamados e dashboard

 

### Funcoes principais
 - Transferência de chamados entre setores de suporte
 - Envio de email automático
 - Suporte para utilização de mais de um setor de suporte
 - Criação de contas de usuario para o sistema (apenas para quem dara suporte)
 - Atualização de observações do chamado
 - Reposição de peças de um chamado

## Imagens
### Nova O.S
<img src="/prints/novaos.jpg">

Módulo de nova ordem de serviço, pode ser acessado tanto por quem não está logado como por quem está logado.

O objetivo de ter sido feito desta forma é para caso o setor de suporte Exemplo1 deseje abrir um chamado para outro setor de suporte sem precisar sair da conta.

### Listar O.S Logado (Sem chamado)
<img src="/prints/listaros.jpg">

O filtro de setor serve para visualizar chamados de setores de suporte diferentes, para não misturar tudo na mesma tela.

Quando logado é priorizado a visualização dos chamados do setor de suporte da conta, quando deslogado é priorizado de todos ao mesmo tempo.

### Listar O.S Logado (Com chamado)
<img src="/prints/listaroschamado.jpg">

O chamado pode ser atendido e finalizado pelo botão da propria tabela, mas também pode ser iniciado e finalizado pela modal clicando no protocolo.

### Listar O.S Logado modal1
<img src="/prints/listarosmodal1.jpg">

### Listar O.S Logado modal2
<img src="/prints/listarosmodal2.jpg">

Na reposição de peças ao selecionar o setor, automaticamente já é puxado do banco de dados as possiveis peças de reposições cadastradas para aquele setor.

### Listar O.S Logado Modal Transferencia
<img src="/prints/listarostransfmodal.jpg">

Apenas setores de suporte diferentes do setor que o chamado se encontra irão aparecer para seleção.

Apenas contas referente ao setor de suporte do chamado aberto poderão transferir aquele chamado

### Listar O.S Logoff
<img src="/prints/listaroslogoff.jpg">

### Listar O.S Logoff Modal
<img src="/prints/listarosmodallogoff.jpg">

### Historico O.S Logado
<img src="/prints/historicoos.jpg">

Nesta tela o filtro de "chamado" se refere ao setor de suporte do chamado e o "setor" ao setor do qual o chamado foi aberto.

Por padrão é selecionado todos no filtro "setor" para visualizar o historico geral de chamados.

Para o filtro "chamado" segue a mesma lógica do filtro setor na aba de listar os.

### Dashboard 1
<img src="/prints/dashboard1.jpg">

Os graficoes seguem ordem de Maior para menor / Mais recente para mais antigo. Pode ser alterado a ordem na view do banco de dados referente ao grafico desejado.

Contas do tipo SUPORTE só poderão enxergar o dashboard do setor de suporte referente a própria conta.

Contas do tipo ADM poderão enxergar o dashboard de qualquer setor de suporte, priorizando a visualização do setor de suporte da conta.

Contas do tipo MOD poderão enxergar o dashboard de qualquer setor de suporte, priorizando a visualização do setor de suporte da conta.

Contas do tipo MAN poderão enxergar o dashboard de qualquer setor de suporte adicionado ao array de $setoresMan no arquivo ./help-desk/php/sql.php
```
$setoresMan = array(); //ADD IDS DOS SETORES PARA O TIPO DE CONTA MANUTENCAO PARA QUE POSSAM VER O DASHBOARD
```
É possível bloquear certas contas de visualizar o dashboard no array de $idsProibidos no arquivo ./help-desk/php/sql.php
```
$idsProibidos = array();//ADD IDS DE CONTA QUE NÃO PODEM VER O DASHBOARD CASO TENHA ALGUEM QUE QUEIRA APOSTAR CORRIDA DE CHAMADOS
```

### Dashboard 2
<img src="/prints/dashboard2.jpg">

### Conta (Alteração de senha)
<img src="/prints/conta.jpg">

### Gerenciamento
<img src="/prints/gerenciamento.jpg">

Tela que apenas contas do tipo ADM terão acesso.

Tela utilizada para criação de novas contas e edição das contas ja existentes.

Toda conta criada inicia com a senha "1234", quando resetado a senha também.

- Tipos de contas e o que fazem:
  - Todas as contas apenas atendem chamados do proprio setor.
  - ADM
    - Permissão para criação de contas e edição de contas;
    - Permissão para visualizar qualquer setor de suporte no dashboard.
  - MOD
    - Permissão para visualizar qualquer setor de suporte no dashboard.
  - MAN
    - Permissão para visualizar qualquer setor de suporte no dashboard que tenha sido incluso no array de $setoresMan no arquivo ./help-desk/php/sql.php
```
$setoresMan = array(); //ADD IDS DOS SETORES PARA O TIPO DE CONTA MANUTENCAO PARA QUE POSSAM VER O DASHBOARD
```
  - SUPORTE
    - Apenas visualiza o proprio setor no dashboard e atende aos chamados como o resto

### Gerenciamento Modal
<img src="/prints/gerenciamentomodal.jpg">

## Mudanças importantes no código para funcionamento adequado do sistema

### ./help-desk/php/sql.php
```
ini_set("session.gc_maxlifetime", 43200); //12 horas de sessão

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

//DEBUG DE ERROS
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

$serverip = "ip-servidor-wow";
$username = "usuario_bd";
$password = "senha_bd";
$db_name = "db_helpdesk";
```

### ./help-desk/index.php Linha 131 (Link gerado do anexo na abertura de chamado)
```
//--------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------- IMPORTANTE - ALTERAR O LINK PARA O LINK DO HELPDESK CASO MUDE O ENDERECO ---------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------
$linkAnexo = "http://alterar-link/help-desk/$target_file";
```

### ./help-desk/listar-os/index.php Linha 430
```
                        if(($diaSemana == 0 || $diaSemana == 6) && $id_setor == 73){//SABADO OU DOMINGO PARA SOBREAVISO TI (id_setor se refere ao setor que deseja receber email sab e dom)
                            $emails[] = "email@email.com";
                        }else{
                            foreach ($usuarios as $usuario) {//DIA DE SEMANA
                                if(!empty($usuario['email'])){
                                    $emails[] = $usuario['email'];
                                }
                            }
                        }
```

### ./help-desk/index.php Linha 156
```
                        if(($diaSemana == 0 || $diaSemana == 6) && $id_setor == 73){//SABADO OU DOMINGO PARA SOBREAVISO TI (id_setor se refere ao setor que deseja receber email sab e dom)
                            $emails[] = "email@email.com";
                        }else{
                            foreach ($usuarios as $usuario) {//DIA DE SEMANA
                                if(!empty($usuario['email'])){
                                    $emails[] = $usuario['email'];
                                }
                            }
                        }
```
