/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| This file is dedicated for defining HTTP routes. A single file is enough
| for majority of projects, however you can define routes in different
| files and just make sure to import them inside this file. For example
|
| Define routes in following two files
| ├── start/routes/cart.ts
| ├── start/routes/customer.ts
|
| and then import them inside `start/routes.ts` as follows
|
| import './routes/cart'
| import './routes/customer''
|
*/
import Route from "@ioc:Adonis/Core/Route";

Route.on("/").render("index");

// Route.resource("formation", "FormationsController").middleware({
//   create: ["auth"],
//   store: ["auth"],
//   destroy: ["auth"],
// });

Route.get("groups/:code/join", "GroupsController.join");
Route.get("groups/join", "GroupsController.join");

Route.resource("plays", "PlaysController");
Route.resource("audios", "AudiosController");
Route.resource("groups", "GroupsController");
Route.resource("lines", "LinesController");
Route.resource("images", "ImagesController");
Route.resource("characters", "CharactersController");


Route.get("dashboard", "AppsController.index");

Route.get("groups/:id/leave", "GroupsController.leave");

Route.get("/groups/:groupId/plays/create", "PlaysController.create");
Route.get("/groups/:groupId/plays/:playId/detach", "PlaysController.detach");
Route.post("/profile/updateRole", "UsersController.RoleUpdateByUser");

//ROUTES FOR SCENES

Route.resource("group/:group_id/scene", "ScenesController");
Route.resource("scenes", "ScenesController");
Route.get("group/:group_id/scene/:scene_id/action", "ScenesController.action");
Route.post("group/:group_id/scene/:scene_id/change", "ScenesController.change");

Route.get("group/:group_id/scene/:scene_id/select", "ScenesController.select");
Route.put("play/:id/scene/createNew", "ScenesController.createNew");
//WITHOUT GROUP
Route.resource("scene", "ScenesController");
Route.get("api/scene/:sceneId/version/:versionId/lines", "ScenesController.lines");
//CHARACTER
Route.post("line/:lineId/characters/create", "CharactersController.store");
Route.post("line/updateCharacter", "LinesController.updateCharacter");
Route.post("line/updateText", "LinesController.updateText");
Route.post("line/splitAText", "LinesController.splitAText");
Route.post("/lines/createNewVersion", "LinesController.createNewVersion");
Route.post("scenes/:scene_id/line/create/:position", "LinesController.create");

Route.post("play/createNew", "PlaysController.createNew");

Route.post("api/scene/:sceneId/updateName", "ScenesController.updateName");
Route.post("api/play/:playId/updateName", "PlaysController.updateName");
Route.post("api/scene/delete", "ScenesController.destroy");


Route.post("/audio/upload", "AudiosController.upload");
Route.get("/audio/getAudioVersions", "AudiosController.getAudioVersions");
Route.post("/audios/createNewVersion", "AudiosController.createNewVersion");
Route.get(
  "/audio/getAudiosFromAudioVersion",
  "AudiosController.getAudiosFromAudioVersion"
);

//PROFILE
Route.get("/profile", "UsersController.profile").middleware("auth");
Route.post("/changeMail", "UsersController.changeMail").middleware("auth");
Route.post("/changePassword", "UsersController.changePassword").middleware(
  "auth"
);

// AUTH

Route.get('auth/google', 'AuthController.redirect').as('social.login')
Route.get('auth/google/callback', 'AuthController.handleCallback').as('social.login.callback')

Route.get("/logout", "AuthController.logout").as("auth.logout");

Route.get(
  "/loginWithSignedUrl/:username",
  "AuthController.loginWithSignedUrl"
).as("loginWithSignedUrl");

Route.get("/login", async ({ response, view, auth }) => {
  if (auth.isGuest) {
    return view.render("auth/login");
  } else {
    response.redirect("/profile");
  }
}).middleware("silentAuth");
Route.get("/register", async ({ response, view, auth }) => {
  if (auth.isGuest) {
    return view.render("auth/register");
  } else {
    response.redirect("/profile");
  }
}).middleware("silentAuth");

Route.post("/login", "AuthController.login").as("auth.login");
Route.post("/register", "AuthController.register").as("auth.register");


//RECOVERY METHODS
Route.post("/recoverUsername", "AuthController.recoverUsername");
Route.post("/recoverPassword", "AuthController.recoverPassword");
Route.get("/recoverUsername", async ({ view }) => {
  return view.render("auth/recoverUsername");
});
Route.get("/recoverPassword", async ({ view }) => {
  return view.render("auth/recoverPassword");
});




//TEST ROUTES
Route.get("/test", "testController.index");
Route.post("testImage", async ({ request }) => {
  const coverImage = request.file("cover_image");
  console.log(coverImage);
  // if (coverImage) {
  //   await coverImage.move(Application.tmpPath('uploads'))
  // }
});





