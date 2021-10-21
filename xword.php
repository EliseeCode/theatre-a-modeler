

<script>
//---------------------------------//
//   GLOBAL VARIABLES              //
//---------------------------------//

var board, wordArr, wordBank, wordsActive, mode;

var Bounds = {
  top:0, right:0, bottom:0, left:0,

  Update:function(x,y){
    this.top = Math.min(y,this.top);
    this.right = Math.max(x,this.right);
    this.bottom = Math.max(y,this.bottom);
    this.left = Math.min(x,this.left);
  },

  Clean:function(){
    this.top = 999;
    this.right = 0;
    this.bottom = 0;
    this.left = 999;
  }
};


//---------------------------------//
//   MAIN                          //
//---------------------------------//

function PlayXword(){
  var letterArr = document.getElementsByClassName('letter');

  for(var i = 0; i < letterArr.length; i++){
    letterArr[i].innerHTML = "<input class='char' type='text' maxlength='2'></input>";
  }
  for(k in wordsActive)
  {
    if($("#box_"+wordsActive[k].y+"_"+wordsActive[k].x+" .numeroX").length==0)
    {$("#box_"+wordsActive[k].y+"_"+wordsActive[k].x).append("<div class='numeroX'><span onmouseover='showClue("+k+");'>"+wordsActive[k].num+"</span></div>");}
    else {
    $("#box_"+wordsActive[k].y+"_"+wordsActive[k].x+" .numeroX").append("-<span onmouseover='showClue("+k+");'>"+wordsActive[k].num+"</span>");
    }
  }
  mode = 0;
  ToggleInputBoxes(false);
}
function showClue(k)
{
$(".clueClone").remove();
idClue=wordsActive[k].id
$("#clue_item_"+idClue).clone().addClass("clueClone").appendTo("#box_"+wordsActive[k].y+"_"+wordsActive[k].x+" .numeroX");
$(".clueClone .num_clue").remove();
}

