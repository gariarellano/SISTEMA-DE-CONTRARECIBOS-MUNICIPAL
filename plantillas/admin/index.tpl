<!DOCTYPE html>

<html lang="en">

<head>

	<title>Panel de control facturas</title>


	<?php 

		$this->Html->Meta("utf8");

		$this->Html->LinkCss("layout");

		$this->Html->LinkCss("jquery");

		echo $this->Html->Mostrar();

	

		$this->Javascript->JS("jquery");

		$this->Javascript->JS("jquery-ui");

		$this->Javascript->JS("tablesorter");

		$this->Javascript->JS("equalHeight");

		$this->Javascript->JS("hideshow");

		$this->Javascript->JS("funciones");

		$this->Javascript->JS("validate");

		$this->Javascript->JS("tiny/tiny_mce");

		$this->Javascript->JS("jquery.maskMoney");

	?>



	<!--[if lt IE 9]>

		<?php $this->Html->LinkCss("ie");?>

		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>

	<![endif]-->

</head>

<body>

	<header id="header">

		<hgroup>

			<h1 class="site_title"><?php echo $this->Html->Link("","Panel de Control facturas");?></h1>

			<h2 class="section_title">Panel de Administración</h2><div class="btn_view_site"><?php echo $this->Html->Link("../","Ir al sitio");?></div>

		</hgroup>

	</header> <!-- end of header bar -->

	

	<section id="secondary_bar">

		<div class="user">

			<p><?php echo isset($Usuario)?"Administrador (".$this->Html->Link("usuario/salir","Salir").")":"Invitado";?></p>

			<!-- <a class="logout_user" href="#" title="Logout">Logout</a> -->

		</div>

		<div class="breadcrumbs_container">

			<article class="breadcrumbs">

				<?php 

				echo $this->Html->Link("javascript:;","Admin",array("prefijo"=>false));

				echo '<div class="breadcrumb_divider"></div>';

				echo $this->Html->Link("javascript:;",($control!="Index"?$control:"Escritorio"),array("prefijo"=>false));

				

				if($archivo!="admin_index"){

					echo '<div class="breadcrumb_divider"></div>';

					echo $this->Html->Link("javascript:;",ucwords(str_replace("admin_","",$archivo)),array("class"=>"current","prefijo"=>false));

				}

				?>

			</article>

		</div>

	</section><!-- end of secondary bar -->

	

	<aside id="sidebar" class="column">

		<?php if(isset($Usuario)){ ?>

		<h3><?php echo $this->Html->link("proveedores","Proveedores");?></h3>	  

        <h3>Facturas</h3>

		<ul class="toggle">

			<li class="icn_categories"><?php echo $this->Html->Link("facturas","Facturas")?></li>
	<li class="icn_categories"><?php echo $this->Html->Link("contrarecibos","Contrarecibos")?></li>

		</ul>

		<footer style="clear:both;">

			<hr />

			<p><strong>Copyright &copy; 2016 Admin facturas.com</strong></p>

			<p>Create by <a href="http://www.rsolache.com">rsolache</a></p>

		</footer>

		<?php } ?>

	</aside><!-- end of sidebar -->

	

	<section id="main" class="column">

		<?php if($ErrorMsg){ ?>

		<h4 class="alert_info"><?php echo $ErrorMsg;?></h4>

		<script>

			setTimeout(function(){

				$(".alert_info").fadeOut("slow",function(){ $(this).remove(); });

			},10000);

		</script>

		<?php } 

		

			echo $body;

			//echo $LOGDB;

		?>

		<!--

		<h4 class="alert_warning">A Warning Alert</h4>

		<h4 class="alert_error">An Error Message</h4>

		<h4 class="alert_success">A Success Message</h4>

		-->

		<div style="clear:both; height:25px;"></div>

		<div class="spacer"></div>

	</section>

	<?php echo $this->Javascript->Mostrar();?>

	

	<script type="text/javascript">

		$(".tablesorter").tablesorter(); 

		

		//When page loads...

		$(".tab_content").hide();

		$("ul.tabs li:first").addClass("active").show();

		$(".tab_content:first").show();



		

		$("ul.tabs li").click(function() {

			$("ul.tabs li").removeClass("active");

			$(this).addClass("active");

			$(".tab_content").hide();



			var activeTab = $(this).find("a").attr("href");

			

			if(activeTab.indexOf("javascript") == -1)

				$(activeTab).fadeIn();

			

			$("input#action").val("agregar");

			return false;

		});

		

		$(function(){

			$('.column').equalHeight();

		});

		

		function InicarTinyMce() {

			tinyMCE.init({

				mode : "textareas",

				theme : "advanced",

				language : 'es',

				plugins : "paste",

				theme_advanced_buttons1 : "cut,copy,paste,pastetext,pasteword|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,styleselect,formatselect,fontselect,fontsizeselect",
   
				theme_advanced_buttons2 :"",

				theme_advanced_buttons3 :"",

				theme_advanced_buttons4 :"",

				theme_advanced_toolbar_location : "top",

				theme_advanced_toolbar_align : "left",

				theme_advanced_statusbar_location : "bottom",

				theme_advanced_resizing : false,
				
				font_size_classes : "fontSize1, fontSize2, fontSize3, fontSize4, fontSize5, fontSize6",//i used this line for font sizes 

				force_br_newlines : true,

				force_p_newlines : false,

				forced_root_block : ''

			});

		}

	</script>

</body>

</html>

