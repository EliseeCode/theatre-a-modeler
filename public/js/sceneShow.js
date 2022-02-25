////////////////////
// INITIALISATION GLOBALS
////////////////////
$(".selectLineVersion select").each(function () {
  characterIds.add($(this).data("character-id"));
});
characterIds.forEach((characterId) => {
  updateLineVersion(characterId, 1);
});

////////////////////
// LINE VERSION SELECTION
////////////////////

$(".selectLineVersion select").on("change", function (e) {
  const audioVersionSelect = e.target
    .closest(".characterSelect")
    .querySelector(".selectAudioVersion select");
  var characterId = $(this).data("character-id");
  var versionId = $(this).val();
  updateLineVersion(characterId, versionId);
});

function updateLineVersion(characterId, versionId) {
  UpdateDisplaySelectLineVersion(characterId, versionId);
  //if it is a real lineVersion
  if (versionId > 0) {
    console.log("versionId", versionId);
    UpdateDisplayLineVersion(characterId, versionId);
    getAudioVersion(characterId, versionId);
  } else {
    //if it is 0=>Create a new text alternative
    createNewLineVersion(characterId);
  }
}
function UpdateDisplaySelectLineVersion(characterId, versionId) {
  $(".characterSelect_" + characterId + " .selectLineVersion select").val(
    versionId
  );
}
function UpdateDisplayLineVersion(characterId, versionId) {
  $(".lineCharacter_" + characterId).hide();
  $(".lineCharacter_" + characterId + ".lineVersion_" + versionId).show();
}

function getAudioVersion(characterId, versionId) {
  console.log("getAudioVersion");
  const token = $(".csrfToken").data("csrf-token");
  const params = {
    characterId,
    versionId,
    sceneId: sceneID,
    _csrf: token,
  };
  $.get("/audio/getAudioVersions", params, function (data) {
    //data: audioVersion[].id
    //                    .doubleur:{name,id}
    //                    .name

    $(".lineCharacter_" + characterId + " .selectAudioVersion").show();
    console.log(data.versions, characterId);
    if (data.versions) {
      var selectAudioVersion = $(
        ".lineCharacter_" + characterId + " .selectAudioVersion select"
      );
      var selectAudioVersion = $(
        ".selectAudioVersion_" + characterId + " select"
      );

      console.log(selectAudioVersion);
      $(
        ".selectAudioVersion_" + characterId + " select .versionOption"
      ).remove();
      //selectAudioVersion.find("option.audioVersionOption").remove();
      for (let version of data.versions) {
        selectAudioVersion.prepend(
          `<option class="versionOption" value=${version.id}>${version.name}-${version.audios.length}/${data.lines.length}</option>`
        );
      }
    }
  });
}

