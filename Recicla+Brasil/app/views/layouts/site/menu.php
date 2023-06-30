<header class="main_header">
    <div class="main_header_content">
        <a href="index.php" class="logo">
            <img src="assets/img/logo.png" alt="Bem vindo ao projeto prático Html5 e Css3 Essentials" title="Bem vindo ao projeto prático Html5 e Css3 Essentials"></a>

        <nav class="main_header_content_menu">
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php 
                    # verifica se existe sessão de usuario e se ele é administrador.
                    # se não for o primeiro caso, verifica se a sessao existe.
                    # por ultimo adiciona somente o link para o login se a sessão não existir. 
                    if (isset($_SESSION['usuario']) && $_SESSION['usuario']['perfil'] == 'ADM')  {
                        echo "<li><a href='usuario_admin.php'>Admin</a></li>";
                    } 
                    if(isset($_SESSION['usuario']) && ($_SESSION['usuario']['perfil'] != 'EDI')) {
                        echo "<li><a href='artigo_index.php'>Artigos</a></li>";
                    } 
                    if(!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] != 'ADM'){
                        echo "<li><a href='categoria_index.php'>Categorias</a></li>";
                    } 
                    if(isset($_SESSION['usuario']) && ($_SESSION['usuario']['perfil'] == 'EDI')) {
                        echo "<li><a href='usuario_admin_list.php'>Listar Dados</a></li>";
                    } 
                    if(isset($_SESSION['usuario'])) {
                        echo "<li><a href='logout.php'>Sair</a></li>";
                    } else {
                        echo "<li><a href='login.php' class='modal-link'>Login</a>";                
                    }
                ?>
            </ul>
        </nav>
    </div>
</header>