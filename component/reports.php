<style>
.tiny_deck_image{width:50px;height:50px;object-fit: cover;}
.input_section{display:inline-block;width:250px;vertical-align:middle;}
</style>
<?php include_once "reportPage/report__studentList.php" ?>
<?php include_once "reportPage/report__quiz.php" ?>
<?php include_once "reportPage/report__deck_exo.php" ?>
<?php include_once "reportPage/report__mission.php" ?>

<script>
function displayPageClassReport(classReport){
  console.log(classReport);
  classUsers=classReport.classUsers;
  $('#page_report').html(
  `<div class="tab_container">
    <!--<div class="tab_item tab2 tab2_exo" onclick="displayReportExo(31);"><?php echo __("Exercices");?></div>-->
    <div class="tab_item tab2 tab2_studentList" onclick="displayStudentList();"><?php echo __("Listes des élèves");?></div>
    <div class="tab_item tab2 tab2_decks" onclick="displayDecksWork();"><?php echo __("Travail");?></div>
    <div class="tab_item tab2 tab2_quiz" onclick="displayQuiz();"><?php echo __("Quiz en classe");?></div>
    <!--<div class="tab_item tab2 tab2_missions" onclick="displayMissionWork();"><?php echo __("Missions");?></div>-->
    <!--<div class="tab_item tab2 tab2_objectif" onclick="displayObjectifWork();"><?php echo __("Objectifs");?></div>-->
   </div>
   <div class="sub_page_container">
     <div class="report_page report_page_exo"></div>
     <div class="report_page report_page_studentList"></div>
     <div class="report_page report_page_quiz"></div>
     <div class="report_page report_page_decks"></div>
     <div class="report_page report_page_missions"></div>
     <div class="report_page report_page_objectif"></div>
   </div>
  `);

  displayDecksWork();

  $(".tableScoreClass").sortable({
    items: "tr:not(:first,.allClassLine)",
    axis:'y',
    update: function (event, ui) {
    var data = $(this).sortable('serialize');
    console.log(data);
    $.ajax({
          data: data,
          type: 'POST',
          url: 'ajax.php?action=setOrderStat'
      });
      }
  });
}







</script>