function createNewLineVersion(characterId) {
  const token = $(".csrfToken").data("csrf-token");
  const params = {
    characterId: characterId,
    sceneId: sceneID,
    _csrf: token,
  };
  $(".selectAudioVersion_" + characterId + " select .versionOption").remove();
  $.post("/lines/createNewVersion", params, function (data) {
    console.log(data);
    versionId = data.version.id;
    $(`.selectLineVersion_${characterId} select`).prepend(
      `<option value="${versionId}">${data.version.name}</option>`
    );
    $(`.selectLineVersion_${characterId} select`).val(versionId);
    for (let line of data.lines) {
      $(".linePosition_" + line.position).hide();
      $(".linePosition_" + line.position + ".lineVersion_New").show();
      $(".linePosition_" + line.position + ".lineVersion_New textarea").attr(
        "id",
        "lineText_" + line.id
      );
      $(".linePosition_" + line.position + ".lineVersion_New textarea").on(
        "input",
        function () {
          updateText(line.id);
        }
      );
    }
  });
}
////////////////////
// AUDIO VERSION SELECTION
////////////////////
$(".selectAudioVersion select").on("change", async function () {
  HARDRESET();
  //alert($(this).val()+$(this).data('character-id'))
  const parentContainer = $(this).closest(".line");
  var characterId = $(this).data("character-id");
  const totalPositions = [];
  $(`.lineCharacter_${characterId}:not([style*="display: none"])`).each(
    function () {
      console.log($(this));
      totalPositions.push($(this).attr("data-position"));
    }
  ); // This is for updating buttons of the same character in different lines.
  console.log("total pos", totalPositions, characterId);
  var audioVersionId = parseInt($(this).val());
  $(".selectAudioVersion_" + characterId + " select").val(audioVersionId);
  for (let position of totalPositions) globalAudios[position] = 0; // declaring default one as robot
  switch (audioVersionId) {
    case "":
      break;
    case 0:
      console.log("on écoute le robot");
      for (let position of totalPositions)
        updateAudioActionBtnDisplay("waitRobotAudio", position);
      break;
    case -1:
      let version = await createAudioVersion(characterId);
      updateAudioVersionSelectDisplay(characterId, version); // prepend loops over the selector, but attr not
      for (let position of totalPositions) {
        updateAudioActionBtnDisplay("waitRecord", position);
        globalAudios[position] = -1;
      }

      break;
    default:
      characterToAudioVersion[characterId] = audioVersionId;
      console.log(characterToAudioVersion, "s");
      await getAudioPaths(audioVersionId, characterId);
      for (let position of totalPositions) {
        if (globalAudios[position]) {
          console.log("haveMyAudio", position);
          updateAudioActionBtnDisplay("haveMyAudio", position);
        } else {
          // no audio record found
          console.log("waitRecord", globalAudios[position]);
          updateAudioActionBtnDisplay("waitRecord", position);
        }
      }
  }
});
function updateAudioVersionSelectDisplay(characterId, version) {
  $(`.selectAudioVersion_${characterId} select`).prepend(
    `<option class="versionOption" value="${version.id}">${version.name}</option>`
  );
  $(`.selectAudioVersion_${characterId} select`).val(version.id);

  $(".lineCharacter_" + characterId + " .btnAction").hide();
  $(".lineCharacter_" + characterId + " .btnStartRecord").show();
}

function getLineIds(characterId) {
  console.log($(".lineCharacter_" + characterId).length);
  return $(".lineCharacter_" + characterId).map((el) => {
    return $(el).data("lineId");
  });
}

function getPosition(lineId) {
  return $("#line_" + lineId)[0].dataset["position"];
}
function updateAutoAudioActionBtnDisplay(status) {
  switch (status) {
    case "AutoPlaying":
      $("#btnAutoPlay").parent().hide();
      $("#btnAutoResume").parent().show();
      $("#btnAutoReplay").parent().show();
      $("#btnAutoPause").parent().show();
      break;
    case "AutoPlayFinished":
      $("#btnAutoPlay").parent().hide();
      $("#btnAutoResume").parent().hide();
      $("#btnAutoReplay").parent().show();
      $("#btnAutoPause").parent().hide();
      break;
  }
}
function updateAudioActionBtnDisplay(status, position = null) {
  switch (status) {
    case "waitRobotAudio":
      $(`.linePosition_${position} .btnAction`).hide();
      $(`.linePosition_${position} .btnRobotize.btnStart`).show();
      break;
    case "robotIsPlaying":
      $(`.linePosition_${position} .btnAction`).hide();
      $(`.linePosition_${position} .btnRobotize.btnPause`).show();
      break;
    case "robotIsPaused":
      $(`.linePosition_${position} .btnAction`).hide();
      $(`.linePosition_${position} .btnRobotize.btnResume`).show();
      break;
    case "waitRecord":
    case "stopRecord":
      $(`.linePosition_${position} .btnAction`).hide();
      $(`.linePosition_${position} .btnStart.btnRecord`).show();
      break;
    case "doRecord":
      $(`.linePosition_${position} .btnAction`).hide();
      $(`.linePosition_${position} .btnStop.btnRecord`).show();
      break;
    case "haveMyAudio":
      $(`.linePosition_${position} .btnAction`).hide();
      $(`.linePosition_${position} .btnPlay.btnAudio`).show();
      $(`.linePosition_${position} .btnDelete.btnAudio`).show();
      break;
    case "audioIsPlaying":
      $(`.linePosition_${position} .btnAction`).hide();
      $(`.linePosition_${position} .btnPause.btnAudio`).show();
      $(`.linePosition_${position} .btnDelete.btnAudio`).show();
      break;
    case "audioIsPaused":
      $(`.linePosition_${position} .btnAction`).hide();
      $(`.linePosition_${position} .btnResume.btnAudio`).show();
      $(`.linePosition_${position} .btnDelete.btnAudio`).show();
      break;
    case "haveOtherAudio":
      $(`.linePosition_${position} .btnAction`).hide();
      $(`.linePosition_${position} .btnPlay.btnAudio`).show();
      break;
  }
}

