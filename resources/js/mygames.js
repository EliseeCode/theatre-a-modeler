$('.new-game').click(function(){
	console.log("creation new game en cours...");
	$.getJSON("ajax_req.php?action=newgame", function(data_game){
		console.log(data_game);
		afficher_mygame(data_game);
		//$.each(result, function(i, data_game){
			//console.log(data_game);
			//afficher_mygame(data_game);
		//});	
	});	
});

function change_name_evt(){
$('.input_name_game').unbind();
$('.input_name_game').change(function(){
	console.log("Changement du nom du jeu...");
	id_game=$(this).parent().attr('id').substr(7);
	name_game=$(this).val();
	$.getJSON("ajax_req.php?action=updategamename&id_game="+id_game,{name:name_game}, function(result){
		console.log(result);
		if(result.status=="ok")
		{
			console.log(id_game);
			$("#mygame_"+id_game+">.input_name_game").addClass("validated").delay(100).queue('fx', function() { $(this).removeClass('validated'); });;
			
			//$("#mygame_"+id_game+">.input_name_game").removeClass('validated').delay(1000);
		}
	});	
});
}

function update_status(id_game,status,edit_key){
	console.log("Changement du status du jeu..."+id_game+status+edit_key);
	$.getJSON("ajax_req.php?action=updategamestatus&id_game="+id_game,{status:status}, function(result){
		if(result.status=="ok")
		{	
			afficher_edit(status,id_game,edit_key);
			$('.drop_edit_'+id_game).dropdown('toggle');
		}
	});	
}

function delete_game(id_game)
{
	if(confirm("Cette action supprimera definitivement ce jeu."))
	{
	console.log("delete a game...");
	$.getJSON("ajax_req.php?action=deletegame&id_game="+id_game, function(result){
		console.log(result);
		if(result.status=="ok")
		{
			$("#mygame_"+id_game).remove();
		}
	});	
	}
}

function new_play(id_game)
{
	// AJAX CREATION
	console.log("create a play...");
	$.getJSON("ajax_req.php?action=newplay&id_game="+id_game, function(result){
		console.log(result);
		if(result.status=="ok")
		{
			id_play=result.id_play;
			play_key=result.play_key;
			afficher_partie(id_game,id_play,play_key);
			$('.menu_jouer_'+id_game).dropdown('toggle');
		}
	});	
}

function delete_play(id_play)
{
	if(confirm("Cette action supprimera definitivement cette partie."))
	{
	console.log("delete a partie...");
	$.getJSON("ajax_req.php?action=deleteplay&id_play="+id_play, function(result){
		console.log(result);
		if(result.status=="ok")
		{
			$(".play_"+id_play).remove();
		}
	});	
	}
}

function afficher_partie(id_game,id_play,play_key)
{
	$('.menu_jouer_'+id_game+'>.new_play_btn').after('<li class="divider"></li><li class="play_'+id_play+'">'+
	'<div class="onglet"><img src="img/serrure.png" width="30px;"> : '+play_key+'</div>'+
	'<div  class="onglet clickable" onclick="location.href=\"./supervision.php?id='+id_play+'\";">Supervision</div><div  class="onglet clickable" style="color:red;" onClick="delete_play('+id_play+')">Supprimer</div></li>');			
}
function afficher_edit(status,id_game,edit_key)
{
	/*
	if(status=="noedit")
	{
		$('.drop_edit_'+id_game).html('<li><div  class="onglet clickable" onClick="open_edit('+id_game+',\''+edit_key+'\');">Générer une clé pour rendre accessible d\'éditeur</div></li>');
	}
	else
	{$('.drop_edit_'+id_game).html('<li><div  class="onglet clickable" onClick="close_edit('+id_game+',\''+edit_key+'\');">Verrouiller l\'éditeur</div></li>');
	$('.drop_edit_'+id_game).append("<li><div  class='onglet'><img src='img/serrure2.png' width='30px;'>"+edit_key+"</div></li>");
	$('.drop_edit_'+id_game).append("<li><div  class='onglet clickable' onclick='location.href=\"editeur_enigme.php?id="+id_game+"&type=boss\"'>Editer le jeu</div></li>");
	}
	*/
}

function open_edit(id_game,edit_key)
{
	//update_status(id_game,"editable",edit_key);
}
function close_edit(id_game,edit_key)
{
	//update_status(id_game,"noedit",edit_key);
}

function afficher_mygame(data_game){
	
	$('#new_game_form').after('<div class="form-mygame" style="display:none;" id="mygame_'+data_game.id+'">'+
	'<img class="" src="./img/circular_maze.png" style="width:70px; display:inline-block;">'+
	'<input class="input_name_game" type="text" style="display:inline-block; margin-left:30px;" value="'+data_game.name+'">'+	
	'<h3 style="float:right;display:inline-block;">'+
	'<div class="dropdown drop_jouer_'+data_game.id+'" style="display:inline-block;">'+
	'<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Jouer <span class="caret"></span></button>'+
    '<ul class="dropdown-menu menu_jouer_'+data_game.id+'">'+
	'<li class="new_play_btn"><div class="btn btn-primary" style="margin:3px 5px;" onclick="new_play('+data_game.id+');">Nouvelle partie</div></li>'+
	'</ul></div>'+	
	
    '<button class="btn btn-warning" type="button" onclick="location.href=\'editeur_enigme.php?id='+data_game.id+'&type=boss\'">Editer '+
    '</button>'+
   
	'<button class="btn btn-danger" style="margin:0px 16px;" onclick="delete_game('+data_game.id+');">Supprimer</button>'+
	'</h3><br style="clear:both;" />');
	$.each(data_game.partie, function(i, partie){
		afficher_partie(data_game.id,partie.id,partie.key);
		});	
	$("#mygame_"+data_game.id).fadeIn("slow");
change_name_evt();	
}

//data_game={statusA:'ok',id:3,name:'TTTTT',edit_key:"KAPUNKA",partie:[]};
//afficher_mygame(data_game);


/*
$(".Del").on("click", function() {
			if (confirm('Es-tu sur de vouloir supprimer ce paquet de cartes?')) {
				var id_menu = $(this).parent().parent().attr("id");
				id_menu=id_menu.substr(6,id_menu.length);
				
				if(id_menu!=""){
					$.ajax({
					url: "ajax_req.php?action=del_group&id=" + id_menu,
					complete : function(xhr,result){
					console.log(xhr.responseText);
					}	
					});
				}
			} 
			else {}//si il inffirme la suppression
			});
*/