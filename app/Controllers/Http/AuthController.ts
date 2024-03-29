import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import User from "App/Models/User";
import Logger from "@ioc:Adonis/Core/Logger";
import Mail from '@ioc:Adonis/Addons/Mail'
import Route from '@ioc:Adonis/Core/Route'
import Env from '@ioc:Adonis/Core/Env'
import { newUserSchema } from 'App/Schemas/newUserSchema'
import { loginUserSchema } from 'App/Schemas/loginUserSchema'

export default class AuthController {

  public async register({ response, auth, request }: HttpContextContract) {

    const payload = await request.validate({
      schema: newUserSchema,
      messages: {
        'email.required': 'Une adresse email est nécessaire',
        'password.minLength': 'Mot de passe trop court'
      }
    });
    const user = await User.create(payload);
    auth.login(user);
    Logger.info("user created");
    return response.redirect().toRoute("/dashboard");
  }



  public async login({ request, auth, response, session }: HttpContextContract) {

    console.log("login attempt");

    const payload = await request.validate({
      schema: loginUserSchema,
      messages: {
        'loginId.required': 'Un identifiant est nécessaire',
        'password.minLength': 'Mot de passe trop court'
      }
    });

    const loginId = payload.loginId;
    const password = payload.password;
    const remember = payload.remember;
    //console.log(loginId, password);
    try {
      // await auth.use("api").attempt(loginId, password, {
      //   expiresIn: "10 days",
      // });
      //return token.toJSON();
      await auth.use("web").attempt(loginId, password, remember);
      // console.log("attempt succeed");
    }
    catch {
      console.log("error");
      session.responseFlashMessages.set('errors.login', "L'identifiant ou le mot de passe ne sont pas correct");
      return response.redirect("/login");
    }
    //return token.toJSON();
    return response.redirect().back();
  }


  public async redirect({ ally }: HttpContextContract) {
    return ally.use('google').redirect()
    //await ally.driver('google').scope(['profile', 'email', 'https://www.googleapis.com/auth/drive']).redirect()
  }

  async handleCallback({ ally, auth, response }: HttpContextContract) {
    const provider = 'google';
    const google = await ally.use(provider)
    try {
      if (google.accessDenied()) {
        return 'Access was denied'
      }

      /**
       * Unable to verify the CSRF state
       */
      if (google.stateMisMatch()) {
        return 'Request expired. Retry again'
      }

      /**
       * There was an unknown error during the redirect
       */
      if (google.hasError()) {
        return google.getError()
      }

      /**
       * Finally, access the user
       */
      const googleUser = await google.user()

      /**
       * Find the user by email or create
       * a new one
       */
      if (googleUser.emailVerificationState === 'verified') {
        const user = await User.firstOrCreate({
          loginId: googleUser.id
        }, {
          username: googleUser.nickName,
          email: googleUser.email == undefined ? undefined : googleUser.email,
          loginId: googleUser.id,
          password: ''
        })
        await auth.use('web').login(user)
        response.redirect('/')
      }
      response.redirect('/')
      /**
       * Login user using the web guard
       */

    }
    catch (error) {
      console.log(error);
    }
  }

  public async loginWithSignedUrl({ view, request, auth, params }: HttpContextContract) {
    if (request.hasValidSignature()) {
      const username = params.username;
      if (username == null) { return view.render("errors/unauthorized"); }
      const user = await User.findBy("username", username);
      if (user != null) {
        auth.login(user);
        return view.render("app/index", { user });
      }
      return view.render("errors/not-found");
    }
    return view.render("errors/unauthorized");
  }

  public async recoverPassword({ session, response, request, view }: HttpContextContract) {
    const username = request.input("username");
    if (username == null) { return view.render('auth/recovery'); }

    const user = await User.findBy("username", username);
    if (user == null) {
      session.responseFlashMessages.set('errors.username', "Il n'y a pas de compte avec cet identifiant.");
      response.redirect().back();
      return;
    }

    if (user.email != null) {
      const absolutePathPrefix = request.protocol() + "://" + request.host();
      const username = user.username;
      const signedUrlRecoveryLogin = Route.builder()
        .params({ username })
        .prefixUrl(absolutePathPrefix)
        .makeSigned('loginWithSignedUrl', { expiresIn: '30m' });
      Env.get('NODE_ENV');
      const senderEmail = process.env.SENDEREMAIL || "";
      await Mail.send((message) => {
        message
          .from(senderEmail)
          .to(user.email)
          .subject('Recover Account!')
          .htmlView('emails/recoveryLogin', { username: user.username, signedUrlRecoveryLogin })
      });
      session.responseFlashMessages.set('message', "un mail vous a été envoyé pour vous connecter :" + user.email + "<br/>" + signedUrlRecoveryLogin);
      response.redirect().back();
      return;
    }
    else {
      session.responseFlashMessages.set('errors.username', "Il n'y a pas d'adresse mail associé à l'identifiant : " + username);
      response.redirect().back();
      return;
    }
  }

  public async recoverUsername({ session, request, response }: HttpContextContract) {
    const email = request.input("email");
    if (email == null) { return response.redirect().back(); }

    //const users = await User.findMany("email",email);
    const users = await User.query()
      .from('users')
      .where('email', email)
      .select('username');

    if (users.length == 0) {
      session.responseFlashMessages.set('errors.username', "Il n'y a pas de compte associé avec l'adresse mail : " + email);
      response.redirect().back();
      return;
    }
    if (email != null) {
      Env.get('NODE_ENV');
      const senderEmail = process.env.SENDEREMAIL || "";
      await Mail.send((message) => {
        message
          .from(senderEmail)
          .to(email)
          .subject('Récupération du/des identifiants!')
          .htmlView('emails/recoveryUsername', { users })
      })
      session.responseFlashMessages.set('message', "Un mail vous a été envoyé avec la liste des identifiants associés à :" + email);
      response.redirect().back();
      return;
    }
    else {
      session.responseFlashMessages.set('errors.email', "merci de renseigner une adresse mail valide");
      response.redirect().back();
      return;
    }
  }


  public async logout({ auth, response }: HttpContextContract) {
    await auth.use('web').logout();
    Logger.info("logout");
    response.redirect().back();
  }
}