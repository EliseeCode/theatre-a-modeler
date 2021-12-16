
xMax = (window.innerWidth);
var masse=1;//masse pour l'inertie
var raideur=15;// raideur pour la force de rappel du ressort
var charge=130;//charge élec opposé pour la répulstion 
var longueur=80;// longueur au repos du ressort

function bubble(id,name,typenum,liaisonprec,liaisonsuiv,x,y)
{

//var raideur=20;// raideur pour la force de rappel du ressort
//var charge=200;//charge élec opposé pour la répulstion 
//var longueur=100;// longueur au repos du ressort
var xavant;
var yavant;


this.id=id;
this.name=name;
this.liaisonprec=liaisonprec;
this.liaisonsuiv=liaisonsuiv;
this.masse=masse;
this.raideur=raideur;
this.charge=charge;
this.longueur=longueur;
this.type=typenum;
this.fixe=false;

if (typenum==0)//"standard"
{
this.Yfixe=false;	
ClassSupp="bubble_enigme";
}
if (typenum==1)//"start"
{
this.Yfixe=true;
this.fixe=true;
ClassSupp="bubble_start";
}
if (typenum==2)//"end"
{
this.Yfixe=true;
ClassSupp="bubble_end";
}

this.x=x;
this.xavant=x;
this.y=y;
this.yavant=y;

mazone=document.getElementById('zone_bubble');
mazone.innerHTML+="<div class='bubble "+ClassSupp+"' id='bubble_"+this.id+"' style='top:"+y+"px;left:"+x+"px;'><span class='titre_bubble'>"+this.name+"</span></div>";

}

//Modif du jquery pour passive
jQuery.event.special.touchstart = {
  setup: function( _, ns, handle ){
    if ( ns.includes("noPreventDefault") ) {
      this.addEventListener("touchstart", handle, { passive: false });
    } else {
      this.addEventListener("touchstart", handle, { passive: true });
    }
  }
};


