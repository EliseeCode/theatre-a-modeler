<?php
?>
<script>
$(".colDroiteDeck").append(`
  <div class="tileDroite whiteTile" id="progressTile" style="order:-1;">
  <h3 style="padding-top:20px;">
    <span class="xp_lvl_deck"></span>
    <span style="float:right;color:var(--mycolor2);cursor:hand;text-transform:uppercase;"  onclick="windowClass('myStats');return false;"><?php echo __("Plus");?></span>
  </h3>
  <div style="display:flex">
    <div class="avatarContainer" style="width:150px;">
      <div class="avatar avatarID scaleOver" onclick="windowClass('avatar');"></div><!--changed by -->
      <?php if($_SESSION["premiumAvatar"]){echo "<div>*</div>";}?>
    </div>
    <div style="width:300px;">
      <div style="color:grey;">Gagne <span class="xp_max_deck"></span>xp</div>
      <div id="XPbarContainer" class="progressbar glitter" style="position:relative;margin:0;width:100%;margin:10px 0;border-radius:12px;">
        <div class="XPbar progressbar_fluid" style="border-radius:12px;"></div>
      </div>
      <span class="xpBilan"></span>
    </div>
  </div>
  <div style="color:grey;"><?php echo __("Votre fortune est de ");?><span class="nbreRuby"></span><span class="ruby ruby_inline"></span></div>
</div>`);

$(".avatarID").html(`
	<img src='avatar/avatar_`+avatar_id+`.png' class='avatar_img avatar_XL'>
`);


$.getJSON("ajax.php?action=getRubyAndXp", function(data)
{
$(".nbreRuby").html(data.nbreRuby);
updateXp(data.xp);
});
</script>
