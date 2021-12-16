
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



