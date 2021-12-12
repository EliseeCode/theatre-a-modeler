import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import User from "App/Models/User";
import Logger from "@ioc:Adonis/Core/Logger";
import { HttpContext } from "@adonisjs/http-server/build/standalone";
export default class AuthController {
  public async login({ request, auth,response }: HttpContextContract) {
    const email = request.input("email");
    const password = request.input("password");
    await auth.use("web").attempt(email,password);
    //return token.toJSON();
    response.redirect().back();
  }
  public async logout({ auth,response }: HttpContextContract) {
    await auth.use('web').logout();
    Logger.info("logout");
    response.redirect().back();
  }
  public async register({ request, auth }: HttpContextContract) {
    const email = request.input("email");
    const password = request.input("password");
    const name = request.input("name");
    const organisation = request.input("organisation");
    const newUser = new User();
    newUser.email = email;
    newUser.password = password;
    newUser.name = name;
    newUser.organisation = organisation;
    await newUser.save();
    const token = await auth.use("web").login(newUser);
    return token.toJSON();
  }
  public async profile({auth, view}:HttpContextContract){
    return view.render('auth.profile',{'user':auth.user})
  }
}