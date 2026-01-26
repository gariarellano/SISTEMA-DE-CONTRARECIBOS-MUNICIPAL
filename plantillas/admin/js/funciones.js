var pagAct=1;

function AjaxForm(Form, DivResponse){
	$.ajax({
		url:Form.attr("action"),
		type:Form.attr("method"),
		data:Form.serialize(),
		success:function(Response){
			DivResponse.html(Response);
		}
	});
}

function GenerarPaginador(){
	$("div.Paginar").each(function(){
		var cantidad = $(this).attr("rev");
		var i=1;
		var pag=0;
				
		$(this).removeAttr("rev").removeClass("Paginar");
		
		$(this).find(".fila").each(function(){
			if(i==1){
				pag++;
				$(this).before("<div id='pag"+pag+"' style='display:"+ (pag>1?"none":"block") +";'></div>");
			}
			$("div#pag"+pag).append($(this));
			
			if(i==cantidad){
				i=0;
				
			}
			i++;
		});
		
		$(this).parent().append("<div class='Paginas'></div>");
		for(var i=pag; i>0; i--){
			$("div.Paginas").append("<div class='PagNum "+(i==pagAct?"PagAct":"")+"' onClick='javascript:paginador(this)'>"+i+"</div>");
		}
	});
}

function paginador(Element){
	pagina=parseInt($(Element).html());
	
	if(pagAct != pagina){
		$("#pag"+pagAct).fadeOut().queue(function(){
			$("#pag"+pagina).fadeIn();
			$(".PagNum").removeClass("PagAct");
			$(Element).addClass("PagAct");
			pagAct=pagina;
			$(this).dequeue();
		});
	}
}

$(".AjaxLoad").each(function(){
	$(this).addClass("load");
	
	$(this).load($(this).attr("rev"),{"ajax":true},function(){
		$(this).removeAttr("rev").removeClass("AjaxLoad load");
		GenerarPaginador();
	});
});

$("a.Eliminar").live("click",function(){
	var title=$(this).find("img").attr("title");
	var href=$(this).attr("href").split("/");
	var max=href.length;
	
	if(!confirm("CONFIRMA \n" + title + ": "+href[max-1])){
		return false;
	}
	
});

function submenu(element){
	$(".active").next().fadeOut();
	$(".active").removeClass("active");
	$(element).addClass("active").next().fadeIn();
}

/*$(".MsgForm").dialog({
	autoOpen: false,
	width: 370,
	modal:true,
	resizable:false,
	dialogClass: "alert",
	buttons:{
		"Guardar": function(){
			$("#ProcesarEstado").submit();
		},
		"Cancelar": function(){
			$(this).dialog("close");
		}
	}
});

$("#ProcesarEstado").validate({
	errorElement:"",
	highlight: function(element){ $(element).css("border","2px solid #A52A2A"); },
	unhighlight: function(element){ $(element).css("border","2px solid #D5D1CE");}
});*/

GenerarPaginador();