async function getAudioPaths(audioVersionId, characterId) {
  const token = $(".csrfToken").data("csrf-token");
  const params = {
    characterId,
    audioVersionId,
    sceneId: sceneID,
    _csrf: token,
  };
  const data = await $.get(
    "/audio/getAudiosFromAudioVersion",
    params
  ).promise();
  console.log("getAudiosFromAudioVersionAnswer", data);
  for (let audio of data) {
    globalAudios[audio.line.position] = audio.public_path;
    // const parentElement = document.getElementById(`line_${audio.line.id}`); // it doesn't work, because a same line can have multiple line version, thus conflicting ids!
    const parentElement = $(
      `.linePosition_${audio.line.position}:not([style*="display: none"])`
    );

    const deleteButton = $(parentElement).find(".btnDelete.btnAudio").get(0);
    $(deleteButton).attr("data-audio-id", audio.id);
    console.log("AAA", audio.id);
  }

  console.log("getAudioVersionAnswer", data);
}

////////////////////
// AUDIO RECORD
////////////////////

const createAudioVersion = async (characterID) => {
  let form = new FormData();
  form.append("characterId", characterID);
  const result = await (
    await fetch(`${window.location.origin}/audios/createNewVersion`, {
      method: "POST",
      headers: {
        "X-CSRF-Token": $(".csrfToken").data("csrf-token"),
        /* 'Accept': `${blob.type}`, // FIXME: Not working while file transfer?
                'Content-Type': `${blob.type}`,*/
        "Content-Transfer-Encoding": "base64",
      },
      mode: "cors",
      body: form,
    })
  ).json();
  return result;
};

const uploadAudio = async (event, objectURL) => {
  const groupId = window.location.pathname.match(/group\/(\d+)/)[1]; // this is for authorization feature
  const parentContainer = $(event.target).closest(".line");
  console.log(parentContainer);
  const position = parentContainer.attr("data-position");
  console.log(parentContainer.attr("data-position"));
  const audioVersionSelect = $(parentContainer)
    .find(".selectAudioVersion select")
    .get(0);
  console.log(audioVersionSelect);
  const characterID = $(event.target)
    .closest(".line")
    .attr("data-character-id");
  const lineID = $(parentContainer).attr("data-line-id");
  /* console.log("uploading audio!!", objectURL)
        console.log(`Here's the line_id to attach: ${lineID}`); */
  const deleteButton = event.target.parentNode.querySelector(".btnDelete");
  /* const recordControlButton = event.target.parentNode.querySelector(".btnStartRecord");
        const recordIcon = recordControlButton.querySelector("i");
        recordIcon.classList.remove("fa-play");
        recordIcon.classList.add("fa-microphone");
        event.target.parentNode.querySelector(".btnStartRecord span:nth-child(2)").textContent = "Enregistrer !"; */
  player.currentTime = 0;
  player.src = "";
  let audioVersionID =
    audioVersionSelect.options[audioVersionSelect.selectedIndex].value;
  updateAudioActionBtnDisplay("haveMyAudio", position);
  if (audioVersionID < 0) {
    // its value is -1
    console.log("mapping audio version to character");
    const result = await createAudioVersion(characterID);
    console.log(result);
    audioVersionID = result.id;

    const audioVersionOption = document.createElement("option");
    audioVersionOption.value = audioVersionID;
    audioVersionOption.text = result.name;
    audioVersionSelect.add(audioVersionOption);
    $(audioVersionSelect).val(audioVersionID).select().trigger("change");
  }
  characterToAudioVersion[characterID] = audioVersionID;
  console.log("deneme", objectURL);
  const blob = await fetch(objectURL).then((r) => r.blob());
  console.log(blob, audioVersionID, lineID);
  form = new FormData();
  form.append("audio", blob);
  form.append("lineId", lineID);
  form.append("versionId", audioVersionID);
  form.append("groupId", groupId);
  fetch(`${window.location.origin}/audios`, {
    method: "POST",
    headers: {
      "X-CSRF-Token": $(".csrfToken").data("csrf-token"),
      /* 'Accept': `${blob.type}`, // FIXME: Not working while file transfer?
                'Content-Type': `${blob.type}`,*/
      "Content-Transfer-Encoding": "base64",
    },
    mode: "cors",
    body: form,
  })
    .then((response) => {
      if (!response.ok) throw response;
      console.log(response);
      return response.json();
    })
    .then((data) => {
      globalAudios[position] = data.public_path;
      $(deleteButton).attr("data-audio-id", data.id);
      // window.location.reload();
    })
    .catch((err) => {
      console.error(err);
    });
};

