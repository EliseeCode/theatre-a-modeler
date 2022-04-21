import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Role from "Contracts/enums/Role";
export default class AppsController {
  public async index({
    auth,
    response,
    view,
  }: HttpContextContract) {
    if (!auth.isLoggedIn) {
      return response.redirect().toRoute("/plays");
    }
    const user = await auth.authenticate();

    await user.load("groups", (Query) => {
      Query.preload("users").preload("creator").preload("plays");
    });

    await user.load("plays", (Query) => {
      Query.preload("groups")
        .preload("creator")
        .preload("scenes", (sceneQuery) => {
          sceneQuery.preload("lines", (lineQuery) => {
            lineQuery.preload("character").orderBy("position");
          });
        });
    });


    return view.render("dashboard/index", { user, Role });
  }
}
