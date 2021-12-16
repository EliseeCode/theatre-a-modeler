import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Formation from "App/Models/Formation";
import Logger from "@ioc:Adonis/Core/Logger";
import { schema, rules } from "@ioc:Adonis/Core/Validator";

export default class FormationsController {
  public async index({ request, view, auth }: HttpContextContract) {
    var user_id = "";
    var role = "";

    if (request.authorized) {
      const user = request.user;
      role = user.role;
    }
    if (role == "validator" || role == "admin") {
      Logger.info("validator");
      const formations = await Formation.all();
      return view.render("formation.index", {
        formations,
      });
    } else {
      Logger.info("not validator");
      //const formations=await Formation.all()

      const formations = await Formation.query() // 👈now have access to all query builder methods
        .where("status", "validated")
        .orWhere("creator_id", user_id);
      return view.render("formation/index", {
        formations: formations,
      });
    }
  }

  public async create({ view }: HttpContextContract) {
    return view.render("formation.create");
  }

  public async show({ request, params, view, auth }: HttpContextContract) {
    Logger.info("dans show formation" + params.id);
    try {
      if (request.authorized) {
        const user = request.user;
      }
      const formation = await Formation.findOrFail(params.id);
      if (formation) {
        Logger.info("found a formation");
        await formation.load("creator");
        return view.render("formation/show", {
          formation: formation,
          user: auth.user,
        });
      }
    } catch (error) {
      console.log(error);
    }
  }

  public async edit({}: HttpContextContract) {}

  public async update({ params, request }: HttpContextContract) {
    const formation = await Formation.find(params.id);

    Logger.info({ ForumId: params.id }, `Formation retrieved successfully`);

    if (formation) {
      formation.title = request.input("title");
      formation.description = request.input("description");
      if (await formation.save()) {
        //await formation.load("user");
        Logger.info({ ForumId: params.id }, `Formation updated successfully`);
        return formation;
      }
      Logger.error({ ForumId: params.id }, `Formation failed to update`);
      return; // 422
    }
    Logger.error({ ForumId: params.id }, `Formation not found`);
    return; // 401
  }

  public async store({ auth, request, response }: HttpContextContract) {
    const newFormationSchema = schema.create({
      title: schema.string({ trim: true }, [rules.minLength(3)]),
      description: schema.string({ escape: true }),
    });

    const messages = {
      "title.minLength": "Il faut un titre d'au moins 3 charactères.",
      "description.required": "Ajoute une description",
    };

    const user = await auth.authenticate();
    const payload = await request.validate({
      schema: newFormationSchema,
      messages,
    });

    const formation = await Formation.create(payload);

    await user.related("formations").save(formation);
    if (formation) {
      Logger.info("ok");
      return response.redirect("back");
    }
    Logger.info({ Formation: formation }, `Formation not created`);
    return response.redirect("back");
  }

  public async destroy({ auth, params, response }: HttpContextContract) {
    const user = await auth.authenticate();
    Logger.info({ user_id: user.id }, `User auth successfully`);
    const formation = await Formation.query().where("id", params.id).delete();
    if (!formation) {
      return response.notFound({ message: "Formation non trouvé" });
    }

    return response.redirect("back");
    //return 404;
  }
}
