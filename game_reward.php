<script>
function showReward(trophyNameArray)
{
  $(".trophy").hide();
  $(".victory")[0].play();
  $(".rewardpage").show();
  $(".rewardpage").off();
  $(".rewardpage").on("click",function(){$(".rewardpage").hide();});
  for(k in trophyNameArray)
  {
  $("#trophy_"+trophyNameArray[k]).show();
  }
}

</script>
