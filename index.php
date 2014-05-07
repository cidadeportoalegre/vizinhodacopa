<?php
    namespace CopaControleVeiculos;
    
    require_once('Connection.php');
    require_once('Logradouro.php');
    require_once('Util.php');
    require_once('Limites.php');
    require_once('Cadastro.php');
    require_once('Veiculo.php');
    require_once('Proprietario.php');
    require_once('securimage/securimage.php');
    
    \CopaControleVeiculos\Util::iniciaSessao();   

    $cadastro = new \CopaControleVeiculos\Cadastro();
    $proprietario = new \CopaControleVeiculos\Proprietario();            
    
    $validado = TRUE;

    if (isset($_POST['destruirSessao'])) {
        \CopaControleVeiculos\Util::finalizaSessao();
    }
    else {
        if (isset($_SESSION['confirmado']) && $_SESSION['confirmado'] == TRUE) {
            $proprietario = unserialize($_SESSION['proprietario']);
        }

        if (isset($_POST['finalizar'])) {
            if ($proprietario->isProprietarioConfirmado()) {
                $cadastro->finalizaCadastro($proprietario);
                
                $proprietario = new \CopaControleVeiculos\Proprietario();
                \CopaControleVeiculos\Util::finalizaSessao();
            }
        }

        if (isset($_POST['avancar'])) {

            if (!isset($_SESSION['iniciado']) || (isset($_SESSION['iniciado']) && $_SESSION['iniciado'] != TRUE)) {
                $securimage = new \Securimage();
                if ($securimage->check($_POST['captcha_code']) == FALSE) {
                    $cadastro->adicionaErroGenerico('Texto da imagem incorreto');
                    $validado = FALSE;
                }
            }

            if ($validado) {                
                $proprietarioExistente = NULL;
                
                if ($cadastro->validaProprietarioExistente($_POST)) {
                    $_SESSION['iniciado'] = TRUE;
                    $proprietarioExistente = $cadastro->getProprietarioExistente($_POST);
                    
                    if ($proprietarioExistente != NULL) {
                        $proprietario = $proprietarioExistente;
                        $proprietario->setProprietarioConfirmado(TRUE);
                        $_SESSION['proprietario'] = serialize($proprietario);
                        $_SESSION['confirmado'] = TRUE;
                    }
                }
            }
        }

        if (isset($_POST['salvar'])) {
            if ($proprietario->isProprietarioConfirmado()) {
                if ($cadastro->validaAdicionaVeiculo($_POST)) {
                    $proprietario = $cadastro->adicionaVeiculo($_POST, $proprietario);
                    
                    if (isset($_POST['veiculoPlaca']) && $cadastro->veiculoAdicionado($proprietario, $_POST['veiculoPlaca'])) {
                        $proprietario->setProprietarioConfirmado(TRUE); 
                        $_SESSION['proprietario'] = serialize($proprietario);
                        $_SESSION['confirmado'] = TRUE;

                        $cadastro->limpaPostVeiculo($_POST);
                    }
                }
           }
           else {
                if ($cadastro->validaCadastro($_POST)) {
                    $proprietario = $cadastro->defineProprietario($_POST);
                    
                    $proprietario->setProprietarioConfirmado(TRUE);
                    $_SESSION['proprietario'] = serialize($proprietario);
                    $_SESSION['confirmado'] = TRUE;

                    $cadastro->limpaPostVeiculo($_POST);
                }
            }
        }
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
 
<head>

<meta name="description" content="Vizinho da Copa - Cadastro de Veículos"> 
<meta name="author" content="Prefeitura Municipal de Porto Alegre"> 
<meta name="MSSmartTagsPreventParsing" content="true"> 
<meta http-equiv="Pragma" content="no-cache"> 
<meta name="robots" content="all"> 
<meta name="language" content="pt-br"> 
<meta name="DC.title" content="EdificaPOA - EGLRF"> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="tags" xml:lang="pt-BR" lang="pt-BR"> 
<meta name="expires" content="never"> 
<meta content="IE=7" http-equiv="X-UA-Compatible"> 

<title>Cadastro de Ve&iacute;culos</title>


<link href="http://www2.portoalegre.rs.gov.br/proweb3_geral/css/estilo_geral.css" type="text/css" rel="stylesheet" title="default" />
<link href="http://www2.portoalegre.rs.gov.br/edificapoa/css/estilo_local.css" type="text/css" rel="stylesheet" />
<link href="css/flick/jquery-ui-1.10.4.custom.css" type="text/css" rel="stylesheet">
<link href="css/cadastro.css" type="text/css" rel="stylesheet">


<!--   estilos acessiveis  -->
<link rel="stylesheet" type="text/css" href="http://www2.portoalegre.rs.gov.br/proweb3_geral/css/estilos_maior.css" title="default_maior" />
<link rel="stylesheet" type="text/css" href="http://www2.portoalegre.rs.gov.br/proweb3_geral/css/estilos_acessivel.css" title="acessivel" />
<link rel="stylesheet" type="text/css" href="http://www2.portoalegre.rs.gov.br/proweb3_geral/css/estilos_acessivel_m.css" title="acessivel_maior" />


<script type="text/javascript" src="http://www2.portoalegre.rs.gov.br/proweb3_geral/includes/jquery-1.2.3.min.js"></script>
<script type="text/javascript" src="http://www2.portoalegre.rs.gov.br/proweb3_geral/includes/jquery.idTabs.min.js"></script>
<script type="text/javascript" src="http://www2.portoalegre.rs.gov.br/proweb3_geral/includes/easySlider1.7.js"></script> 
<script type="text/javascript" src="http://www2.portoalegre.rs.gov.br/proweb3_geral/includes/potoquer.js"></script>
<script type="text/javascript" src="http://www2.portoalegre.rs.gov.br/proweb3_geral/includes/funcJS.js"></script> 
<script type="text/javascript" src="http://www2.portoalegre.rs.gov.br/proweb3_geral/includes/lasier.js"></script> 
<script src="scripts/jquery-1.10.2.js"></script>
<script src="scripts/jquery-ui-1.10.4.custom.js"></script>        
<script src="scripts/cep.js"></script>






</head>

<body class="interna">

<!-- abre acessibilidade ouvinte --> 
    <ul id="acesso_ouvinte"> 
      <li><a href="http://www2.portoalegre.rs.gov.br/edificapoa/#menu" accesskey="m">Menu</a></li> 
      <li><a href="http://www2.portoalegre.rs.gov.br/edificapoa/#conteudo" accesskey="c">Conteúdo</a></li> 
      <li><a href="http://www2.portoalegre.rs.gov.br/edificapoa/#procura" accesskey="b">Busca</a></li> 
    </ul> 
<!-- fecha acessibilidade ouvinte --> 

<!--  abre container  -->
<div id="container">


	<!--  abre topo  -->
	<div id="topo">

		<!--   abre logo PMPA  -->
<a href="http://www.portoalegre.rs.gov.br/">
	<img src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/icones/brasao_pmpa_cor.png" border="0" alt="Prefeitura de Porto Alegre" id="logo_pmpa_cor">
	<!-- <img src="images/icones/logo_pmpa_pb.gif" border="0" alt="Prefeitura de Porto Alegre" id="logo_pmpa_pb" /> -->
</a>
<!--   fecha logo PMPA  -->
	
<!--   abre espaÁo banner institucional -->
<div id="bnr_inst">
<a href="http://www2.portoalegre.rs.gov.br/vizinhodacopa/" target="_blank"><img src="http://lproweb.procempa.com.br/pmpa/prefpoa/portal_pmpa_novo/usu_img/vizinhoCOPA.gif" alt="" /></a>
</div>
<!--   fecha espaÁo banner institucional  -->

<!-- include menu -->

	<!--   abre menu secretarias, departamentos, Ûrg„os, serviÁos  -->	
<ul id="menu_topo">

	<!-- Secretarias -->
	<li id="btn-menu1">
        <a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" id="btn1">Secretarias</a>
    </li>
	
	<!-- Departamentos -->
	<li id="btn-menu2">
        <a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" id="btn2">Departamentos</a> 
    </li>

	<!-- Empresas -->
   	<li id="btn-menu3">
    	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" id="btn3">Empresas</a> 
    </li>

	<!-- ServiÁos -->
    <li id="btn-menu4">
        <a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" id="btn4">Servi&ccedil;os</a> 
    </li> 
    
</ul> 

<!--  fecha menu secretarias, departamentos, Ûrg„os, serviÁos   -->	

<!-- painel menu -->
<div class="panela">
	<ul>
		<!-- O GOVERNO - FIXO! -->
		<li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_novo/default.php?p_secao=58">O Governo</a></li>
		<li><a href="http://www2.portoalegre.rs.gov.br/smacis">Acessibilidade</a></li><li><a href="http://www2.portoalegre.rs.gov.br/sma/default.php">AdministraÁ„o</a></li><li><a href="http://www.portoalegre.rs.gov.br/cs">ComunicaÁ„o Social</a></li><li><a href="http://www2.portoalegre.rs.gov.br/cmm/">Coordenadoria da Mulher</a></li><li><a href="http://www2.portoalegre.rs.gov.br/smc/default.php">Cultura</a></li><li><a href="http://www2.portoalegre.rs.gov.br/codec/">Defesa Civil</a></li><li><a href="http://www2.portoalegre.rs.gov.br/seda">Direitos Animais</a></li><li><a href="http://www2.portoalegre.rs.gov.br/smdh/">Direitos Humanos</a></li><li><a href="http://www.portoalegre.rs.gov.br/smed">EducaÁ„o</a></li><li><a href="http://www.portoalegre.rs.gov.br/sme/">Esporte</a></li><li><a href="http://www.portoalegre.rs.gov.br/smf">Fazenda</a></li><li><a href="http://www2.portoalegre.rs.gov.br/smgae/">Gest„o</a></li><li><a href="http://www2.portoalegre.rs.gov.br/smgl/">GovernanÁa</a></li><li><a href="http://www.portoalegre.rs.gov.br/smic">Ind˙stria e ComÈrcio</a></li><li><a href="http://www.inovapoa.com/">Inovapoa</a></li><li><a href="http://www2.portoalegre.rs.gov.br/smj/default.php">Juventude</a></li><li><a href="http://www.portoalegre.rs.gov.br/smam">Meio Ambiente</a></li><li><a href="http://www.portoalegre.rs.gov.br/smov">Obras e ViaÁ„o</a></li><li><a href="http://www2.portoalegre.rs.gov.br/gpn/">Povo Negro</a></li><li><a href="http://www.portoalegre.rs.gov.br/pgm/">Procuradoria</a></li><li><a href="http://www2.portoalegre.rs.gov.br/smpeo/">Planejamento EstratÈgico e OrÁamento</a></li><li><a href="http://www.portoalegre.rs.gov.br/sms">Sa˙de</a></li><li><a href="http://www.secopapoa.com.br/">Secopa</a></li><li><a href="http://www2.portoalegre.rs.gov.br/smseg/default.php">SeguranÁa</a></li><li><a href="http://www.portoalegre.rs.gov.br/smte/">Trabalho</a></li><li><a href="http://www.portoalegre.rs.gov.br/turismo">Turismo</a></li><li><a href="http://www2.portoalegre.rs.gov.br/spm">Urbanismo</a></li>	</ul>
</div>

<div class="panelb">
	<ul>
		<!-- O GOVERNO - FIXO! -->
		<li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_novo/default.php?p_secao=58">O Governo</a></li>
		<li><a href="http://www.portoalegre.rs.gov.br/demhab/">DEMHAB</a></li><li><a href="http://www.portoalegre.rs.gov.br/dep">DEP</a></li><li><a href="http://www.portoalegre.rs.gov.br/dmae">DMAE</a></li><li><a href="http://www.portoalegre.rs.gov.br/dmlu">DMLU</a></li><li><a href="http://www.portoalegre.rs.gov.br/fasc">FASC</a></li><li><a href="http://www.portoalegre.rs.gov.br/previmpa/">PREVIMPA</a></li>	</ul>
</div>

<div class="panelc">
	<ul>
		<!-- O GOVERNO - FIXO! -->
		<li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_novo/default.php?p_secao=58">O Governo</a></li>
		<li><a href="http://www.carris.com.br/">CARRIS</a></li><li><a href="http://www.eptc.com.br/">EPTC</a></li><li><a href="http://www2.portoalegre.rs.gov.br/imesf/default.php">IMESF</a></li><li><a href="http://www.procempa.com.br/">PROCEMPA</a></li>	</ul>
</div>
	
<div class="paneld">
	<ul>
		<li><a href="http://www.falaportoalegre.com.br/solicitacao">SolicitaÁ„o pelo FALA PORTO ALEGRE</a></li><li><a href="http://www.falaportoalegre.com.br/consulta">Consulta pelo FALA PORTO ALEGRE</a></li><li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_servicos/default.php?p_secao=55">Consultas</a></li><li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_servicos/default.php?p_secao=44">Ouvidorias</a></li><li><a href="http://www2.portoalegre.rs.gov.br/procon/">Procon</a></li><li><a href="http://www2.portoalegre.rs.gov.br/sma/default.php?p_secao=74">Protocolo Administrativo</a></li><li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_servicos/default.php?p_secao=47">Den˙ncias e ReclamaÁıes</a></li><li><a href="http://www.portoalegre.rs.gov.br/concursos/">Concursos</a></li><li><a href="http://www2.portoalegre.rs.gov.br/estagios/">Est·gios</a></li><li><a href="http://www2.portoalegre.rs.gov.br/dopa/">Di·rio Oficial</a></li><li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_servicos/default.php?p_secao=42">Fornecedores / LicitaÁıes</a></li><li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_servicos/default.php?p_secao=51">Licenciamentos, Alvar·s e Habite-se</a></li><li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_servicos/default.php?p_secao=52">Conselhos, Comissıes e ComitÍs</a></li><li><a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_servicos/default.php?p_secao=53">Fundos</a></li><li><a href="./equeleto_cadastro_veiculos_files/equeleto_cadastro_veiculos.html">EdificaPOA</a></li>	</ul>
</div>
<!-- fecha panel -->

 

<!-- fim include menu -->		

<!--   abre caixa perfil  -->
<div id="caixa_perfil">
      
	<!--   abre tÌtulo  -->
	<h1>
		<a href="http://www2.portoalegre.rs.gov.br/vizinhodacopa">VIZINHO DA COPA - Cadastro de Ve&iacute;culos</a> 	</h1>
	<!--   fecha tÌtulo  -->
</div>
<!--   fecha caixa perfil  -->		
	

	<!-- abre destaques  -->
	
		<!--   abre caixa destaques  -->
		  <div id=""></div>
		<!-- fecha destaques -->
		
			<div id="xoxo"></div>
		<!--   fecha eles  -->
			    
	<!--   abre script destaque  -->
	<script type="text/javascript"> 
	//função do JQUERY pra montar o esquema dos destaques
	$("#destaques").idTabs(function(id,list,set){ 
	$("a",set).removeClass("selected") 
	.filter("[@href='"+id+"']",set).addClass("selected"); 
	for(i in list) 
	$(list[i]).hide(); 
	$(id).fadeIn(); 
	return false; 
	}); 
	
	
	</script>
	

	<!--   fecha script destaque  -->
		
	<!-- fecha destaques  -->
	
		</div>
	<!--  fecha topo  -->
    
    	<!--  abre coluna da direita  -->
	<div id="coluna_4">

	<!-- abre busca -->
	

<form action="http://www2.portoalegre.rs.gov.br/edificapoa/default.php" method="GET">
	<div id="busca_direita"><img src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/icones/lupinha.gif" alt="Lupa" title="Lupa" class="lupa">
		<input name="p_busca" type="textbox" value="Procure no EdificaPOA" onclick="if(this.value==&#39;Procure no EdificaPOA&#39;)this.value=&#39;&#39;;" onblur="if(this.value==&#39;&#39;)this.value=&#39;Procure no EdificaPOA&#39;;" onfocus="if(this.value==&#39;&#39;)this.value=&#39;Procure no EdificaPOA&#39;;" class="txt_buscad">
		<input type="image" src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/botoes/btn_buscad.gif" value="BUSCAR" class="btn_buscad">
	</div>
</form>
<!-- fecha busca -->

<!--   abre div lista perfil-->
<div id="barra_qv">
	<ul class="cssMenu_qv"> 
		<li><a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" id="cssMenu_qv_header"><strong>Escolha um perfil</strong><br>¡rea de interesse</a> 
			<ul> 
							<li>
					<a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_cidadao">
					CIDAD√O					</a>
				</li>
							<li>
					<a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_cidade">
					CIDADE					</a>
				</li>
							<li>
					<a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_empreendedor">
					EMPREENDEDOR					</a>
				</li>
							<li>
					<a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_estudante">
					ESTUDANTE					</a>
				</li>
							<li>
					<a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_servidor">
					SERVIDOR					</a>
				</li>
							<li>
					<a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_turista">
					TURISTA					</a>
				</li>
				
			</ul>
		</li> 		
	</ul> 
</div> 
<!--   fecha div lista perfil-->

<!-- abre acessibilidade --> 
<div id="acessibilidade">
	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" onclick="setActiveStyleSheet(&#39;default&#39;); return false;" title="Diminuir letras" class="a1">A<sup>-</sup></a>
	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" onclick="setActiveStyleSheet(&#39;default_maior&#39;); return false;" title="Aumentar letras" class="a2">A<sup>+</sup></a>
	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" onclick="setActiveStyleSheet(&#39;acessivel&#39;); return false;" title="Diminuir letras" class="a3">A<sup>-</sup></a>
	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" onclick="setActiveStyleSheet(&#39;acessivel_maior&#39;); return false;" title="Aumentar letras" class="a4">A<sup>+</sup></a>
	&nbsp;&nbsp;
	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" onclick="setActiveStyleSheet(&#39;default&#39;); return false;" class="preto_acessivel" title="Alto contraste">A</a>
	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" onclick="setActiveStyleSheet(&#39;acessivel_maior&#39;); return false;" class="preto_acessivel_m" title="Alto contraste">A</a>
	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" onclick="setActiveStyleSheet(&#39;acessivel&#39;); return false;" class="preto" title="Alto contraste">A</a>
	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#" onclick="setActiveStyleSheet(&#39;default_maior&#39;); return false;" class="preto_m" title="Alto contraste">A</a>
	&nbsp;&nbsp;
	<a href="http://www2.portoalegre.rs.gov.br/edificapoa/acessibilidade.php" class="ajuda" title="Acessibilidade no site.">?</a> 
</div>
<!-- fecha acessibilidade -->

<!--   abre caixa m·gica - METROCLIMA -->  	 


<div id="app_metroclima">

		<img src="http://www2.portoalegre.rs.gov.br/ceic/images/ico_solechuva.png" class="ico_metroclima">
<a target="_blank" href="http://www2.portoalegre.rs.gov.br/ceic/default.php?p_secao=11">
<ul>
<li class="max_min"><span class="temp_max">28∞C&#8743;</span><br><span class="temp_min">16∞C&#8744;</span></li>
<li class="data_metroclima">
<img src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/icones/logo_metroclima.gif"><br>10 de abril </li>		
</ul>
</a>
</div>
 				
<!--   fecha caixa m·gica - METROCLIMA  -->	

<!-- ⁄TEIS -->
<div id="barra_v"> 
	<ul class="cssMenu_v"> 
		<li><a href="http://bancodeimagens.procempa.com.br/" target="_blank"><span>Banco de Imagens</span></a></li> 
		<li><a href="http://www2.portoalegre.rs.gov.br/radioweb/" target="_blank"><span>Rádio WEB</span></a></li> 
		<li><a href="http://www2.portoalegre.rs.gov.br/cs/default.php?p_secao=22" target="_blank"><span>TV Prefeitura</span></a></li> 
		<li><a href="http://www2.portoalegre.rs.gov.br/dopa/" target="_blank"><span>Diário Oficial</span></a></li> 		
		<li><a href="http://www2.portoalegre.rs.gov.br/webcam/" target="_blank"><span>Webcams</span></a></li> 		
	</ul> 
</div> 
<!-- FECHA ⁄TEIS -->

<!--   abre banners  -->		

	<!-- banner ESTATICO DO 156 - banner 1 -->

		<a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_servicos/">
		<img border="0" src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/banners/bnr_falaportoalegre156.jpg" id="banner1">
		</a>

	
<a href="http://www.portoalegre.rs.gov.br/transparencia">
<img border="0" src="http://lproweb.procempa.com.br/pmpa/prefpoa/portal_pmpa_novo/usu_img/bnr_portal_transparencia2013.jpg" id="banner1">
</a>


<!--<a href="http://www.portoalegre.rs.gov.br/portalgestao">
	<img border=0 src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/banners/banner_portal_gestao.png" id="banner1">
</a>-->		


<!--banner saude novo-->
<!--<a href="http://www2.portoalegre.rs.gov.br/sms/default.php?p_secao=891">
	<img border=0 src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/banners/banner-saude.jpg" id="banner1">
</a>-->		
<!--fim banner saude novo-->


<!-- banner 3 MUTANTE --> 
		<!--   abre banner  -->
<div id="gallery" class="banner3">	
</div>		
		<!--   fecha banner  --> 

<!--   fecha banners  -->

	
<!--   abre A/Z  -->
<div id="navegue_az">
	<a href="http://www2.portoalegre.rs.gov.br/portal_pmpa_sitespmpa">&gt; Sites de A a Z</a>
</div>
<!--   fecha A/Z  -->


<!--   abre ver todas notÌcias   -->
<div id="todas_noticias">
	<a href="http://www2.portoalegre.rs.gov.br/cs/default.php?p_secao=3">
			[+]&nbsp;&nbsp;Notícias	</a>
</div>
<!--   fecha todas as notÌcias  -->
	
<!-- Abre ¬ncora vai para o topo -->	
<a href="http://www2.portoalegre.rs.gov.br/edificapoa/#topo" id="ancora_topo">voltar ao topo ^</a>
<!-- Abre ¬ncora vai para o topo -->

	</div>
	<!--  fecha coluna direita  -->
    
    
	<!--  abre conteudo  -->
	<div id="conteudo" class="outros">
            

        <form id="formCadastro" method="post" action="index.php">
            <?= isset($cadastro) ? $cadastro->errosGenericosComoDiv($_SESSION) : '';  ?> 
            <?= isset($cadastro) ? $cadastro->alertasGenericosComoDiv($_SESSION) : '';  ?> 
            <?= isset($cadastro) ? $cadastro->mensagensGenericasComoDiv($_SESSION) : '';  ?> 
    <div id="<?= ((!isset($_SESSION)) || (isset($_SESSION) && !isset($_SESSION['iniciado']))) ? 'inicial' : 'veiculos' ?>">
            <div id="divInformacoesVeiculo">
                <div id="divProprietarioTipoPessoaCpfCnpj">
                    <div id="radios">
                        <input type="radio" id="proprietarioTipoPessoaCpf" name="proprietarioTipoPessoa" 
                            value="cpf" <?= isset($cadastro) ? $cadastro->getTipoPessoaChecked('cpf', $proprietario, $_POST) : ''; ?> 
                            <?=  isset($cadastro) ? $cadastro->isInputDisabled($proprietario) : ''; ?> >CPF
                        <input type="radio" id="proprietarioTipoPessoaCnpj" name="proprietarioTipoPessoa" 
                            value="cnpj" <?= isset($cadastro) ? $cadastro->getTipoPessoaChecked('cnpj', $proprietario, $_POST) : ''; ?>
                            <?=  isset($cadastro) ? $cadastro->isInputDisabled($proprietario) : ''; ?> >CNPJ
                    </div>
                    <input type="text" id="proprietarioCpfCnpj" name="proprietarioCpfCnpj"
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('proprietarioCpfCnpj', $proprietario, $_POST) : '';  ?>"                         <?php  
                            if (isset($_SESSION) && isset($_SESSION['iniciado']) && $_SESSION['iniciado'] == TRUE) {
                                $_POST['proprietarioCpfCnpj'] = 
                                print 'readonly';
                            }
                            else {
                                if (isset($cadastro)) {
                                    $cadastro->isInputDisabled($proprietario);
                                }
                            } 
                        ?>/>
                    <?php 
                    if ((!isset($_SESSION)) || (isset($_SESSION) && !isset($_SESSION['iniciado']))) {
                        print '<br />';
                        print '<img id="captcha" src="securimage/securimage_show.php" alt="CAPTCHA Image" />';
                        print '<br />';
                        print '<p class="avanca"><a href="#" onclick="document.getElementById(\'captcha\').src = \'securimage/securimage_show.php?\' + Math.random(); return false">Gerar outra imagem</a>';
                        print '<br />';
                        print '<br />';
                        print 'Digite os caracteres da imagem e clique em "AvanÁar"';
                        print '<br />';
                        print '<input type="text" id="captcha_code" name="captcha_code" /><br /><input type="submit" value="AvanÁar" name="avancar" id="avancar" />'; 
                        print '</p>';
                    }
                    ?>
                </div>

                <?php
                    if (isset($_SESSION) && isset($_SESSION['iniciado'])) {
                ?>

                <div id="divVeiculoPlaca">
                    <label for="veiculoPlaca">Placa</label>
                    <input type="text" id="veiculoPlaca" name="veiculoPlaca"
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('veiculoPlaca', $proprietario, $_POST) : '';  ?>" />
                </div>

                <div id="divVeiculoCodRenavam">
                    <label for="veiculoCodRenavam">RENAVAM</label>
                    <input type="text" id="veiculoCodRenavam" name="veiculoCodRenavam"
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('veiculoCodRenavam', $proprietario, $_POST) : '';  ?>" />
                </div>

                <div id="divVeiculoMarca">
                    <label for="veiculoMarca">Marca</label>
                    <input type="text" id="veiculoMarca" name="veiculoMarca"
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('veiculoMarca', $proprietario, $_POST) : '';  ?>" />
                </div>

                <div id="divVeiculoModelo">
                    <label for="veiculoModelo">Modelo</label>
                    <input type="text" id="veiculoModelo" name="veiculoModelo"
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('veiculoModelo', $proprietario, $_POST) : '';  ?>" />
                </div>

                <div id="divVeiculoAno">
                    <label for="veiculoAno">Ano</label>
                    <input type="text" id="veiculoAno" name="veiculoAno"
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('veiculoAno', $proprietario, $_POST) : '';  ?>" />
                </div>

                <div id="divVeiculoCor">
                    <label for="veiculoCor">Cor</label>
                    <input type="text" id="veiculoCor" name="veiculoCor"
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('veiculoCor', $proprietario, $_POST) : '';  ?>" />
                </div>           
                
            </div>
            </div>

            <div id="pessoas">
            <div id="divInformacoesPessoais">
            <div id="divdivLogradouroNome">
                <div id="divProprietarioNome">
                    <label for="proprietarioNome">Nome <br />ou Raz&atilde;o Social</label>
                    <input type="text" id="proprietarioNome" name= "proprietarioNome" 
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('proprietarioNome', $proprietario, $_POST) : '';  ?>" 
                        <?=  isset($cadastro) ? $cadastro->isInputDisabled($proprietario) : ''; ?> />
                </div>
               <div id="divProprietarioTelefone">
                    <label for="proprietarioTelefone">*Telefone</label>
                    <input type="text" id="proprietarioTelefone" name="proprietarioTelefone" 
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('proprietarioTelefone', $proprietario, $_POST) : '';  ?>" 
                        <?= isset($cadastro) ? $cadastro->isInputDisabled($proprietario) : '' ?> />
                </div>
                
                <div id="divLogradouroCod">
                    <label for="logradouroCod">Endere&ccedil;o</label>      
                </div>

                <?= isset($cadastro) ? $cadastro->limitesComoHtmlSelect($_POST, $proprietario) : ''; ?>
        
            </div>
            
            <div id="logradouroTipoEndereco">
                
                    <input type="radio" id="logradouroTipoEnderecoResidencial" name="logradouroTipoEndereco" 
                        value="R" <?= isset($cadastro) ? $cadastro->getTipoEnderecoChecked('R', $proprietario, $_POST) : ''; ?>
                        <?= isset($cadastro) ? $cadastro->isInputDisabled($proprietario) : '' ?>>Residencial
                        
                    <input type="radio" id="logradouroTipoEnderecoComercial" name="logradouroTipoEndereco" 
                        value="C" <?= isset($cadastro) ? $cadastro->getTipoEnderecoChecked('C', $proprietario, $_POST) : ''; ?>
                        <?= isset($cadastro) ? $cadastro->isInputDisabled($proprietario) : '' ?>>Comercial
                
            </div>
            
                 <div id="divLogradouroNumero">
                    <label for="logradouroNumero">N&uacute;mero</label>
                    <input type="text" id="logradouroNumero" name="logradouroNumero"
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('logradouroNumero', $proprietario, $_POST) : '';  ?>"
                        <?= isset($cadastro) ? $cadastro->isInputDisabled($proprietario) : '' ?> />
                </div>
                <div id="divLogradouroComplemento">
                    <label for="logradouroComplemento">*Complemento</label>
                    <input type="text" id="logradouroComplemento" name="logradouroComplemento"
                        value="<?= isset($cadastro) ? $cadastro->getInputValue('logradouroComplemento', $proprietario, $_POST) : '';  ?>" 
                        <?= isset($cadastro) ? $cadastro->isInputDisabled($proprietario) : '' ?> />
                </div>
                <div id="divLogradouroBairro">
                    <label for="logradouroBairro">Bairro</label>
                    <?= isset($cadastro) ? $cadastro->bairroComoHtmlSelect($_POST, $proprietario) : ''; ?>
                </div>
                <div id="divLogradouroCep">
                    <label for="logradouroCep">CEP</label>
                    <input type="text" id="logradouroCep" name="logradouroCep"
                        value="<?= isset($cadastro) ? $cadastro->cepValue($_POST, $proprietario) : '';  ?>" readonly />
                </div>
                <div id="divOpcionais">
                    <h6>* Campos opcionais</h6>
                </div>
            </div>
            </div>
            
            <div id="botoes">
            <div id="divControles">
                <input type="submit" value="In&iacute;cio" name="destruirSessao" id="destruirSessao" <?= isset($cadastro) ? $cadastro->veiculosJaSalvos($proprietario, $_POST) : '';  ?>
/>
                <input type="submit" 
                    value="<?= (isset($proprietario) && $proprietario->isProprietarioConfirmado()) ? 'Adicionar' : 'Avan&ccedil;ar'; ?>" 
                    name="salvar" 
                    id="salvar"  />    
                <input type="submit" 
                    value="Salvar" 
                    name="finalizar" 
                    id="finalizar" 
                    <?= ((isset($proprietario) && (!$proprietario->isProprietarioConfirmado())) || (isset($cadastro) && $cadastro->veiculosJaSalvos($proprietario, $_POST) == '')) ? 'disabled' : ''; ?> />
            </div>
            </div>
             <h3><?= (isset($proprietario) && ($proprietario->isProprietarioConfirmado())) ? 'Ve&iacute;culos cadastrados para ' . $proprietario->getNroCpfCnpj() : ''; ?></h3>
                <div id="divVeiculosContainer">
                     <?php
                         if (isset($proprietario) && $proprietario->isProprietarioConfirmado()) {
                             echo $proprietario->veiculosComoDiv();
                             echo $proprietario->linksVeiculosComoUl();
                             echo $proprietario->scriptDialogs();
                         }
                     ?>
                </div>
            

            <?php
                }
                else {
                    
            ?>
                    </div>
                </div>
            <?php
                 }
            ?>  

       </form>

        <?php
            if (!$validado) {
        ?>
                <script>
                    window.document.getElementById("captcha_code").focus();
                </script>
        <?php
            }
        ?>


        </div>
	<!--  fecha conteudo  -->



<!--  abre espaÁo limpa rodape    -->
<div id="limpa_rodape"></div>
<!--  fecha espaÁo limpa rodape  -->

</div>
<!--  fecha container  -->

<!--  abre rodape  -->
<div id="rodape">

	<!--   abre alinha rodape  -->
	<div id="alinha_rodape">
	<a id="poatravel" href="http://www.portoalegre.travel/"><img src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/botoes/logo_poatravel.jpg" title="POA Travel" alt="POA Travel"></a>&nbsp;&nbsp;&nbsp;&nbsp;
<a id="logopmpabranco" href="http://www2.portoalegre.rs.gov.br/nossaportoalegre"><img src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/botoes/logo_nossapoa.jpg" title="Nossa Porto Alegre" alt="Nossa Porto Alegre"></a>
<a id="flickrpmpa" href="http://www.flickr.com/photos/48286463@N08/" target="_blank"><img src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/botoes/flickrpoa_rodape.png" title="Flickr da Prefeitura" alt="Flickr da Prefeitura"></a>
<a id="rssfeed" href="http://www2.portoalegre.rs.gov.br/cs/rss.php"><img src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/botoes/rss_rodape.png" title="RSS da Prefeitura" alt="RSS da Prefeitura"></a>
<a id="twitterpmpa" href="http://twitter.com/Prefeitura_POA/" target="_blank"><img src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/botoes/twitter_rodape.png" title="Twitter da Prefeitura" alt="Twitter da Prefeitura"></a>
<img src="http://www2.portoalegre.rs.gov.br/proweb3_geral/images/icones/icone_site_pmpa_movel.gif" title="Porto Alegre MÛvel" alt="Porto Alegre MÛvel">
<p><strong>Prefeitura Municipal de Porto Alegre </strong>- PraÁa MontevidÈo, 10 - Rio Grande do Sul - Brasil - CEP 90010-170</p>	</div>
	<!--   fecha alinha rodape  -->


</div>
<!--  fecha rodape  -->


<!-- abre script estatÌstica -->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script><script src="./equeleto_cadastro_veiculos_files/ga.js" type="text/javascript"></script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-13113255-47");
pageTracker._trackPageview();
} catch(err) {}</script>



</body></html>
