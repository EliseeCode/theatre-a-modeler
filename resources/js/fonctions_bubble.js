
//cleanArray 		removes all duplicated elements
//distance			donne la distance entre deux bubbles
//décalage_angualaire donne la position d'un nouveau bubble en fonction de son bubble d'attache, de celui qui est avant celui-ci et d'un angle
//animate 			est repeté toute les 30ms et calcul les nouvelles positions des spheres en fonctions des forces qu'elle exerce entre elles
//init_bubble 		initialise les parametres (taille canvas et viscolité) et crée le premier bubble.
//add_bubble 		ajoute les bubble correspondant aux liaisons en respectanat une apparition correct des bubbles lors de leur création
//clic_bubble(id)	ajoute un bubble

function distance(bubble1,bubble2)
{
var distance12=0;
var distance12=Math.sqrt(Math.pow(bubble1.x-bubble2.x,2)+Math.pow(bubble1.y-bubble2.y,2));
return distance12;
}

function decalage_angulaire(bubble1,bubble2,alpha,l)
{
var dista=distance(bubble1,bubble2);
var cosalpha=Math.cos(alpha);
var sinalpha=Math.sin(alpha);
var cosbeta=(bubble1.x-bubble2.x)/dista;
var sinbeta=(bubble1.y-bubble2.y)/dista;
var X=bubble2.x+l*(cosalpha*cosbeta-sinalpha*sinbeta);
var Y=bubble2.y+l*(sinalpha*cosbeta+cosalpha*sinbeta);
var position=[X,Y];
return position;
}

function deplacement_bubble(div_id,X,Y)
{
var madiv=document.getElementById(div_id);
	madiv.style.left=X+'px';
	madiv.style.top=Y+'px';
}
function deplacement_liaison(idA,idB)
{
	madiv=document.getElementById("liaison_"+idA+"_"+idB);
	xLiaisons=bubbles[idA].x;
	madiv.style.left=xLiaisons+'px';
	yLiaisons=bubbles[idA].y-7;
	madiv.style.top=yLiaisons+'px';
	dista=distance(bubbles[idA],bubbles[idB]);
	longLiaisons=dista-10;
    madiv.style.width=longLiaisons+"px";
	cosbeta=(bubbles[idB].x-bubbles[idA].x)/dista;
    sinbeta=(bubbles[idB].y-bubbles[idA].y)/dista;
	if(sinbeta<0)
	{Beta=-(Math.acos(cosbeta)*180/Math.PI);}
	else
	{Beta=(Math.acos(cosbeta)*180/Math.PI);}	
	madiv.style.transform="rotate("+Beta+"deg)";
}
function deplacement_liaison_tmp(idA,idB)
{
	madiv=document.getElementById("liaison_tmp");
	xLiaisons=bubbles[idA].x;
	madiv.style.left=xLiaisons+'px';
	yLiaisons=bubbles[idA].y-8;
	madiv.style.top=yLiaisons+'px';
	dista=distance(bubbles[idA],bubbles[idB]);
	longLiaisons=dista-10;
    madiv.style.width=longLiaisons+"px";
	cosbeta=(bubbles[idB].x-bubbles[idA].x)/dista;
    sinbeta=(bubbles[idB].y-bubbles[idA].y)/dista;
	if(sinbeta<0)
	{Beta=-(Math.acos(cosbeta)*180/Math.PI);}
	else
	{Beta=(Math.acos(cosbeta)*180/Math.PI);}	
	madiv.style.transform="rotate("+Beta+"deg)";
}

