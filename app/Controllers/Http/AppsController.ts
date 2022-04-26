import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Image from "App/Models/Image";
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

  public async admin({
    auth,
    view,
  }: HttpContextContract) {
    const user = await auth.authenticate();
    if (user.roleId == Role.ADMIN) {
      const coverImages = await Image.query().where("status", "coverOfficial");
      const characterImages = await Image.query().where("status", "characterOfficial");
      return view.render("admin/image", { user, coverImages, characterImages });
    }

  }
}