const deleteRecording = (groupId, linePosition, audioId, objectURL = null) => {
  // groupId, position, audioId
  console.log("deleting record :", audioId);
  updateAudioActionBtnDisplay("waitRecord", linePosition);

  const form = new FormData();
  form.append("groupId", groupId);

  player.currentTime = 0;
  player.src = "";

  if (objectURL) URL.revokeObjectURL(objectURL); // FIXME Invalid URI. Load of media resource  failed.

  fetch(`${window.location.origin}/audios/${audioId}`, {
    method: "DELETE",
    headers: {
      "X-CSRF-Token": $(".csrfToken").data("csrf-token"),
      /* 'Accept': `${blob.type}`, // FIXME: Not working while file transfer?
                      'Content-Type': `${blob.type}`,*/
      "Content-Transfer-Encoding": "base64",
    },
    mode: "cors",
    body: form,
  })
    .then((response) => {
      if (!response.ok) throw response;
      return response.json();
    })
    .then((data) => {
      globalAudios[linePosition] = 0;
      console.log(
        `[OK] Successfully deleted the audio with an id of ${audioId}`
      );
    })
    .catch((err) => {
      console.error(err);
    });
};

const handleMediaDevice = (event, stream) => {
  const parentContainer = $(event.target).closest(".line");
  console.log(event.target);
  const options = { mimeType: "audio/webm" };
  const recordedChunks = [];
  let objectURL;
  mediaRecorder = new MediaRecorder(stream, options);
  console.log(mediaRecorder.mimeType);
  mediaRecorder.addEventListener("dataavailable", function (e) {
    if (e.data.size > 0) recordedChunks.push(e.data);
  });
  mediaRecorder.addEventListener("stop", async (e) => {
    console.log("stopped the media recorder");
    // make the create request here!
    const blob = new Blob(recordedChunks, { type: mediaRecorder.mimeType });
    objectURL = URL.createObjectURL(blob);
    player.src = objectURL;
    // ref: https://stackoverflow.com/questions/11455515/how-to-check-whether-dynamically-attached-event-listener-exists-or-not
    // uploadButton.onclick = (_e) => uploadAudio(_e, objectURL);
    await uploadAudio(event, objectURL);
    // FIXME how to revoke microphone permissions?
  });
  if (mediaRecorder && mediaRecorder.state !== "recording")
    mediaRecorder.start();
};

const playAudio = (linePosition) => {
  const player = $("#player").get(0);
  updateAudioActionBtnDisplay("audioIsPlaying", linePosition);

  if (globalAudios[linePosition]) {
    player.src = globalAudios[linePosition];
    player.onended = () =>
      updateAudioActionBtnDisplay("haveMyAudio", linePosition);
    player.play();
  }
  return;
};

const pauseAudio = (linePosition) => {
  const player = $("#player").get(0);
  updateAudioActionBtnDisplay("audioIsPaused", linePosition);

  if (globalAudios[linePosition]) {
    player.pause();
  }
  return;
};

const resumeAudio = (linePosition) => {
  const player = $("#player").get(0);
  updateAudioActionBtnDisplay("audioIsPlaying", linePosition);
  if (globalAudios[linePosition]) {
    player.play();
  }
  return;
};

////////////////////
// AUTO PLAYER
////////////////////
const resumeAuto = () => {
  if (audioIsPaused) {
    console.log("[INFO] Was paused, resuming...");
    speechSynthesis.resume();
    return player.play();
  }
};