function dragclick_evt()
{	
$(".bubble:not(.bubble_start)").unbind();
var isDragging = false;  
var isMousedown =false;
var delayClickPassed=false;
var LinkableId=[];
var SelectedId=false;
$(".bubble").unbind();

$(".bubble:not(.bubble_start)")
.mousedown(function() {
	isMousedown=true;
    isDragging = false;
	SelectedId=this.id.substr(7);
	LinkableId=getLinkableId(SelectedId);
	console.log('LinkableId');
	console.log(LinkableId);
	timeoutClick=setTimeout(function(){delayClickPassed=true;},200);
	clearTimeout(myAnimationTimeout);
	Anim.start();
})
$(".bubble:not(.bubble_start)")
.on('touchstart',function(e) {
	//e.preventDefault();
	isMousedown=true;
    isDragging = false;
	SelectedId=this.id.substr(7);
	LinkableId=getLinkableId(SelectedId);
	console.log('LinkableId');
	console.log(LinkableId);
	timeoutClick=setTimeout(function(){delayClickPassed=true;},200);
	clearTimeout(myAnimationTimeout);
	Anim.start();
})



	
$(document).unbind();
$(document).on('mouseup touchend',function() {
	$('#liaison_tmp').hide();
	$(".bubble").removeClass("linkable");
	if(SelectedId)//On a cliqué sur une bulle !!!!
	{
    var wasDragging = isDragging;
    isDragging = false;
	isMousedown = false;
	delayClickPassed=false;
	//id=MyObject.id.substr(7);
	bubbles[SelectedId].fixe=false;
	bubbles[SelectedId].charge=charge;
	bubbles[SelectedId].raideur=raideur;
	clearTimeout(timeoutClick);
	
	if (!wasDragging) {
		console.log("clicking");
        //BubbleClicked
		bubbleClicked(SelectedId);		
    }
	else
	{
		//buubleDragged
		console.log('liaisons_tempo'+liaisons_tmp.join('-'));	
		if(liaisons_tmp.length>0)
		{
		liaisons.push(liaisons_tmp); 
		saveGame(); 
		preAnimation();
		}
	}
	//on efface nos traces;
	SelectedId=false;
	}

	if(myAnimationTimeout){clearTimeout(myAnimationTimeout);}
    myAnimationTimeout=setTimeout(function(){Anim.stop();},500);
}); 

$(document).mousemove(function(e) {
	pauseEvent(e);
    if(isMousedown && delayClickPassed)
	{
	//console.log("mouseMove in Document");	
	isDragging = true;
	
	scrollTop     = $(window).scrollTop(),
	scrollLeft     = $(window).scrollLeft(),
    elementOffsetTop = $('#zone_bubble').offset().top,
    offsetZoneTop      = (elementOffsetTop - scrollTop);
	elementOffsetLeft = $('#zone_bubble').offset().left,
    offsetZoneLeft      = (elementOffsetLeft - scrollLeft);
		
	var y=e.clientY-offsetZoneTop;
	var x=e.clientX-offsetZoneLeft;
	//id=MyObject.id.substr(7);
	xB=x;
	yB=y;
	if (x<R+50)
	{xB=R+50;}
	if (y<R)
	{yB=R;}
	if (x>xMax-R-50)
	{xB=xMax-R-51;}
	if (y>yMax-R-50)
	{yB=yMax-R-51;}
	
	bubbles[SelectedId].x=xB;
	bubbles[SelectedId].y=yB;
	bubbles[SelectedId].fixe=true;
	bubbles[SelectedId].charge=0;
	bubbles[SelectedId].raideur=raideur/2;
	//BubbleDragged
	getClosestBubble(SelectedId,LinkableId);
	}
 })
 document.getElementById("parametre_game").addEventListener('touchmove',function(e) {
	 console.log("touchmove"+e.touches[0].clientX);
	//e.preventDefault();
	pauseEvent(e);
    if(isMousedown && delayClickPassed)
	{
	//console.log("mouseMove in Document");	
	isDragging = true;
	
	scrollTop     = $(window).scrollTop(),
	scrollLeft     = $(window).scrollLeft(),
    elementOffsetTop = $('#zone_bubble').offset().top,
    offsetZoneTop      = (elementOffsetTop - scrollTop);
	elementOffsetLeft = $('#zone_bubble').offset().left,
    offsetZoneLeft      = (elementOffsetLeft - scrollLeft);
		
	var y=e.touches[0].clientY-offsetZoneTop;
	var x=e.touches[0].clientX-offsetZoneLeft;
	//id=MyObject.id.substr(7);
	xB=x;
	yB=y;
	if (x<R+50)
	{xB=R+50;}
	if (y<R)
	{yB=R;}
	if (x>xMax-R-50)
	{xB=xMax-R-51;}
	if (y>yMax-R-50)
	{yB=yMax-R-51;}
	
	bubbles[SelectedId].x=xB;
	bubbles[SelectedId].y=yB;
	bubbles[SelectedId].fixe=true;
	bubbles[SelectedId].charge=0;
	bubbles[SelectedId].raideur=raideur/2;
	//BubbleDragged
	getClosestBubble(SelectedId,LinkableId);
	}
 },{passive:false})
 
 
 
 
 $('#zone_bubble').mouseup(function(){
	if(!SelectedId)
	{
		unselect_enigma();
	}
})
}

var liaisons_tmp=[];
function getClosestBubble(id,LinkableId)
{
	$(".bubble").removeClass("linkable");
	var DistMini=130;
	var idMini=-1;
	//recuperation de toutes les bulles linkable avec la bulle id.
	for(var b in LinkableId)
	{
		idLinkATester=LinkableId[b]
		if(idLinkATester!=id){var D=distance(bubbles[idLinkATester],bubbles[id]);
			if(D<DistMini){DistMini=D;idMini=idLinkATester;} 
		}
	}
	if(idMini!=-1)
	{
		$('#liaison_tmp').show();
		//console.log('création de liaisons_'+idMini+' '+id);
		//La bulle est près d'une autre, on peut mettre une liaison entre les deux.
		$(".bubble").removeClass("linkable");
		$("#bubble_"+idMini).addClass("linkable");
		$("#bubble_"+id).addClass("linkable");
		liaisons_tmp=[parseInt(idMini),parseInt(id)];
		deplacement_liaison_tmp(parseInt(idMini),parseInt(id));
	}
	else
	{
		liaisons_tmp=[];
		$('#liaison_tmp').hide();
	}
}

function unselect_enigma()
{$('.bubble').removeClass('bubble_selected');
//Deselection des enigmes.
$('#parametre_enigme').slideUp();}




