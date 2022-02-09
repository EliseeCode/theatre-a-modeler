 import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
 import Logger from "@ioc:Adonis/Core/Logger";
export default class AppsController {
    public async index({ request, auth,response,session,view}: HttpContextContract)
    {
        if(!auth.isLoggedIn)
        {
            return response.redirect().toRoute("/plays");
        }
        const user=await auth.authenticate();
        await user.load('plays',(Query)=>{Query.preload('creator').preload('scenes')});

        await user.load('groups',(Query)=>{Query.preload("creator")});
        return view.render("dashboard/index",{user})      
    }
}
