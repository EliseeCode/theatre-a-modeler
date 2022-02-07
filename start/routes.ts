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
//import I18n from '@ioc:Adonis/Addons/I18n'
import Route from "@ioc:Adonis/Core/Route";

// Route.post('language/:locale', async ({ session, response, params }) => {
//   /**
//    * Only update locale when it is part of the supportedLocales
//    */
//   if (I18n.supportedLocales().includes(params.locale)) {
//     session.put('locale', params.locale)
//   }
//   response.redirect().back()
// }).as('language.update')

Route.on("/").render("index");
//Route.get('/test',async({request})=>{return {domaine:request.subdomains(),hostname:request.hostname(),host:request.host(),prot:request.protocol()}});

Route.resource("formation", "FormationsController").middleware({
  create: ["auth"],
  store: ["auth"],
  destroy: ["auth"],
});
// Route.resource('users','UsersController').middleware({
// })
// Route.resource('classes','ClassesController');
// Route.post('/user/:id/admin','UsersController.giveAdminRole');
// Route.post('/user/:id/notAdmin','UsersController.removeAdminRole');

// Route.get('/auth/recovery',async({ view }) => {
//   return view.render('auth/recovery')
// })

Route.resource("plays", "PlaysController");
Route.resource("scenes", "ScenesController");
Route.resource("audios", "AudiosController");
Route.resource("lines", "LinesController");
Route.get("play/:play_id/scene/:scene_id", "ScenesController.show");
Route.get("api/play/createNew", "PlaysController.createNew");
Route.get("api/play/:id/scene/createNew", "ScenesController.createNew");
Route.post("api/scene/:sceneId/updateName", "ScenesController.updateName");
Route.post("api/scene/delete", "ScenesController.destroy");

//Route.post('/auth/recovery',async({view,request})=>{return view.render('auth/recovery',{username:request.input("username")});})
// Route.get('/checkRecoveryMethod', async({view,request})=>{return view.render('auth/recovery')});
Route.post("/recoverUsername", "AuthController.recoverUsername");
Route.post("/recoverPassword", "AuthController.recoverPassword");

Route.post("/audio/upload", "AudiosController.upload");

Route.get("/recoverUsername", async ({ view }) => {
  return view.render("auth/recoverUsername");
});
Route.get("/recoverPassword", async ({ view }) => {
  return view.render("auth/recoverPassword");
});
// Route.get('/verifyResetPassword/:username/', 'AuthController.verifyResetPassword');

Route.get("/logout", "AuthController.logout").as("auth.logout");
Route.get("/profile", "AuthController.profile").middleware("auth");
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

Route.get("/:objet", "DefaultsController.index");