const playAuto = (i) => {
  if (i == globalAudios.length + 1) {
    console.log("The album is over...");
    return;
  }
  console.log("[INFO] Setting up the auto player...");
  const audioPath = globalAudios[i];
  updateAutoAudioActionBtnDisplay("AutoPlaying");
  try {
    audioIsPaused = false;
    player.currentTime = 0;
    const dummyURL = new URL(audioPath); // if it doesn't fail, it's a url
    player.src = audioPath;
    player.play();
    player.onended = () => {
      player.currentTime = 0;
      player.src = "";
      console.log(
        `The ${i + 1}th track is over, playing the next if there's one...`
      );
      return playAuto(++i);
    };
  } catch (e) {
    if (audioPath == 0) {
      // Text to speech
      console.log("[INFO] Setting up the text-to-speech engine...");
      globalSynthesedSpeech = robotSpeak(i);

      globalSynthesedSpeech.onend = () => {
        console.log("[INFO] Speech ended. Passing to the following...");
        return playAuto(++i);
      };
      return;
    } else if (audioPath == -1) {
      console.log("[INFO] Starting auto registration...");
      return;
    } else {
      console.log("[ERROR] Audio path is null... Skipping it.");
      return playAuto(++i);
    }
  }
};

const pauseAuto = (e) => {
  console.log("[INFO] Stopping the player...");
  audioIsPaused = true;
  speechSynthesis.pause();
  player.pause();
};

const replayAuto = (e) => {
  pauseAuto();
  globalSynthesedSpeech.text = "";
  globalSynthesedSpeech.onend = null;
  speechSynthesis.cancel();
  player.currentTime = 0;
  player.src = "";
  playAuto(0);
};

////////////////////
// HARD RESET
////////////////////
const HARDRESET = () => {
  speechSynthesis.pause();
  if (globalSynthesedSpeech) globalSynthesedSpeech.onend = null;
  speechSynthesis.cancel();
  player.pause();
  player.currentTime = 0;
  player.src = "";
  return;
};

////////////////////
// Text2Speech
////////////////////
const robotSpeak = (linePosition) => {
  updateAudioActionBtnDisplay("robotIsPlaying", linePosition);
  const text = $(
    `.linePosition_${linePosition}:not([style*="display: none"]) textarea`
  ).val();
  console.log(linePosition, text);
  let message = new SpeechSynthesisUtterance(text);
  message.lang = "fr-FR";
  speechSynthesis.speak(message);
  message.onend = () =>
    updateAudioActionBtnDisplay("waitRobotAudio", linePosition);
  return message;
};

const robotPause = (linePosition) => {
  updateAudioActionBtnDisplay("robotIsPaused", linePosition);
  speechSynthesis.pause();
};

const robotResume = (linePosition) => {
  updateAudioActionBtnDisplay("robotIsPlaying", linePosition);
  speechSynthesis.resume();
};

////////////////////
// BUTTON LISTENERS
////////////////////

const startRecordButtonCallback = (event) => {
  const parentElement = $(event.target).closest(".line");
  const linePosition = parentElement.attr("data-position");
  updateAudioActionBtnDisplay("doRecord", linePosition);
  isRecording = true;
  console.log("starting recording...");
  navigator.mediaDevices
    .getUserMedia({ audio: true, video: false })
    .then((stream) => handleMediaDevice(event, stream));
};

const stopRecordButtonCallback = (event) => {
  const parentElement = $(event.target).closest(".line");
  const linePosition = parentElement.attr("data-position");
  updateAudioActionBtnDisplay("stopRecord", linePosition);
  isRecording = false;
  console.log("stopping recording...");
  if (mediaRecorder && mediaRecorder.state === "recording")
    mediaRecorder.stop();
};

const playAudioButtonCallback = (event) => {
  const parentContainer = $(event.target).closest(".line");
  const linePosition = parentContainer.attr("data-position");
  playAudio(linePosition);
  return;
};

const pauseAudioButtonCallback = (event) => {
  const parentContainer = $(event.target).closest(".line");
  const linePosition = parentContainer.attr("data-position");
  pauseAudio(linePosition);
  return;
};

const resumeAudioButtonCallback = (event) => {
  const parentContainer = $(event.target).closest(".line");
  const linePosition = parentContainer.attr("data-position");
  resumeAudio(linePosition);
  return;
};

const deleteAudioButtonCallback = (event) => {
  const groupId = window.location.pathname.match(/group\/(\d+)/)[1]; // this is for authorization feature
  const parentContainer = $(event.target).closest(".line");
  const linePosition = parentContainer.attr("data-position");
  const audioId = $(event.target).attr("data-audio-id");
  console.log(event.target);
  console.log(audioId);

  deleteRecording(groupId, linePosition, audioId);
  return $(event.target).attr("data-audio-id", "");
};