function animate()
    {
		var tmpXmin=xMax*3/8;
		var tmpXmax=xMax*5/8;
		//J'ISOLE la bubbles i POUR TROUVER SON DEPLACEMENT
		for (i in bubbles)//pour tous les bubbles on va calculer xapres et yapres
		{
		if(!bubbles[i].fixe)//on bouge pas les bulles fixe, start et end 
		{

			//INIT DES FORCES LIEES A i
			var FxRessort=0;
			var FyRessort=0;
			var FxRepuls=0;
			var FyRepuls=0;
			
			//FORCE REPULSION ENTRE bubble i et bubble j
			
			for (j in bubbles)
			{
				
				var dist=Math.sqrt(Math.pow(bubbles[j].x-bubbles[i].x,2)+Math.pow(bubbles[j].y-bubbles[i].y,2));
				FxRepuls += 10000*bubbles[i].charge*bubbles[j].charge/Math.pow(dist+0.001,3)*(bubbles[i].x-bubbles[j].x);
				FyRepuls += 10000*bubbles[i].charge*bubbles[j].charge/Math.pow(dist+0.001,3)*(bubbles[i].y-bubbles[j].y);
			}
			//CALCUL DES FORCES DES RESSORTS LIEES A i
			for(var k in bubbles[i].liaisonprec)
			{
				bubblelie=bubbles[i].liaisonprec[k];// bulle liée a bulle i
				var dist=distance(bubbles[bubblelie],bubbles[i]);
				FxRessort += -bubbles[i].raideur*bubbles[bubblelie].raideur*(dist-(bubbles[i].longueur+bubbles[bubblelie].longueur)/2)*(bubbles[i].x-bubbles[bubblelie].x)/(dist+0.1);
				FyRessort += -bubbles[i].raideur*bubbles[bubblelie].raideur*(dist-(bubbles[i].longueur+bubbles[bubblelie].longueur)/2)*(bubbles[i].y-bubbles[bubblelie].y)/(dist+0.1);
			}
			for(var k in bubbles[i].liaisonsuiv)
			{
				bubblelie=bubbles[i].liaisonsuiv[k];
				var dist=distance(bubbles[bubblelie],bubbles[i]);
				FxRessort += -bubbles[i].raideur*bubbles[bubblelie].raideur*(dist-(bubbles[i].longueur+bubbles[bubblelie].longueur)/2)*(bubbles[i].x-bubbles[bubblelie].x)/(dist+0.1);
				FyRessort += -bubbles[i].raideur*bubbles[bubblelie].raideur*(dist-(bubbles[i].longueur+bubbles[bubblelie].longueur)/2)*(bubbles[i].y-bubbles[bubblelie].y)/(dist+0.1);
			}
			//Calcul de la nouvelle position du bubble[i]	
			
			x2apres=((FxRessort+FxRepuls)*Dt2 + M*bubbles[i].x*Dt + bubbles[i].masse*(2*bubbles[i].x-bubbles[i].xavant) )/(bubbles[i].masse+M*Dt);
			
			y2apres=((FyRessort+FyRepuls)*Dt2 + M*bubbles[i].y*Dt + bubbles[i].masse*(2*bubbles[i].y-bubbles[i].yavant) )/(bubbles[i].masse+M*Dt);
			
			// Blocage a l'interieur de la fenetre
			if (x2apres<R+50)
				{x2apres=R+50;}
			if (y2apres<R+50)
				{y2apres=R+50;}
			if (x2apres>xMax-R-50)
				{x2apres=xMax-R-51;}
			if (y2apres>yMax-R-50)
				{y2apres=yMax-R-51;}
			//Blocage des bulles Yfixe sur l'axe:
			
			tmpxavant[i]=bubbles[i].x;
			tmpyavant[i]=bubbles[i].y;
			tmpx[i]=x2apres;
			tmpy[i]=y2apres;
			tmpXmax=Math.max(tmpXmax,x2apres)
			tmpXmin=Math.min(tmpXmin,x2apres)
		}
		}
			//Blocage des bulles 0 et 100 aux extrémité.
			tmpx[0]=tmpXmin;
			tmpx[100]=tmpXmax;
			tmpy[0]=yMax/2;
			tmpy[100]=yMax/2;
		//bascule dans le temps de t vers t+Dt pour la position de tous les bubbles
		for(k in bubbles)
		{
			if(!bubbles[k].fixe)
			{
				bubbles[k].xavant=tmpxavant[k];
				bubbles[k].yavant=tmpyavant[k];
				bubbles[k].x=tmpx[k];
				bubbles[k].y=tmpy[k];
			}	
		}
		
		
		for(var k in bubbles)//pour chaque bulle
		{
			for(var l in bubbles[k].liaisonprec)//pour chaque liaison précédente
			{
			deplacement_liaison(bubbles[k].liaisonprec[l],k);	
			}
		}
		
		//Déplacement des DIVs
		for(var k in bubbles)
		{
			deplacement_bubble('bubble_'+bubbles[k].id,bubbles[k].x-R,bubbles[k].y-R);					
		}		
    } 