function createXword(){
  for(i in selected_card)
  {id_a_travailler_restant[i]=selected_card[i];}
  if (mode === 0){
    ToggleInputBoxes(true);
    document.getElementById("crossword").innerHTML = BoardToHtml();
    mode = 1;
  }
  else{
    GetWordsFromInput();

    for(var i = 0, isSuccess=false; i < 10 && !isSuccess; i++){
      CleanVars();
      isSuccess = PopulateBoard();
    }

    document.getElementById("crossword").innerHTML =
      (isSuccess) ? BoardToHtml(" ") : "Failed to find crossword." ;
  }

  $('.letter').on("keyup",function(e){
    if(e.key=="Backspace"){console.log("MoveBack");checkForGoodLetter(this,-1);}
    if(e.keyCode==37){move(this,-1,0);}//left
    if(e.keyCode==38){move(this,0,-1);}//up
    if(e.keyCode==39){move(this,1,0);}//right
    if(e.keyCode==40){move(this,0,1);}//down
  })

  $('.letter').on("input",function(ev){

    if($(this).find('input').val().length==2){
    $(this).find('input').val($(this).find('input').val().substring(1,2));}
    if($(this).find('input').val().length==0){}
    else{checkForGoodLetter(this,1);}

  });
  $('.letter').on("click",function(){$(this).find('input').val("");dir="";})
}
var dir="";
var pilePosition=[];
function moveBack(that)
{newPos=pilePosition.pop();
  posXp=newPos[0];
  posYp=newPos[1];

  if($("#box_"+posYp+"_"+posXp+" .char").length==1)
  {$("#box_"+posYp+"_"+posXp+" .char").focus();}
}
function move(that,dirX,dirY)
{arr=that.id.split("_");
posY=parseInt(arr[1]);
posX=parseInt(arr[2]);
posXp=posX+parseInt(dirX);
posYp=posY+parseInt(dirY);
pilePosition.push([posXp,posYp]);
if($("#box_"+posYp+"_"+posXp+" .char").length==1)
{$("#box_"+posYp+"_"+posXp+" .char").focus();}
}
function checkForGoodLetter(that,direction)
{
  arr=that.id.split("_");
  posY=parseInt(arr[1]);
  posX=parseInt(arr[2]);
  posXp=posX+parseInt(direction);
  posYp=posY+parseInt(direction);
  //console.log(posX,posY,posXp,posYp);
  //if($("#box_"+posY+"_"+posX+" .char").val()!="")
  //{
    if(dir=="" && $("#box_"+posYp+"_"+posX+" .char").length==1){dir="down";}
    if(dir=="" && $("#box_"+posY+"_"+posXp+" .char").length==1){dir="right";}
    if(dir=="down"){
      if($("#box_"+posYp+"_"+posX+" .char").length==1)
      {$("#box_"+posYp+"_"+posX+" .char").focus();}
      else{dir="";}
    }
    if(dir=="right"){
      if($("#box_"+posY+"_"+posXp+" .char").length==1)
      {$("#box_"+posY+"_"+posXp+" .char").focus();}
      else{dir="";}
    }
    Words2check=wordsActive.filter(function(elem){return ((posX==elem.x && elem.dir==1)||(posY==elem.y && elem.dir==0));});
    for(var W in Words2check){
      checkCorrectness(Words2check[W]);
    }
  //}
}
function checkCorrectness(mot)
{correctFlag=true;
  var posX=parseInt(mot.x);
  var posY=parseInt(mot.y);
  for(var k in mot.char)
  {
    $("#box_"+posY+"_"+posX+" .char").addClass("mot_"+mot.id);
    //console.log(mot.char[k].toUpperCase(),$("#box_"+posY+"_"+posX+" .char").val().toUpperCase())
    if(mot.char[k].toUpperCase()!=$("#box_"+posY+"_"+posX+" .char").val().toUpperCase()){correctFlag=false;}
    if(mot.dir==0){posX++;}
    if(mot.dir==1){posY++;}
  }
  if(correctFlag){
    update_mots_restant();
    $(".mot_"+mot.id).addClass('goodLetter');
    $(".mot_"+mot.id).attr("readonly",true);
    $(".mot_"+mot.id).parent().off();
    $("#clue_item_"+mot.id).css("border","3px lime solid");
    removeElem(mot.id,id_a_travailler_restant);
    selected_card_done.push(card_id);
    $.getJSON("ajax.php?action=addActiviteGlobal&exo_id=2&card_id="+card_id+"&game="+game+"&correctness=1", function(result){});
    $.getJSON("ajax.php?action=cardLearned&exo_id=2&card_id="+card_id+"&puissance=30", function(result){
      //console.log(result);
      updateXp(result.nbreCoins);
      $('#XPcontainer').append('<div class="animatedXP" style="">+'+result.coins2add+'xp</div>');
      if(result.bankStatus=="limit"){$(".animatedXP").html("<span style='font-size:0.8;'><?php echo __("Limite quotidienne atteinte");?></span>");}
      play_audio_coin();
      $(".animatedXP").on("animationend",function(){$(this).remove();});
      //gestion durée
      // Create a new JavaScript Date object based on the timestamp
      // multiplied by 1000 so that the argument is in milliseconds, not seconds.
      Current_TimeStamp=Math.floor(Date.now() / 1000);
      Delta=result.nextOptimalRD-Current_TimeStamp;
      console.log(Delta,result.nextOptimalRD,Current_TimeStamp);
      DeltaY=Math.floor(Delta/(365*24*60*60));
      DeltaM=Math.floor((Delta-DeltaY*365*24*60*60)/(30*24*60*60));
      DeltaS=Math.floor((Delta-DeltaY*365*24*60*60-DeltaM*30*24*60*60)/(7*24*60*60));
      DeltaJ=Math.floor((Delta-DeltaY*365*24*60*60-DeltaM*30*24*60*60-DeltaS*7*24*60*60)/(24*60*60));
      DeltaText="";
      if(DeltaJ==1){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaJ+" <?php echo __("jour");?>";}
      if(DeltaJ>1){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaJ+" <?php echo __("jours");?>";}
      if(DeltaS==1){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaS+" <?php echo __("semaine");?>";}
      if(DeltaS>1){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaS+" <?php echo __("semaines");?>";}
      if(DeltaM!=0){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaM+" <?php echo __("mois");?>";}
      if(DeltaY!=0){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaY+" <?php echo __("ans");?>";}

      $("#duree").html("<img src='img/sablier.png' width='50px'>"+DeltaText);
      $("#duree").show();
    });

    if(id_a_travailler_restant.length==0){fin();}

  }
  else
  {
    $(".mot_"+mot.id).removeClass('goodLetter');
  }
}

function ToggleInputBoxes(active){
  var w=document.getElementsByClassName('word'),
      d=document.getElementsByClassName('clue');

  for(var i=0;i<w.length; i++){
    if(active===true){
      RemoveClass(w[i], 'hide');
      RemoveClass(d[i], 'clueReadOnly');
      d[i].disabled = '';
    }
    else{
      AddClass(w[i], 'hide');
      AddClass(d[i], 'clueReadOnly');
      d[i].disabled = 'readonly';
    }
  }
}


