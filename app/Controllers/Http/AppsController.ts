import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import Logger from "@ioc:Adonis/Core/Logger";
import Role from 'Contracts/enums/Role';
export default class AppsController {
    public async index({ request, auth, response, session, view }: HttpContextContract) {
        if (!auth.isLoggedIn) {
            return response.redirect().toRoute("/plays");
        }
        const user = await auth.authenticate();
        await user.load('plays', (Query) => { Query.preload('creator').preload('scenes') });

        await user.load('groups', (Query) => { Query.preload("creator").preload('plays').preload('users') });
        const role = Role;
        return view.render("dashboard/index", { user, role })
    }
}