var myAnimation=false;
var liaisons=[];
function init()
{	
	//CONSTANTES DE MODELISATION 
	Dt=0.1;
	Dt2=Math.pow(Dt,2);
	M=200;
	vitesse_anim=70;
	
	tmpxavant = new Array ();
	tmpyavant = new Array ();
	tmpx = new Array ();
	tmpy = new Array ();
	
	//rayon des bulles
	R=20;
	//taille de la simulation
	
	
	//xMax = (window.innerWidth);
	yMax = (window.innerHeight)/2;
    div_zone=document.getElementById('zone_bubble');
	xMax=$("#zone_bubble").innerWidth();
	//console.log()
	div_zone.style.height=yMax+'px';
	//div_zone.style.width=xMax+'px';
	
	//CONSTRUCTION DU PREMIER bubble
	bubbles  = new Array ();
	liaisons=[];
	//liaisons=[[0, 1],[1, 2],[2, 3],[3, 4],[4, 5],[5, 6],[6, 7],[7, 8],[8, 9]];
	//console.log(liaisons);
	//0 est l'id de départ	
    //bubble(id,name,typenum,liaisonprec,liaisonsuiv,x,y)
	bubbles[0]= new bubble(0,"Départ",1,[],[1],xMax/10,yMax/2);
	$('#zone_bubble').append("<div class='liaison' id='liaison_tmp'></div>");
	//preAnimation();
	}

	var firstAnimation=true;
	
function preAnimation()
{	
$('#liaison_tmp').hide();
//récupère la listes des bulles dans les liaisons et des "bubbles" déjà existante
bubblesIds=getBubblesIds();
//créé les bubbles qui manquent dans bubblesIds
addBubble();
//Calcul les distances du point de départ (ne sert pas a grand chose pour le moment)
dists=calculDist(bubblesIds);
validationStructure(dists);
//liaisons=ordonner(liaisons,dists);
liaisons=cleanArray(liaisons);
//liaisons=addFinalBubbles(bubblesIds);

addLiaisons(liaisons);
dragclick_evt();

if(firstAnimation){firstAnimation=false;for(k=0;k<150;k++){animate();}}
for(k=0;k<10;k++){animate();}
Anim.start();
myAnimationTimeout=setTimeout(function(){Anim.stop();},2000);

}
	
function animation(){
	this.AnimId=false;
	this.start = function()
{
	if(!this.AnimId){this.AnimId = setInterval(animate, vitesse_anim);	}
}
this.stop = function()
{   if(this.AnimId){clearInterval(this.AnimId);this.AnimId = false;}}
}
Anim=new animation();

function getLinkableId(id)
{
	console.log('GetLink'+id);
	
	var linkableId=[];
	for(var b in bubbles)
	{linkableId.push(parseInt(b));}
	for(var b in bubbles)
	{
		for(l in liaisons)
		{
			if((liaisons[l][1]==b && liaisons[l][0]==id)||(liaisons[l][0]==b && liaisons[l][1]==id))
			{suppr_elem(parseInt(b),linkableId);}
		}
	}
	suppr_elem(parseInt(id),linkableId);
	suppr_elem(100,linkableId);
	console.log(linkableId);
return linkableId;
}


