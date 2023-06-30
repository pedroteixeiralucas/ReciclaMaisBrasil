
<body>
<?php
    # para trabalhar com sessões sempre iniciamos com session_start.
    session_start();

    # inclui os arquivos header, menu e login.
    require_once 'layouts/site/header.php';
    require_once 'layouts/site/menu.php';
    require_once 'login.php';
    require_once "../database/conexao.php";

    # cria a variavel $dbh que vai receber a conexão com o SGBD e banco de dados.
    $dbh = Conexao::getInstance();
    
    # cria variavel que recebe parametro da categoria
    # se foi passado via get quando o campo select do
    # formulario é modificado.    
    $filtroCategoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
    $filtroTitulo = isset($_GET['filtro']) ? $_GET['filtro'] : null;
    
    
    # cria uma consulta banco de dados buscando todos os dados da tabela  
    # ordenando pelo campo data e limita o resultado a 10 registros.
    $query = "SELECT art.*, cat.nome as categoria 
                FROM `pccsampledb`.`artigos` AS art 
                INNER JOIN `pccsampledb`.`categorias` AS cat ON cat.id = art.categoria_id
                WHERE art.status = 1";
    # verifica se existe filtro para categoria.
    # se sim adiciona condição ao select.
    if($filtroCategoria != null && $filtroCategoria != "0") {
        $query .= " AND cat.id = '" .$filtroCategoria . "' ";    
    }
    if($filtroTitulo != null && $filtroTitulo != "0") {
        $query .= " AND art.titulo LIKE '%" .$filtroTitulo . "%' ";    
    }

    $query .= " ORDER BY art.data_publicacao DESC limit 10";

    $stmt = $dbh->prepare($query);
    
    # executa a consulta banco de dados e aguarda o resultado.
    $stmt->execute();
    
    # Faz um fetch para trazer os dados existentes, se existirem, em um array na variavel $row.
    # se não existir retorna null
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    # cria uma consulta banco de dados buscando todos os dados da tabela  
    # ordenando pelo campo nome da categoria.
    $query = "SELECT * 
                FROM `pccsampledb`.`categorias` 
                ORDER BY nome";
    $stmt = $dbh->prepare($query);
    
    # executa a consulta banco de dados e aguarda o resultado.
    $stmt->execute();
    
    # Faz um fetch para trazer os dados existentes, se existirem, em um array na variavel $row.
    # se não existir retorna null
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    # destroi a conexao com o banco de dados.
    $dbh = null;
?>

<!--DOBRA PALCO PRINCIPAL-->

<!--1ª DOBRA-->

<main>


    
    <?php
        # verifca se existe uma mensagem de erro enviada via GET.
        # se sim, exibe a mensagem enviada no cabeçalho.
        if(isset($_GET['error']) || isset($_GET['success']) ) { ?>
            <script>
                Swal.fire({
                icon: '<?php echo (isset($_GET['error']) ? 'error' : 'success');?>',
                title: 'Cadastro!',
                text: '<?php echo (isset($_GET['error']) ? $_GET['error']: $_GET['success']); ?>',
                })
            </script>
    <?php } ?>
    <div class="main_cta">
        <article class="main_cta_content">
            <div class="main_cta_content_spacer">
                <header>
                    <h1>Você empresa, deseja comprar materiais reciclavéis?<br> Faça seu cadastro ja!
                    </h1>
                </header>
                <p>Venha nós conhecer!</p>
                
                <p ><a href="usuario_admin_new.php" class="btn" >Cadastre-se</a></p>
            </div>
        </article>
    </div>
    <!--FIM 1ª DOBRA-->



    <!--INICIO SESSÃO SESSÃO DE ARTIGOS-->
    <section class="main_blog">
        <header class="main_blog_header">
            <h1 class="icon-blog">Produtos</h1>
            <p>Interessado em vender seu produto?</p>
            <!-- cria o campo select com os dados da consulta 
                realizada na tabela de categorias -->
            <select name="categorias" onchange="filtroPorCategoria(this.value);">
                <option value="0">Filtrar por categoria</option>
                <?php 
                    foreach($categorias as $categoria) {
                        echo "<option value='" 
                        . $categoria['id'] ."'"
                        .($filtroCategoria == $categoria['id'] ? ' selected': '') . ">" 
                        . $categoria['nome'] 
                        . "</option>";
                    }
                ?>
            </select>

            <section class="novo__form__filtar">
                <form action="" method="get">
                    <input 
                        type="text" 
                        name="filtro" 
                        placeholder="Informe uma empresa a qual você deseja vender seu produto" 
                        class="novo__form__input__filtar"
                        value="<?= isset($_GET['filtro'])?$_GET['filtro']:'';?>" 
                        autofocus>
                    <button type="submit" class="btn novo__form__btn__cadastrar">Buscar</button>
                </form>
            </section>
        
        </header>
        
        <?php if($rows) { foreach ($rows as $row){ ?>
            <article>
                <a href="artigo_show.php?id=<?=$row['id'];?>">
                     <?php
                        $imagem = $row['imagem'] == '' ? "semimagem.jpg" : $row['imagem'];
                        if($row['imagem_externa'] == '0') {
                            $imagem = "assets/img/artigos/" . $imagem;
                        }                          
                     ?>   
                    <img src="<?=$imagem?>" width="250px" height="250px" alt="<?=$row['titulo']?>" title="<?=$row['titulo']?>">
                </a>
                <p><a href="#" class="category"><?=$row['titulo']?></a> || <a href="#" class="category"><?=$row['categoria']?></a></p>
                <h2 class="title" title="<?=$row['texto']?>">
                    <?php 
                        $max = 350;
                        echo substr($row['texto'], 1, (strlen($row['texto']) <= $max) ? strlen($row['texto']): $max) . '(...)'; 
                    ?>
                </h2>
            </article>
        <?php } } else { echo "<p>Esta empresa não esta no nosso sistema!</p>"; } ?>
    </section>



    <!--FIM SESSÃO SESSÃO DE ARTIGOS-->

    

    <?php 
        // require_once 'layouts/site/secao_optin.php';
        // require_once 'layouts/site/secao_cursos.php';
        // require_once 'layouts/site/secao_review.php';
        // require_once 'layouts/site/secao_dobra_escola.php';
        // require_once 'layouts/site/secao_tutor.php';
        // require_once 'layouts/site/secao_conteudo_exclusivo.php';
    ?>
   
    
    
</main>
<script>
    function filtroPorCategoria(valorId) {
        window.location.href="index.php?categoria=" + valorId;
    }
</script>

<!-- inclui o arquivo de rodape do site -->
<?php require_once 'layouts/site/footer.php'; ?>
