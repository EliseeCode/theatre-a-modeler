import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'

export default class GroupsController {
  public async index({ auth, view }: HttpContextContract) {
    const user = await auth.authenticate();
    await user.load("groups");
    return view.render('test', { user })
  }
}