function addFinalBubbles(bubblesIds)
{
//var finalNode=[];
//var beginNode=[];
//je remplis finalNode avec tous les noeuds
//for(n in bubblesIds)	
//{finalNode[n]=bubblesIds[n];
//beginNode[n]=bubblesIds[n]}

//J'enleve tous les noeuds qui ont des liaisons VERS un autre.

//for(l in liaisons)
//{suppr_elem(liaisons[l][0],finalNode);
//suppr_elem(liaisons[l][1],beginNode);
//}
//suppr_elem(0,beginNode);
//suppr_elem(100,finalNode);

//J'ajoute le noeud final
//if(!bubbles[100])	
//{bubbles[100]=new bubble(100,"Fin",2,[],[],xMax*9/10,yMax/2);}
//J'ajoute toutes mes liaisons finales avec le noeud final
//for(f in finalNode)
//{liaisons.push([finalNode[f],100]);}
//for(b in beginNode)
//{liaisons.push([0,beginNode[b]]);}
//$(".bubble").removeClass("finalBubble");
//for(b in finalNode)
//{
//$("#bubble_"+finalNode[b]).addClass("finalBubble");
//}


return liaisons;	
}

function addBubble()
{
	for(b in bubblesIds){
		//console.log(typeof bubbles[bubblesIds[b]]+" pour la bulle"+bubblesIds[b]);
		if (typeof bubbles[bubblesIds[b]] == "undefined") {
		bubbles[bubblesIds[b]]=new bubble(bubblesIds[b],"Nouvelle énigme",0,[],[],xMax/2+200*Math.random(),yMax/2+200*Math.random()-100);
		}
	}
}
function addLiaisons(liaisons)
{
	//console.log(liaisons);
	mazone=document.getElementById('zone_bubble');
	for(n in liaisons){
		liaP=liaisons[n][0];
		liaS=liaisons[n][1];
		if(bubbles[liaS].liaisonprec.indexOf(liaP)==-1){bubbles[liaS].liaisonprec.push(liaP);};
		if(bubbles[liaP].liaisonsuiv.indexOf(liaS)==-1){bubbles[liaP].liaisonsuiv.push(liaS);};	
		if($("#liaison_"+liaP+"_"+liaS).length==0)
		{
		mazone.innerHTML+="<div class='liaison' id='liaison_"+liaP+"_"+liaS+"' onclick='suppr_liaison("+liaP+","+liaS+");saveGame();preAnimation();'></div>";
		}
	}
	
}

function suppr_liaison(idA,idB)
{
	//console.log("suppression liaison "+idA+" "+idB);
	suppr_elem(idB,bubbles[idA].liaisonsuiv);
	suppr_elem(idA,bubbles[idB].liaisonprec);
	//suppr_elem([idA,idB],liaisons);
	for(l in liaisons){if(liaisons[l][0]==idA && liaisons[l][1]==idB){liaisons.splice(l,1);}}
	$("#liaison_"+idA+"_"+idB).remove();
}
	
function getBubblesIds()
{
//Je supprime toutes les anciennes liaisons finals
/*for(l=liaisons.length-1;l>=0;l--)
{
	if(liaisons[l][1]==100){suppr_liaison(liaisons[l][0],100);}
}
for(l=liaisons.length-1;l>=0;l--)
{
	if(liaisons[l][0]==100){suppr_liaison(100,liaisons[l][1]);}
}*/
//console.log("liaisons sans les liaisons finals");
//console.log(liaisons);
	bubblesIds=[];
	for(var l in liaisons)
	{elem=liaisons[l];
		if(bubblesIds.indexOf(elem[0]) == -1){
		bubblesIds.push(elem[0]);}
		if(bubblesIds.indexOf(elem[1]) == -1){
		bubblesIds.push(elem[1]);}
	}
	for(b in bubbles)
	{
	id=parseInt(b);
	if(bubblesIds.indexOf(id)==-1){bubblesIds.push(id);}
	}
	

//Je supprime la bulle de fin
//suppr_elem(100,bubblesIds);

//console.log('bubblesIds');	
//console.log(bubblesIds);	
return bubblesIds;
}	
	
