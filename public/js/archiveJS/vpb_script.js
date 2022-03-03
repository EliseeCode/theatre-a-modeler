/**************************************************************
* This script is brought to you by Vasplus Programming Blog
* Website: www.vasplus.info
* Email: info@vasplus.info
****************************************************************/


//This is the Upload Function
function vpb_upload_and_resize(param) 
{
	console.log("MAJ_ligne_image(param="+param+"test");
	//alert('COOL'+param);
	$("#vpb_file_attachment_form").vPB({
		url: 'vpb_uploader.php?id='+param,
		beforeSubmit: function() 
		{
			$("#vpb_upload_status").html('<div style="font-family: Verdana, Geneva, sans-serif; font-size:12px; color:black;" align="center">Please wait <img src="image/attente.gif" align="absmiddle" title="Upload...."/></div><br clear="all">');
		},
		success: function(id_image) 
		{
		remove_fenetre();
		console.log("MAJ_ligne_image(param="+param+"/"+id_image);
		MAJ_ligne_image(param,id_image);
			//$("#vpb_upload_status").hide().fadeIn('slow').html(response);
		}
	}).submit(); 
}

function vpb_upload_and_resize_titre(param) 
{
	//alert('COOL'+param);
	$("#vpb_file_attachment_form").vPB({
		url: 'vpb_uploader.php?id=titre_'+param,
		beforeSubmit: function() 
		{
			$("#vpb_upload_status").html('<div style="font-family: Verdana, Geneva, sans-serif; font-size:12px; color:black;" align="center">Please wait <img src="image/attente.gif" align="absmiddle" title="Upload...."/></div><br clear="all">');
		},
		success: function(response) 
		{
		remove_fenetre();
		MAJ_ligne_image_titre(param);
			//$("#vpb_upload_status").hide().fadeIn('slow').html(response);
		}
	}).submit(); 
}