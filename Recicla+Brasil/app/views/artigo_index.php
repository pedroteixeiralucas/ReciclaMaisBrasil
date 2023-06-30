<?php
    # para trabalhar com sessões sempre iniciamos com session_start.
    session_start();
    
    # inclui o arquivo header e a classe de conexão com o banco de dados.
    require_once 'layouts/site/header.php';
    require_once "../database/conexao.php";

    # verifica se existe sessão de usuario e se ele é administrador.
    # se não existir redireciona o usuario para a pagina principal com uma mensagem de erro.
    # sai da pagina.
    if(!isset($_SESSION['usuario']) || ($_SESSION['usuario']['perfil'] == 'USU' )) {
        header("Location: index.php?error=Usuário não tem permissão para acessar esse recurso");
        exit;
    }

    # cria a variavel $dbh que vai receber a conexão com o SGBD e banco de dados.
    $dbh = Conexao::getInstance();

    # Rotina para excluir dados da tabela artigos
    # verifica se os dados do formulario foram enviados via POST 
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        # recupera o id do enviado por post para delete ou update.
        $id = (isset($_POST['id']) ? $_POST['id'] : 0);
        $operacao = (isset($_POST['botao']) ? $_POST['botao'] : null);
        # verifica se o nome do botão acionado por post se é deletar 
        if($operacao === 'deletar'){
            # cria uma query no banco de dados para excluir o usuario com id informado 
            $query = "DELETE FROM `pccsampledb`.`artigos` WHERE id = :id";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':id', $id);
            
            # executa a consulta banco de dados para excluir o registro.
            $stmt->execute();

            # verifica se a quantiade de registros excluido é maior que zero.
            # se sim, redireciona para a pagina de admin com mensagem de sucesso.
            # se não, redireciona para a pagina de admin com mensagem de erro.
            if($stmt->rowCount()) {
                header('location: artigo_index.php?success=Artigo excluído com sucesso!');
            } else {
                header('location: artigo_index.php?error=Erro ao excluir Artigo!');
            }
        }
    } 
    
    $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
    
    # cria uma consulta banco de dados buscando todos os dados da tabela usuarios 
    # ordenando pelo campo perfil e nome.
    $query = "SELECT art.*, cat.nome as categoria FROM `pccsampledb`.`artigos` art
                INNER JOIN `pccsampledb`.`categorias` cat ON cat.id = art.categoria_id ";
    if($filtro) {
        $query .= " WHERE art.titulo LIKE '%" . $filtro . "%' ";    
    }
    $query .= " ORDER BY art.status, data_publicacao desc";
    $stmt = $dbh->prepare($query);
    
    # executa a consulta banco de dados e aguarda o resultado.
    $stmt->execute();
    
    # Faz um fetch para trazer os dados existentes, se existirem, em um array na variavel $row.
    # se não existir retorna null
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    # destroi a conexao com o banco de dados.
    $dbh = null;
?>
<body>
    <?php require_once 'layouts/admin/menu.php'; ?>

    <main>
    <?php
        # verifca se existe uma mensagem de erro enviada via GET.
        # se sim, exibe a mensagem enviada no cabeçalho.
        if(isset($_GET['error']) || isset($_GET['success']) ) { ?>
            <script>
                Swal.fire({
                icon: '<?php echo (isset($_GET['error']) ? 'error' : 'success');?>',
                title: 'Artigo',
                text: '<?php echo (isset($_GET['error']) ? $_GET['error']: $_GET['success']); ?>',
                })
            </script>
        <?php } ?>
        <div class="main_opc">

            <div class="main_stage">
                <div class="main_stage_content">
                    <section class="novo__form__cadastrar">
                        <button class="btn novo__form__btn__cadastrar"
                            onclick="javascript:window.location='artigo_create.php'"
                            >Adicionar novo artigo</button>
                    </section>
                    <section class="novo__form__filtar">
                        <form action="" method="get">
                            <input 
                                type="text" 
                                name="filtro" 
                                placeholder="Informe o nome da empresa a ser buscada." 
                                class="novo__form__input__filtar"
                                value="<?= isset($_GET['filtro'])?$_GET['filtro']:'';?>" 
                                autofocus>
                            <button type="submit" class="btn novo__form__btn__cadastrar">Buscar</button>
                        </form>
                    </section>
                    <article>
                        <header>
                            <table width="1300px" class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Data Publicação</th>
                                    <th>Status</th>
                                    <th>Categoria</th>
                                    <th>Ação</th>
                                </tr>
                                
                                <?php
                                    # verifica se os dados existem na variavel $row.
                                    # se existir faz um loop nos dados usando foreach.
                                    # cria uma variavel $count para contar os registros da tabela.
                                    # se não existir vai para o else e imprime uma mensagem.
                                    if($rows) {
                                        $count = 1;
                                        foreach ($rows as $row) {?>
                                        <tr>
                                            <td><?=$count?></td>
                                            <td><?=$row['titulo']?></td>
                                            <td><?=date_format(date_create($row['data_publicacao']),'d/m/Y H:i')?></td>
                                            <td><?=($row['status'] == '1' ? 'Publicado': 'Em Edição')?></td>
                                            <td><?=$row['categoria']?></td>
                                            <td>
                                                <div style="display: flex;">
                                                    <a href="artigo_edit.php?id=<?=$row['id']?>" class="btn">Editar</a>&nbsp;
                                                    <?php 
                                                        if($_SESSION['usuario']['perfil'] == 'ADM' || $_SESSION['usuario']['perfil'] == 'GER') { ?>
                                                            <form action="" method="post">
                                                                <input type="hidden" name="id" value="<?=$row['id']?>"/>
                                                                <button class="btn" 
                                                                        name="botao" 
                                                                        value="deletar"
                                                                        onclick="return confirm('Deseja excluir o artigo?');"
                                                                        >Apagar</button>
                                                            </form>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>    
                                        <?php $count++;} } else {?>
                                    <tr><td colspan="6"><strong>Não existem artigos cadastrados.</strong></td></tr>
                                <?php }?>
                            </table>

                        </header>
                    </article>

                </div>
            </div>

    </main>
    <!--FIM DOBRA PALCO PRINCIPAL-->

</body>


</html>