const startRobotizeButtonCallback = (event) => {
  const parentContainer = $(event.target).closest(".line");
  const linePosition = parentContainer.attr("data-position");
  robotSpeak(linePosition);
  return;
};

const pauseRobotizeButtonCallback = (event) => {
  const parentContainer = $(event.target).closest(".line");
  const linePosition = parentContainer.attr("data-position");
  robotPause(linePosition);
  return;
};

const resumeRobotizeButtonCallback = (event) => {
  const parentContainer = $(event.target).closest(".line");
  const linePosition = parentContainer.attr("data-position");
  robotResume(linePosition);
  return;
};

$("#btnAutoPlay").on("click", () => playAuto(0));
$("#btnAutoPause").on("click", pauseAuto);
$("#btnAutoResume").on("click", resumeAuto);
$("#btnAutoReplay").on("click", replayAuto);

$(".btnStart.btnRecord").on("click", startRecordButtonCallback);
$(".btnStop.btnRecord").on("click", stopRecordButtonCallback);
$(".btnStart.btnRobotize").on("click", startRobotizeButtonCallback);
$(".btnPause.btnRobotize").on("click", pauseRobotizeButtonCallback);
$(".btnResume.btnRobotize").on("click", resumeRobotizeButtonCallback);
$(".btnPlay.btnAudio").on("click", playAudioButtonCallback);
$(".btnPause.btnAudio").on("click", pauseAudioButtonCallback);
$(".btnDelete.btnAudio").on("click", deleteAudioButtonCallback);
$(".btnResume.btnAudio").on("click", resumeAudioButtonCallback);

function toggleDropdownMenu(objectType, objectId) {
  window.event.stopPropagation();
  $("#dropdown-option-container").html("");
  $("#dropdown-menu-" + objectType + "-" + objectId)
    .clone()
    .attr("id", "currentDropDown")
    .appendTo("#dropdown-option-container");
  $("#dropdown-option-container").show();
  var positionTrigger = $(
    "#dropdown-trigger-" + objectType + "-" + objectId
  ).offset();
  console.log(positionTrigger);
  $("#dropdown-option-container").offset({
    top: positionTrigger.top + 20,
    left: positionTrigger.left - 200,
  });
}
$(document).mouseup(function (e) {
  var container = $("#dropdown-option-container, .dropdown-trigger");
  var stuffToHide = $("#dropdown-option-container");
  // if the target of the click isn't the container nor a descendant of the container
  if (!container.is(e.target) && container.has(e.target).length === 0) {
    stuffToHide.hide();
  }
});

let timer = [];

function updateText(lineId) {
  if (timer[lineId] != null) {
    clearTimeout(timer[lineId]);
  }
  timer[lineId] = setTimeout(() => {
    sendUpdateText(lineId);
  }, 1000);
}
function sendUpdateText(lineId) {
  console.log("updateText");
  const text = $(`#lineText_${lineId}`).val().trim();
  const token = $(".csrfToken").data("csrf-token");
  const params = {
    lineId,
    text,
    _csrf: token,
  };
  $.post("/line/updateText", params, function (data) {
    console.log("updateTextData");
    if (data) {
      $(`#lineText_${lineId}`).parent().addClass("saved");
      setTimeout(function () {
        $(".saved").removeClass("saved");
      }, 500);
    }
  });
}

function updateCharacter(characterId, lineId) {
  const token = $(".csrfToken").data("csrf-token");
  const params = {
    lineId,
    characterId,
    _csrf: token,
  };

  $.post("/line/updateCharacter", params, function (data) {
    if (data) {
      $(`#select_character_line_${lineId}`).html("");
      characterClone = $(`#character_line_${characterId}_${lineId}`).clone();
      characterClone.appendTo(`#select_character_line_${lineId}`);
      characterClone.find(".caret").show();
    }
  });
}

function auto_grow(element) {
  element.style.height = "30px";
  element.style.minHeight = "60px";
  element.style.height = element.scrollHeight + "px";
}
[...document.getElementsByClassName("lineText")].forEach((element) => {
  auto_grow(element);
});