function GetWordsFromInput(){
wordArr=[];
$("#reponse").html("");
  for(k in selected_card){
    this_card_id=selected_card[k];
    mot=cardsById[this_card_id].mot.trim().toUpperCase();
    mot_trad=cardsById[this_card_id].mot_trad;
    hasImage=cardsById[this_card_id]["hasImage"];
    sentences=cardsById[this_card_id]["sentences"];
    sentence=rand_parmi(sentences);
    questionCloze='<input type="text" autocomplete="off" class="input_reponse" value="" class="">';
    repCloze=mot;
    if(sentence)
    {
      nbreEtoile=0;
      for(s in sentence)
       {if(sentence[s]=="*"){nbreEtoile++;}}
      if(nbreEtoile==1){sentence+="*";}
      if(nbreEtoile==0){sentence+="<br>*"+mot+"*";}
      repCloze=sentence.match(/\*(.*?)\*/)[0];
      repCloze=repCloze.replace('*','');
      repCloze=repCloze.replace('*','');
      questionCloze=sentence.replace("*"+repCloze+"*",'<input type="text" autocomplete="off" class="input_reponse" readonly value="" class="">',1);
    }
    wordArr.push({id:this_card_id,val:mot,num:k});
    $("#reponse").append("<div class='clue_item' id='clue_item_"+this_card_id+"'><span class='num_clue'>"+k+"</span><div class='img_card'></div></div>");
    if(mot_trad!=""){$("#clue_item_"+this_card_id+" > .img_card").append("<span class='mot_trad_card'>"+mot_trad+"</span>");}
    if(hasImage>0){$("#clue_item_"+this_card_id+" > .img_card").css("background-image","url(card_img/card_"+hasImage+".png)");}
    else {$("#clue_item_"+this_card_id+" > .img_card").css("background-image","url(img/default_card.png)");}
  }

  //for(var i=0,val,w=document.getElementsByClassName("word");i<w.length;i++){
  //  val = w[i].value.toUpperCase();
  //  if (val !== null && val.length > 1){wordArr.push({id:i,val:val});}
  //}
}


function CleanVars(){
  Bounds.Clean();
  wordBank = [];
  wordsActive = [];
  board = [];

  for(var i = 0; i < 64; i++){
    board.push([]);
    for(var j = 0; j < 64; j++){
      board[i].push(null);
    }
  }
}


function PopulateBoard(){
  PrepareBoard();

  for(var i=0,isOk=true,len=wordBank.length; i<len && isOk; i++){
    isOk = AddWordToBoard();
  }
  return isOk;
}


function PrepareBoard(){
  wordBank=[];

  for(var i = 0, len = wordArr.length; i < len; i++){
    wordBank.push(new WordObj(wordArr[i]));
  }

  for(i = 0; i < wordBank.length; i++){
    for(var j = 0, wA=wordBank[i]; j<wA.char.length; j++){
      for(var k = 0, cA=wA.char[j]; k<wordBank.length; k++){
        for(var l = 0,wB=wordBank[k]; k!==i && l<wB.char.length; l++){
          wA.totalMatches += (cA === wB.char[l])?1:0;
        }
      }
    }
  }
}


