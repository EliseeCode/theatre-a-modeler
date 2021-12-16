function saveGame(){
	console.log("Save des datas du jeu en cours...");
	//gameJSON={desciption, theme, duree, liaisons}
	description=$('#description_game').val();
	description=description.replace(/\n/g,"--");
	description=description.replace(/"/g,"'");
	console.log(description);
	
	theme=$("#theme").val();
	duree=$("#duree").val();
	liaisonsJSON=JSON.stringify(liaisons);
	json_data='{"description":"'+description+'","theme":'+theme+',"duree":'+duree+',"liaisons":'+liaisonsJSON+'}';
	$.getJSON("ajax_public.php?action=updateGameJSON&json_data="+json_data, function(data){
		console.log(data);
	});	
}
function change_game_name(){
	console.log("Changement du nom du jeu...");
	name_game=$(".input_name_game").val();
	$.getJSON("ajax_public.php?action=updategamename&name="+name_game, function(result){
		console.log(result);
		if(result.status=="ok")
		{
			$(".input_name_game").addClass("validated").delay(100).queue('fx', function() { $(".input_name_game").removeClass('validated'); });;
			
			//$("#mygame_"+id_game+">.input_name_game").removeClass('validated').delay(1000);
		}
	});	
}

function addBubbleButton(){
	console.log("addBubble");
	
		$.getJSON("ajax_public.php?action=newEnigma", function(data){
		console.log(data);
		
		if(data.status=="ok")
		{
			data_enigme=JSON.parse(data.data);
			console.log(data_enigme);
			id_enigme=data.id_enigme;
			name=data_enigme.name;
		bubbles[id_enigme]=new bubble(id_enigme,name,0,[],[],xMax/2+200*Math.random(),yMax/2+200*Math.random()-100);
		preAnimation();
		}
		else if(data.status=="toomuch"){
			$("#message_alert").html("<div class='alert alert-danger'>Vous avez atteint le nombre d'énigme maximal autorisé pour ce jeu.</div>");
		}
	});			
}
function delete_enigme()
{
	if(confirm("Cette action supprimera definitivement cette enigme."))
	{
	id_enigma=parseInt(id_enigma);
	deleteFile();
	
	console.log("delete "+id_enigma);
	for(l=liaisons.length-1; l>=0; l--)
	{	myLiaison=liaisons[l];
		if(myLiaison[0]==id_enigma || myLiaison[1]==id_enigma)
		{
		suppr_elem(myLiaison[1],bubbles[myLiaison[0]].liaisonsuiv);
		suppr_elem(myLiaison[0],bubbles[myLiaison[1]].liaisonprec);
		suppr_liaison(myLiaison[0],myLiaison[1]);
		}
	}
	suppr_elem(id_enigma,bubblesIds);
    delete bubbles[id_enigma];
	$("#bubble_"+id_enigma).remove();
	
	unselect_enigma();
	saveGame();
	
	$.getJSON("ajax_public.php?action=deleteEnigma&id_enigme="+id_enigma, function(data){
	console.log(data);});
	preAnimation();
	}
}

function saveEnigme(){
	versionNum=new Date().getTime();
	console.log("Save des datas de l'énigme en cours...");
	//{name, theme, action_type, action_data, indice_type, indice_data}
	name=$('.input_name_enigme').val().replace(/"/g,"'");
	enigma_name=name;
	theme='0';
	action_type=$('#action_select').val().replace(/"/g,"'");
	indice_type=$('#indice_select').val().replace(/"/g,"'");
	aide_enigme=$('#aide_enigme').val().replace(/"/g,"'").replace(/\n/g,"--");
	//gestion des data des actions & indice.
		if(action_type=="cadenas" || action_type=="objet"){
			action_data=$('#'+action_type+'_action_input').val().replace(/"/g,"'");
		}else if(action_type=="website")
		{action_data=$('#website_action_link').val().replace(/"/g,"'");}
		if(action_type=="QRcode"){getQRcode();}
		
		if(indice_type=="text" || indice_type=="website" || indice_type=="video360" || indice_type=="objet"){
			//console.log(indice_type+" "+indice_data);
			indice_data=$('#'+indice_type+'_indice_input').val().replace(/"/g,"'");;
		}
		else if(indice_type=="image" || indice_type=="audio" || indice_type=="video")
		{
			filename=$('#'+indice_type+'_indice_input').val();
		if(filename!=""){
			var extension = filename.substr( (filename.lastIndexOf('.') +1) );
			indice_data=indice_type+id_enigma+"."+extension;
			if(indice_type=="image")
			{	console.log("show image");
				$("#preview_indice").html('<img src="uploads/'+indice_data+'?cb='+versionNum+'" width="300px"><br><div class="btn btn-danger delFile" onclick="deleteFile();"></div>').show();
			}
			else if(indice_type=="audio")
			{
				console.log("show audio");
				
			$("#preview_indice").html(''+
			'<audio id="myIndiceAudio" preload="auto" controls>'+
				'<source src="uploads/'+indice_data+'?cb='+versionNum+'" type="audio/mpeg">'+
			'</audio>'+
			'<br><div class="btn btn-danger delFile" onclick="deleteFile();"></div>').show();
			console.log("audio changed");
			}
			else if(indice_type=="video")
			{
				console.log("show video");
				$("#preview_indice").html(''+
			'<video width="320" height="240" controls>'+
				'<source src="uploads/'+indice_data+'?cb='+versionNum+'" type="video/mp4">'+
				'Your browser does not support the video tag.'+
			'</video>'+
			'<br><div class="btn btn-danger delFile" onclick="deleteFile();"></div>').show();
			}	
			else
			{
			$("#preview_indice").html('');
			$("#preview_indice").hide();
			}
		}
		}
	//action_description=$('#action_description').val();
	//indice_description=$('#action_description').val();
	json_data='{"name":"'+name+'","theme":"'+theme+'","action_type":"'+action_type+'","action_data":"'+action_data+'","indice_type":"'+indice_type+'","indice_data":"'+indice_data+'","aide":"'+aide_enigme+'"}';
	$.getJSON("ajax_public.php?action=updateEnigma&id_enigme="+id_enigma+"&json_data="+json_data, function(data){
		console.log(data);
	});
	$('#bubble_'+id_enigma).find(".titre_bubble").text(name);	
}

//==================GESTION DES FICHIERS=================

function upload(that){
    file=that.files[0];
	console.log(file.size);
	console.log(file.name);
	$("#preview_indice").show();
	$("#preview_indice").html('<div class="progress">'+
			'<div class="upload_progress progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 10%"></div>'+
	'</div>');
	
    if (file.size > 50*1024*1024) {
        alert('max upload size is 50M')
    }
	var formData = new FormData();
	type_enigme=$("#indice_select").val();
	formData.append("type", type_enigme);
	formData.append("id_enigme", id_enigma);
	formData.append("fileToUpload", file);
	var request = new XMLHttpRequest();
	
	
	request.upload.onprogress = function (evt) {
  
               var percentComplete = parseInt(evt.loaded *100/ evt.total);
               // Do something with download progress
               
			   $('.upload_progress').css("width",percentComplete+'%');

	}
	
          
	
	
	request.onreadystatechange = function() {
    if (request.readyState == XMLHttpRequest.DONE) {
        console.log(request.responseText);
		saveEnigme();
		
		}
	}
	request.open("POST", "upload.php");
	request.send(formData);
}
	
	
var id_enigma=-1;
var action_data="";
var indice_data="";
var enigma_name="";
function ok_enigme()
{
	saveEnigme();
	unselect_enigma();
}
//===========BUBBLE CLICKED
function bubbleClicked(id)
{
	if(id!=0)
	{
	id_enigma=id;
	$('#preview_btn_enigme').unbind();
	$('#preview_btn_enigme').on("click",function(){window.open('./enigme_preview/'+id_game+"/"+id_enigma+"/0");});
	$('.bubble').removeClass('bubble_selected');
	$('#bubble_'+id).addClass('bubble_selected');
	$('#parametre_enigme').slideDown(
	function(){location.href="#parametre_enigme";});
	
	var $root = $('html, body');
    $root.animate({
        scrollTop: $("#parametre_enigme").offset().top
    }, 1000);
	
	//recuperation des info de l'énigme
	$.getJSON("ajax_public.php?action=getEnigma&id_enigme="+id, function(data){
	console.log(data);
	//{name, theme, action_type, action_data, indice_type, indice_data}
		var id_enigme=data.id_enigme;	
		var data_enigme=JSON.parse(data.json_data);
		var name=data_enigme.name;
		enigma_name=name;
		var theme=data_enigme.theme;
		var action_type=data_enigme.action_type;
		var action_data=data_enigme.action_data;
		var indice_type=data_enigme.indice_type;
		var indice_data=data_enigme.indice_data;
		var aide_enigme=data_enigme.aide;
		$('.input_name_enigme').val(name);
		$('#action_select').val(action_type);
		$('#indice_select').val(indice_type);
		//gestion des data des actions & indice.
		if(action_type=="cadenas" || action_type=="objet"){
			$('#'+action_type+'_action_input').val(action_data);
		}
		else if(action_type=="website")
		{$('#website_action_link').val(action_data);}
		if(action_type=="QRcode"){getQRcode();}
		if(indice_type=="text" || indice_type=="website" || indice_type=="video360" || indice_type=="objet"){
			$('#'+indice_type+'_indice_input').val(indice_data);
		}
			
		$('#aide_enigme').val(aide_enigme);		
		update_vu("action");
		update_vu("indice");
		
		if(indice_type=="image" && indice_data!=""){
			//$('#image_indice_input').slideUp();
			vt=Date.now();
			$("#preview_indice").html('<img src="uploads/'+indice_data+'?t='+vt+'" width="300px"><br><div class="btn btn-danger delFile" onclick="deleteFile();"></div>').show();
		}	
		if(indice_type=="audio" && indice_data!=""){
			//$('#image_indice_input').slideUp();
			vt=Date.now();
			$("#preview_indice").html(''+
			'<audio controls>'+
				'<source src="uploads/'+indice_data+'?t='+vt+'" type="audio/mpeg">'+
			'</audio>'+
			'<br><div class="btn btn-danger delFile" onclick="deleteFile();"></div>').show();
		}
		if(indice_type=="video" && indice_data!=""){
			//$('#image_indice_input').slideUp();
			vt=Date.now();
			$("#preview_indice").html(''+
			'<video width="320" height="240" controls>'+
				'<source src="uploads/'+indice_data+'?t='+vt+'" type="video/mp4">'+
				'Your browser does not support the video tag.'+
			'</video>'+
			'<br><div class="btn btn-danger delFile" onclick="deleteFile();"></div>').show();
		}
		
	});
	
	}
}
//Affichage des options pour les actions et recompences
function update_vu(type)
{
	action_type=$("#"+type+"_select").val();
	
	if(action_type=="no_action")
	{console.log("no_action");
	$('#aide_enigme_block').slideUp();
	}
	else if(type=="action"){$('#aide_enigme_block').slideDown();}
	
	$('.'+type+'_input_hidden').hide();
	$('#'+action_type+'_'+type+'_input').slideDown();
	$('#preview_indice').html("");
	$('#preview_indice').hide();
}		
	
function deleteFile()
{
	console.log('deleteFile');
	$.getJSON("ajax_public.php?action=deleteFile&id_enigme="+id_enigma, function(data){
	console.log(data);
	
	$('#preview_indice').html("");
	$('#preview_indice').hide();
	$('.indice_input_hidden').val("");
	});	
}
//==========AU CHARGEMENT DE LA PAGE=============
var my_json_data="";
//recupération des Data Enigmes
$.getJSON("ajax_public.php?action=getEnigmas", function(data){
	console.log(data.json_data);
		for(d in data.json_data)
		{
		var id_enigme=data.json_data[d].id_enigme;	
		var data_enigme=JSON.parse(data.json_data[d].data_enigme);
		var name=data_enigme.name;
		if (typeof bubbles[id_enigme] == "undefined") {
			bubbles[id_enigme]=new bubble(id_enigme,name,0,[],[],xMax/2+200*Math.random(),yMax/2+200*Math.random()-100);
			}
		}
		//recupération liaisons & Game datas
		$.getJSON("ajax_public.php?action=getGameJSON", function(data_game){
		console.log(data_game.json_data);
		if(data_game.status=="ok")
		{
		data_game=JSON.parse(data_game.json_data);
		description=data_game.description.replace(/--/g,"\n");
		$('#description_game').val(description);
		$("#theme").val(data_game.theme);
		$("#duree").val(data_game.duree);
		liaisons=data_game.liaisons;
		preAnimation();	
		}
	});	
});
	
	
function getQRcode()
{	
console.log("getQRcode");
$("#qrcode").html('');
new QRCode(document.getElementById("qrcode"),{
	text: "victoire"+id_enigma,
	width: 256,
	height: 256,
	colorDark : "#000000",
	colorLight : "#ffffff",
});
$('#QRcode_action_input').attr("href",$("#qrcode > img").attr('src'));
$('#QRcode_action_input').attr("download",enigma_name+'.png');
}

	
	
	
	
	
	
	
	
	