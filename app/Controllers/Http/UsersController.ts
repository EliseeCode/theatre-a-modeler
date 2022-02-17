import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import User from 'App/Models/User'
import Role from 'Contracts/enums/Role';


export default class UsersController {
  public async index({ view }: HttpContextContract) {

    const users = await User.all()
    return view.render("user.index", {
      users
    });
  }
  public async profile({ auth, view }: HttpContextContract) {
    return view.render('user.profile', { 'user': auth.user, Role })
  }

  public async RoleUpdateByUser({ auth, request, response }: HttpContextContract) {
    const user = await auth.authenticate();
    const roleId = parseInt(request.all().role);
    if (![Role.STUDENT, Role.TEACHER].includes(roleId)) {
      return response.status(403);
    }
    user.roleId = roleId;
    await user.save();
    return response.redirect('back');
  }

  public async giveAdminRole({ auth, params, response }: HttpContextContract) {
    const askingUser = await auth.authenticate();
    if (askingUser.roleId != Role.ADMIN) { return response.redirect('back'); }
    const id = params.id;
    const user = await User.findOrFail(id);
    user.roleId = Role.ADMIN;
    await user.save();
    return response.redirect('back');
  }
  public async removeAdminRole({ auth, params, response }: HttpContextContract) {
    const askingUser = await auth.authenticate();
    if (askingUser.roleId != Role.ADMIN) { return response.redirect('back'); }
    const id = params.id;
    const user = await User.findOrFail(id);
    user.roleId = Role.STUDENT;
    await user.save();
    return response.redirect('back');
  }
  public async create({ view }: HttpContextContract) {

    return view.render('formation.create')

  }

  public async destroy({ params, response }: HttpContextContract) {
    await User.query()
      .where("id", params.id)
      .delete();


    return response.redirect('back')
    //return 404;
  }
}
