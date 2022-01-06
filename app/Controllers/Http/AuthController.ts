import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import User from "App/Models/User";
import Logger from "@ioc:Adonis/Core/Logger";

import UsersController from "./UsersController";
import { Response } from "@adonisjs/http-server/build/standalone";
import Mail from '@ioc:Adonis/Addons/Mail'
import Route from '@ioc:Adonis/Core/Route'
import Env from '@ioc:Adonis/Core/Env'
import {newUserSchema} from 'App/Schemas/newUserSchema'
import View from "@ioc:Adonis/Core/View";

export default class AuthController {

  public async register({ request,response }: HttpContextContract) {
    
    const payload=await request.validate({
      schema:newUserSchema,
      messages:{
        'name.required':'Un nom ou pseudo est nécessaire',
        'name.maxLength':'Le nom est trop long',
        'password.minLength':'Mot de passe trop court'
      }
    });
    const user = await User.create(payload);
    Logger.info("user created");
    return response.json({user});
  }



  public async login({ request, auth,response,session }: HttpContextContract) {
    const username = request.input("username");
    const password = request.input("password");
    try{
    await auth.use("web").attempt(username,password);
    }
    catch{
      session.responseFlashMessages.set('errors.login',"Wrong username or password");
      response.redirect().back();
    }
    //return token.toJSON();
    response.redirect().back();
  }


  public async loginWithSignedUrl({view,request, auth, params}: HttpContextContract)
  {
      if (request.hasValidSignature()) {
        const username=params.username;
        if(username==null){return view.render("errors/unauthorized");}
        const user = await User.findBy("username",username);
        if(user!=null){
        auth.login(user);
        return view.render("app/index",{user});
        }
        return view.render("errors/not-found");
      }
      return view.render("errors/unauthorized");
  }

  public async checkRecoveryMethod({ request, auth,response,view }: HttpContextContract) {
    const username = request.input("username");
    if(username==null){return view.render('auth/recovery');}

    const user = await User.findBy("username",username);
    if(user==null){
      return "there is no account with that username. Are you sure you have an account?";
    }
    if(user.email!=null){
      const absolutePathPrefix=request.protocol()+"://"+request.host();
      const username=user.username;
      const signedUrlRecoveryLogin=Route.builder()
                                          .params({username})
                                          .prefixUrl(absolutePathPrefix)
                                          .makeSigned('loginWithSignedUrl');  
      Env.get('NODE_ENV');
      const senderEmail = process.env.SENDEREMAIL || "";
      await Mail.send((message) => {
        message
          .from(senderEmail)
          .to(user.email)
          .subject('Recover Account!')
          .htmlView('emails/recoveryLogin', { username: user.username,signedUrlRecoveryLogin})
      })
      return "Please click in the link sent by e-mail to your recovery electronic address :"+user.email;
    }
    else{
      return "there is no recovery methode for "+username+". We can't do anything for you.";
    }
  }
    //response.redirect().back();
  

  

  

  public async logout({ auth,response }: HttpContextContract) {
    await auth.use('web').logout();
    Logger.info("logout");
    response.redirect().back();
  }

  public async profile({auth, view}:HttpContextContract){
    return view.render('auth.profile',{'user':auth.user})
  }
}