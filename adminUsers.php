<?php

include_once ("db.php");
session_start();
$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
//$_SESSION['url']=end($explodeURI);
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
$user_id = $_SESSION['user_id'];
//Uniquement reservé à Elisée SUperAdmin
$sql="SELECT COUNT(*) as flagLicence FROM user_licence
LEFT JOIN licences ON user_licence.licence_id=licences.licence_id WHERE user_licence.user_id=".$user_id." AND licences.active=1 AND licences.date_fin>CURRENT_TIMESTAMP AND licence_type='superAdmin' LIMIT 1";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
if($row["flagLicence"]==0){header("location:index.php");exit();}
$result->close();
// if ($_SERVER['REQUEST_METHOD'] == 'POST')
//   {
//     if(isset($_POST["user_id"])){
//       $user_id=(int)$_POST["user_id"];
//       }
//   }
$sql="SELECT * FROM `lang` WHERE 1";
$result = $mysqli->query($sql);
$dataLang=array();
while($row = $result->fetch_assoc())
{array_push($dataLang,$row);}
$result->close();
echo "<script>dataLang=".json_encode($dataLang).";</script>";
echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
//if($type!="prof"){header("location:decks.php");exit();}
$_SESSION['url']="";

?>

<!DOCTYPE html>
<html >
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADMIN</title>
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <link rel="stylesheet" type="text/css" href="css/DataTables.min.css"/>
    <!-- Bootstrap -->
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
	  <link href="css/deck.css?ver=<?php echo filemtime('css/deck.css');?>" rel="stylesheet">
	  <link href="css/card.css?ver=<?php echo filemtime('css/card.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
    <link href="css/navStyle.css?ver=<?php echo filemtime('css/navStyle.css');?>" rel="stylesheet">
    <script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/Moment.js"></script>
		<script src="js/charts.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/DataTables.min.js"></script>
		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
      td.details-control {
        background: url('img/details_open.png') no-repeat center center;
        cursor: pointer;
      }
      tr.shown td.details-control {
        background: url('img/details_close.png') no-repeat center center;

      }
      td.checkBox-control {
        background: url('img/emptyCheckBox.png') no-repeat center center;
        cursor: pointer;
        background-size:20px 20px;
      }
      tr.selectedRow td.checkBox-control {
        background: url('img/check4.png') no-repeat center center;
        background-size:20px 20px;
      }
      .disableLicence{background-color:#ff3060;}
      .licencesTable,.classesTable{border:1px solid grey;margin:auto;border-radius:5px;}
      .infoTextarea{width:80%;height:200px;}
      .takeControlBtn{background-color:#6060FF;color:white;margin:10px;padding:10px;cursor:pointer;}
    </style>
</head>
<body class="fond">
  <?php include "entete.php";?>

	<div style="text-align:center;padding-top:100px;" class="bodyContent">
    <h3>Gestionnaire des licences</h3>

    <div style="width:80%;margin:auto;">
    <table id="userTable" class="stripe hover" style="width:100%;box-shadow:0 0 5px grey;">
      <thead>
            <tr>
                <th>Detail</th>
                <th>Select<br><input type="checkbox" value=1 class="checkAllVisible" onchange="selectAllVisible();"></th>
                <th>userID</th>
                <th>inscription</th>
                <th>prénom</th>
                <th>nom</th>
                <th>email</th>
                <th>Role</th>
                <th>lang</th>
                <th>notification</th>

            </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
            <tr>
              <th></th>
              <th></th>
              <th>userID</th>
              <th><input class="inputFilter" type="text" placeholder="Jour" /></th>
              <th><input class="inputFilter" type="text" placeholder="Prénom" /></th>
              <th><input class="inputFilter" type="text" placeholder="Nom" /></th>
              <th><input class="inputFilter" type="text" placeholder="Mail" /></th>
              <th>Role</th>
              <th>lang</th>
              <th>notification</th>
            </tr>
        </tfoot>
    </table>
    <textarea class="infoTextarea"></textarea>
  </div>
</div>
<script>
$('.desktop').menuBreaker();
function format ( d ) {
    return `<div id="container_user_`+d.user_id+`"></div>`;
}
$.getJSON("ajax_admin.php?action=getUsers", function(result)
{console.log(result);});

$(document).ready(function() {

    table=$('#userTable').DataTable( {
        "ajax": "ajax_admin.php?action=getUsers",
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            {
                "className":      'checkBox-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "name": "user_id","data": "user_id" },
            { "name": "engine","data": "jourInscription" },
            { "name": "engine","data": "first_name" },
            { "name": "engine","data": "last_name" },
            { "name": "engine","data": "email" },
            { "name": "engine","data": "role" },
            { "name": "engine","data": "lang" },
            { "name": "engine","data": "notification" }
        ],
        initComplete: function () {
            this.api().columns([7,8,9]).every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );
    $('#userTable tbody').on('click', 'td.checkBox-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
        if ( tr.hasClass("selectedRow")){
            tr.removeClass('selectedRow');
        }
        else {
            user_id=row.data().user_id;
            tr.addClass('selectedRow');
          }
        updateMailList();
      });
    $('#userTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            user_id=row.data().user_id;

            row.child( format(row.data())).show();
            $("#container_user_"+user_id).html(`
              <h3>Licences</h3>
              <table class="licencesTable">
                    <tr>
                       <td>
                         <select name="licence_category" onchange="changeCategory(`+user_id+`)" class="licence_category_input">
                           <option value="custom" selected>custom</option>
                           <option value="trans_deck">Traducteur deck</option>
                           <option value="trans_interface">Traducteur interface</option>
                         </select>
                         <input type="text" name="licence_type" class="licence_type_input" value="" placeholder="licenceType">
                         <select style="display:none;" name="lang" onchange="" class="lang_input"></select>
                       </td>
                       <td>
                       <select name="date_fin" class="nbreJour_input">
                         <option value="7">Une semaine</option>
                         <option value="31">Un mois</option>
                         <option value="360" selected>Une ans</option>
                         <option value="36000">sans fin</option>
                       </select>
                       </td>
                       <td><button onclick="sendAddLicence(`+user_id+`)">Ajouter</button></td>
                   </tr>
              </table>
              <h3>Classes</h3>
              <table class="classesTable">
              </table>
              <button class="takeControlBtn" onclick="takeControl(`+user_id+`);">Prendre le controle</button>`);
              //list all Licences
              for(k in row.data().licences){
                licence_id=row.data().licences[k].licence_id;
                licence_type=row.data().licences[k].licence_type;
                lang_id=row.data().licences[k].lang_id;
                date_ini=row.data().licences[k].date_ini;
                date_ini=moment(date_ini,"YYYY-MM-DD h:mm:ss a").format("YYYY-MM-DD");
                date_fin=row.data().licences[k].date_fin;
                date_fin=moment(date_fin,"YYYY-MM-DD h:mm:ss a").fromNow("YYYY-MM-DD");
                active=row.data().licences[k].active;
                if(licence_id!=null){
                  $("#container_user_"+user_id).find(".licencesTable").append(
                    `<tr class="licence_`+licence_id+`">
                      <td><span class="tinyFlag flag_`+code2FromLang_id(lang_id)+`"></span>`+licence_type+`</td>
                      <td>`+date_fin+`</td>
                      <td><button class="disableLicence" onclick="DisableLicence(`+licence_id+`);">Désactiver</button></td>
                    </tr>`
                  );
                  if(active==0){$("#container_user_"+user_id).find(".licencesTable").find(".licence_"+licence_id).hide();}
                }
              }
              //list all classes
              for(k in row.data().classes){
                class_id=row.data().classes[k].class_id;
                class_code=row.data().classes[k].code;
                class_name=row.data().classes[k].class_name;
                promo=row.data().classes[k].promo;
                lang_name=row.data().classes[k].lang_name;
                lang_code2=row.data().classes[k].lang_code2;
                status=row.data().classes[k].status;
                role=row.data().classes[k].role;
                if(class_id!=null){
                  $("#container_user_"+user_id).find(".classesTable").append(
                    `<tr class="classes_`+class_id+`">
                      <td>`+class_id+`</td>
                      <td><span class="tinyFlag flag_`+lang_code2+`"></span></td>
                      <td>`+class_name+`</td>
                      <td>`+promo+`</td>
                      <td>`+class_code+`</td>
                      <td>`+status+`</td>
                      <td>`+role+`</td>
                    </tr>`
                  );
                  if(role=="explore" || role=="perso"){$("#container_user_"+user_id).find(".classes_"+class_id).hide();}
                }
              }
              for(k in dataLang)
              {
                lang_code2=dataLang[k].lang_code2;
                lang_id=dataLang[k].lang_id;
                lang_name=dataLang[k].lang_name;
                lang_interface_build=dataLang[k].interface_build;
                if(lang_interface_build==1)
                {
                  $("#container_user_"+user_id).find(".lang_input").append("<option value='"+lang_id+"'>"+lang_name+"</option>");
                }
              }
            tr.addClass('shown');
        }
    });
    table.columns().every( function () {
            var that = this;

            $( '.inputFilter', this.footer() ).on( 'keyup change clear', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        } );
});


function selectAllVisible()
{
  console.log($(".checkAllVisible").prop("checked"));
  if($(".checkAllVisible").prop("checked")==1){
  table.rows({page:'current'})
    .nodes()
    .to$().addClass( 'selectedRow');
  }
  else {
    table.rows()
      .nodes()
      .to$().removeClass( 'selectedRow' );
  }
  updateMailList();
}
function updateMailList()
{  selectedRow=table.rows( ".selectedRow" ).data();
$(".infoTextarea").html("");
  for(kRow=0;kRow<selectedRow.length;kRow++){
    $(".infoTextarea").append(selectedRow[kRow].email+";");
    }

}
function changeCategory(user_id)
{
  event.preventDefault();
  licenceCategory=$("#container_user_"+user_id).find(".licence_category_input").val();
  if(licenceCategory=="custom")
  {
    $("#container_user_"+user_id).find(".licence_type_input").show();
    $("#container_user_"+user_id).find(".lang_input").hide();
  }
  else {
    $("#container_user_"+user_id).find(".licence_type_input").hide();
    $("#container_user_"+user_id).find(".lang_input").show();
  }
}
function sendAddLicence(user_id)
{
  event.preventDefault();
  licenceCategory=$("#container_user_"+user_id).find(".licence_category_input").val();
  if(licenceCategory=="custom")
  {
  licence_type=$("#container_user_"+user_id).find(".licence_type_input").val();
  lang_id="";
  }
  else if(licenceCategory=="trans_deck"){
    lang_id=$("#container_user_"+user_id).find(".lang_input").val();
    licence_type="trans_deck";

  }
  else if(licenceCategory=="trans_interface"){
    lang_id=$("#container_user_"+user_id).find(".lang_input").val();
    licence_type="trans_interface";
  }
  nbreJour=$("#container_user_"+user_id).find(".nbreJour_input").val();
  $.getJSON("ajax_admin.php?action=addLicence&user_id="+user_id+"&licence_type="+licence_type+"&lang_id="+lang_id+"&nbreJour="+nbreJour, function(result)
  {console.log(result.licence_id);
    $("#container_user_"+user_id).find(".licencesTable").append(
      `<tr class="licence_`+result.licence_id+`">
        <td><span class="tinyFlag flag_`+code2FromLang_id(lang_id)+`"></span>`+licence_type+`</td>
        <td>valable `+nbreJour+` jours</td>
        <td><button class="disableLicence" onclick="DisableLicence(`+result.licence_id+`);">Désactiver</button></td>
      </tr>`
    );
  });
}
function takeControl(user_id)
{
  $.getJSON("ajax_admin.php?action=takeControl&user_id="+user_id, function(result)
  {console.log(result);
    if(result=="ok")
    {
        window.location.href="loginSession.php";
    }
  });
}
function code2FromLang_id(lang_id){
  lang_code2="";
  for(klang in dataLang)
  {
    if(dataLang[klang].lang_id==lang_id){lang_code2=dataLang[klang].lang_code2;}
  }
  return lang_code2;
}
function DisableLicence(licence_id)
{
  $("#container_user_"+user_id).find(".licence_"+licence_id).hide();
  $.getJSON("ajax_admin.php?action=disableLicence&licence_id="+licence_id, function(result)
  {console.log(result);});
}
</script>


</body>
</html>