function calculDist(bubblesIds)
{
	dists=[];
	//je remplis dists avec des 100,
	for(k=0;k<bubblesIds.length;k++)
	{dists[bubblesIds[k]]=100;}
	//j'initialise la distance au point de départ égal à 0.
	dists[0]=0;
	for(var i in liaisons)
	{
		for(var j in liaisons)
		{
		lia=liaisons[j];
		dists[lia[0]]=Math.min(dists[lia[0]],dists[lia[1]]+1);
		dists[lia[1]]=Math.min(dists[lia[1]],dists[lia[0]]+1);
		}	
	}
	
return dists
}

function validationStructure(dists)
{
	//console.log("validation structure");
	 bubblesReachable=[];
	 bubblesPrec=[];
	 for(var i in bubblesIds)
	 {
	 id=bubblesIds[i];
	 bubblesReachable[id]=false;
	 bubblesPrec[id]=[];
	 }
	 bubblesReachable[0]=true;
	 //console.log('liaisons');
	 //console.log(liaisons);
	 for(var i in liaisons)
	 {bubblesPrec[liaisons[i][1]].push(liaisons[i][0]);}
	//console.log('bubblesPrec');
	//console.log(bubblesPrec);
	var len=-1;
	var newLen=0;
	//console.log('bubblesReachable');
	while(newLen!=len)
	{
	 len=newLen;
	 console.log(bubblesReachable);
	 for(var rang in bubblesIds)
	 {
	 idATester=bubblesIds[rang];
	 if(bubblesPrec[idATester].length!=0){
		reachable=true;
		for(var i in bubblesPrec[idATester])
		{
		IdBubblePrec=bubblesPrec[idATester][i];
		if(bubblesReachable[IdBubblePrec]==false){reachable=false;}
		}
		if(reachable){bubblesReachable[idATester]=true;}
		}		
	 }
	 newLen = bubblesReachable.filter(reach => reach==true).length;
	 
	 //console.log("len"+len+" Newlen"+newLen);
	}
	
	//console.log('bubblesReachable apres');
	//console.log(bubblesReachable);
	
	
	isolatedBubbles=[];
	$('.bubble').removeClass("bubble_reachable");
	$('.bubble').removeClass("problem");
	
	for(var k in bubblesIds)
	{
		if(!bubblesReachable[bubblesIds[k]])
		{
			isolatedBubbles.push(bubblesIds[k]);
			$("#bubble_"+bubblesIds[k]).addClass('problem');
		}
		else
		{
			//$("#bubble_"+bubblesIds[k]).addClass('reachable');
		}	
	}
	if(isolatedBubbles.length>0){
		if(isolatedBubbles.length==1){
			message="L'énigme numéro "+isolatedBubbles.join(',')+" ne pourra pas être jouée. Veuillez revoir l'enchainement des énigmes.";
		}
		else
		{
		message="Les énigmes numéros "+isolatedBubbles.join(',')+" ne pourront pas être jouées. Veuillez revoir l'enchainement des énigmes.";	
		}	
		$("#message_alert").html("<div class='alert alert-danger'>"+message+"</div>");
		$('#zone_bubble').addClass("problem");
		console.log('isolatedBubbles');
		console.log(isolatedBubbles);
	}
	else
	{
	$("#message_alert").html("");
	$('#zone_bubble').removeClass("problem");
	}
}

function ordonner(liaisons,Dist)
{
	for(var n in liaisons)
	{
		if(Dist[liaisons[n][0]]>Dist[liaisons[n][1]])
		{
			var tmp=liaisons[n][1];
			liaisons[n][1]=liaisons[n][0];
			liaisons[n][0]=tmp;
		}
	}
	
return liaisons;
}



//===========FONCTIONS UTILITAIRES=========
	
function suppr_elem(elem,liste)
{
var index = liste.indexOf(elem);
if(index > -1){
liste.splice(index,1);
}
}

function cleanArray(array) {
  var i, j, len = array.length, out = [];
  for (i = 0; i < len; i++) {
    if(out.indexOf(array[i])==-1){out.push(array[i]);}
  }
  return out;
}

function pauseEvent(e){
    if(e.stopPropagation) e.stopPropagation();
    if(e.preventDefault) e.preventDefault();
    e.cancelBubble=true;
    e.returnValue=false;
    return false;
}



