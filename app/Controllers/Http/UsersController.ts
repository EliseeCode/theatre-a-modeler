import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import User from "App/Models/User";
import Role from "Contracts/enums/Role";
import { schema, rules } from "@ioc:Adonis/Core/Validator";


export default class UsersController {
  public async index({ view }: HttpContextContract) {
    const users = await User.all();
    return view.render("user.index", {
      users,
    });
  }
  public async profile({ auth, view }: HttpContextContract) {
    return view.render("user.profile", { user: auth.user, Role });
  }

  public async changePassword({
    request,
    auth,
    response,
  }: HttpContextContract) {
    const user = await auth.authenticate();
    const password = request.all().password;
    if (password) {
      user.password = password;
      await user.save();
      await auth.logout();
      return response.json({ success: true });
    }
    return response.json({ success: false });
  }

  public async changeMail({ request, auth, response }: HttpContextContract) {
    const user = await auth.authenticate();
    const emailRegex =
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    const email = request.all().email;
    const assertion = emailRegex.test(email);
    if (assertion) {
      console.log(`Mail regex check passed for ${email} ...`);
      user.email = email;
      await user.save();
      return response.json({ email });
      return response.redirect().back();
    }
    return response.internalServerError(
      "Regex check failed, invalid email form..."
    );
  }
  public async updateUsername({ request, auth, response }: HttpContextContract) {
    const user = await auth.authenticate();
    const username = request.all().username;
    const newUsernameSchema = schema.create({
      username: schema.string({}, [
        rules.maxLength(50),
        rules.minLength(2),
        rules.unique({ table: 'users', column: 'username' })
      ])
    })

    const payload = await request.validate({
      schema: newUsernameSchema, messages: {
        'username.unique': 'Cet identifiant est déjà attribué. Vous devez en choisir un autre',
        'username.minLength': 'identifiant trop court',
        'username.maxLength': 'identifiant trop long'
      }
    })

    user.username = payload.username;
    await user.save();
    return response.redirect().back();


  }

  public async RoleUpdateByUser({
    auth,
    request,
    response,
  }: HttpContextContract) {
    const user = await auth.authenticate();
    const roleId = parseInt(request.all().role);
    if (![Role.STUDENT, Role.TEACHER].includes(roleId)) {
      return response.status(403);
    }
    user.roleId = roleId;
    await user.save();
    return response.redirect("back");
  }

  public async giveAdminRole({ auth, params, response }: HttpContextContract) {
    const askingUser = await auth.authenticate();
    if (askingUser.roleId != Role.ADMIN) {
      return response.redirect("back");
    }
    const id = params.id;
    const user = await User.findOrFail(id);
    user.roleId = Role.ADMIN;
    await user.save();
    return response.redirect("back");
  }
  public async removeAdminRole({
    auth,
    params,
    response,
  }: HttpContextContract) {
    const askingUser = await auth.authenticate();
    if (askingUser.roleId != Role.ADMIN) {
      return response.redirect("back");
    }
    const id = params.id;
    const user = await User.findOrFail(id);
    user.roleId = Role.STUDENT;
    await user.save();
    return response.redirect("back");
  }
  public async create({ view }: HttpContextContract) {
    return view.render("formation.create");
  }

  public async destroy({ params, response }: HttpContextContract) {
    await User.query().where("id", params.id).delete();

    return response.redirect("back");
    //return 404;
  }
}
