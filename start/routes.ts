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
import { Response } from "@adonisjs/http-server/build/standalone";
import I18n from "@ioc:Adonis/Addons/I18n";
import Route from "@ioc:Adonis/Core/Route";
import { logger } from "Config/app";
import authConfig from "Config/auth";

Route.post("language/:locale", async ({ session, response, params }) => {
  /**
   * Only update locale when it is part of the supportedLocales
   */
  if (I18n.supportedLocales().includes(params.locale)) {
    session.put("locale", params.locale);
    logger.info("inside", params.locale);
  }
  logger.info("inside", params.locale);
  response.redirect().back();
}).as("language.update");

Route.on("/").render("index");

Route.resource("formation", "FormationsController").middleware({
  "*": ["silentAuth"],
});
Route.resource("users", "UsersController").middleware("silentAuth");
Route.resource("classes", "ClassesController").middleware("silentAuth");
Route.post("/user/:id/admin", "UsersController.giveAdminRole").middleware(
  "silentAuth"
);
Route.post("/user/:id/notAdmin", "UsersController.removeAdminRole").middleware(
  "silentAuth"
);

Route.get("/auth/", async ({ request, response, view, auth }) => {
  if (!request.authorized) {
    return view.render("auth/auth", { user: request.user });
  } else {
    response.redirect("/formation");
  }
});
Route.post("/auth/login", "AuthController.login").as("auth.login");
Route.post("/auth/register", "AuthController.register").as("auth.register");
Route.get("/auth/logout", "AuthController.logout").as("auth.logout");
Route.get("/profile", "AuthController.profile").middleware("silentAuth");

Route.get("/auth/google/redirect", async ({ ally }) => {
  return ally.use("google").redirect();
});

Route.get("/auth/google/callback", "AuthController.googleCallback").as(
  "auth.googleCallback"
);