// TODO: Clean this guy up
function AddWordToBoard(){
  var i, len, curIndex, curWord, curChar, curMatch, testWord, testChar,
      minMatchDiff = 9999, curMatchDiff;

  if(wordsActive.length < 1){
    //la premiere fois que j'applique AddWordToBoard, je cherche celui avec le moins de Match.
    curIndex = 0;
    for(i = 0, len = wordBank.length; i < len; i++){
      if (wordBank[i].totalMatches < wordBank[curIndex].totalMatches){
        curIndex = i;
      }
    }
    wordBank[curIndex].successfulMatches = [{x:12,y:12,dir:0}];
  }
  else{//on a déja des mots sur le plateau
    curIndex = -1;

    for(i = 0, len = wordBank.length; i < len; i++){
      curWord = wordBank[i];
      curWord.effectiveMatches = 0;
      curWord.successfulMatches = [];
      for(var j = 0, lenJ = curWord.char.length; j < lenJ; j++){
        curChar = curWord.char[j];
        for (var k = 0, lenK = wordsActive.length; k < lenK; k++){
          testWord = wordsActive[k];
          for (var l = 0, lenL = testWord.char.length; l < lenL; l++){
            testChar = testWord.char[l];
            if (curChar === testChar){
              curWord.effectiveMatches++;

              var curCross = {x:testWord.x,y:testWord.y,dir:0};
              if(testWord.dir === 0){
                curCross.dir = 1;
                curCross.x += l;
                curCross.y -= j;
              }
              else{
                curCross.dir = 0;
                curCross.y += l;
                curCross.x -= j;
              }

              var isMatch = true;

              for(var m = -1, lenM = curWord.char.length + 1; m < lenM; m++){
                var crossVal = [];
                if (m !== j){
                  if (curCross.dir === 0){
                    var xIndex = curCross.x + m;

                    if (xIndex < 0 || xIndex > board.length){
                      isMatch = false;
                      break;
                    }

                    crossVal.push(board[xIndex][curCross.y]);
                    crossVal.push(board[xIndex][curCross.y + 1]);
                    crossVal.push(board[xIndex][curCross.y - 1]);
                  }
                  else{
                    var yIndex = curCross.y + m;

                    if (yIndex < 0 || yIndex > board[curCross.x].length){
                      isMatch = false;
                      break;
                    }

                    crossVal.push(board[curCross.x][yIndex]);
                    crossVal.push(board[curCross.x + 1][yIndex]);
                    crossVal.push(board[curCross.x - 1][yIndex]);
                  }

                  if(m > -1 && m < lenM-1){
                    if (crossVal[0] !== curWord.char[m]){
                      if (crossVal[0] !== null){
                        isMatch = false;
                        break;
                      }
                      else if (crossVal[1] !== null){
                        isMatch = false;
                        break;
                      }
                      else if (crossVal[2] !== null){
                        isMatch = false;
                        break;
                      }
                    }
                  }
                  else if (crossVal[0] !== null){
                    isMatch = false;
                    break;
                  }
                }
              }

              if (isMatch === true){
                curWord.successfulMatches.push(curCross);
              }
            }
          }
        }
      }

      curMatchDiff = curWord.totalMatches - curWord.effectiveMatches;

      if (curMatchDiff<minMatchDiff && curWord.successfulMatches.length>0){
        curMatchDiff = minMatchDiff;
        curIndex = i;
      }
      else if (curMatchDiff <= 0){
        return false;
      }
    }
  }

  if (curIndex === -1){
    return false;
  }

  var spliced = wordBank.splice(curIndex, 1);
  wordsActive.push(spliced[0]);

  var pushIndex = wordsActive.length - 1,
      rand = Math.random(),
      matchArr = wordsActive[pushIndex].successfulMatches,
      matchIndex = Math.floor(rand * matchArr.length),
      matchData = matchArr[matchIndex];

  wordsActive[pushIndex].x = matchData.x;
  wordsActive[pushIndex].y = matchData.y;
  wordsActive[pushIndex].dir = matchData.dir;

  for(i = 0, len = wordsActive[pushIndex].char.length; i < len; i++){
    var xIndex = matchData.x,
        yIndex = matchData.y;

    if (matchData.dir === 0){
      xIndex += i;
      board[xIndex][yIndex] = wordsActive[pushIndex].char[i];
    }
    else{
      yIndex += i;
      board[xIndex][yIndex] = wordsActive[pushIndex].char[i];
    }

    Bounds.Update(xIndex,yIndex);
  }

  return true;
}


function BoardToHtml(blank){
  for(var i=Bounds.top-1, str=""; i<Bounds.bottom+2; i++){
    str+="<div class='row'>";
    for(var j=Bounds.left-1; j<Bounds.right+2; j++){
      str += BoardCharToElement(board[j][i],i,j);
    }
    str += "</div>";
  }
  return str;
}


function BoardCharToElement(c,i,j){
  var arr=(c)?['square','letter']:['square'];
  return EleStr('div',[{a:'class',v:arr}],c,i,j);
}



//---------------------------------//
//   OBJECT DEFINITIONS            //
//---------------------------------//

function WordObj(stringValue){
  this.id = stringValue.id;
  this.num = stringValue.num;
  this.string = stringValue.val;
  this.char = stringValue.val.split("");
  this.totalMatches = 0;
  this.effectiveMatches = 0;
  this.successfulMatches = [];
}


//---------------------------------//
//   HELPER FUNCTIONS              //
//---------------------------------//

function EleStr(e,c,h,x,y){
  h = (h)?h:"";
  for(var i=0,s="<"+e+" "; i<c.length; i++){
    s+=c[i].a+ "='"+ArrayToString(c[i].v," ")+"' id='box_"+x+"_"+y+"' ";
  }
  return (s+">"+h+"</"+e+">");
}

function ArrayToString(a,s){
  if(a===null||a.length<1)return "";
  if(s===null)s=",";
  for(var r=a[0],i=1;i<a.length;i++){r+=s+a[i];}
  return r;
}

function AddClass(ele,classStr){
  ele.className = ele.className.replaceAll(' '+classStr,'')+' '+classStr;
}

function RemoveClass(ele,classStr){
  ele.className = ele.className.replaceAll(' '+classStr,'');
}

function ToggleClass(ele,classStr){
  var str = ele.className.replaceAll(' '+classStr,'');
  ele.className = (str.length===ele.className.length)?str+' '+classStr:str;
}

String.prototype.replaceAll = function (replaceThis, withThis) {
   var re = new RegExp(replaceThis,"g");
   return this.replace(re, withThis);
};


</script>
