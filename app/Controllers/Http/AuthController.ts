import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import User from "App/Models/User";
import Logger from "@ioc:Adonis/Core/Logger";
import { sign } from "jsonwebtoken";
import Env from "@ioc:Adonis/Core/Env";

import { createHash } from "crypto";

async function userInsertion(user_obj) {
  /* user_obj {
    email:
    verification:
    name:
    password:
    organisation:
  }; */

  const newUser = new User();
  Object.assign(newUser, user_obj);
  newUser.save();
  return newUser;
}

export default class AuthController {
  public async login({ request, auth, response }: HttpContextContract) {
    const email = request.input("email");
    const password = request.input("password");
    const toRemember = request.input("toRemember");

    // const hashedPassword = await Hash.make(password); // https://stackoverflow.com/questions/55801772/nodejs-how-to-compare-two-hashes-password-of-bcrypt
    const hashedPassword = createHash("sha256")
      .update(password)
      .digest("base64");

    let checkedUser = await User.findBy("email", email);
    if (checkedUser?.password === hashedPassword) {
      const SECRET = Env.get("SECRET");
      // Grant access
      console.log("Access granted...");
      const expiresIn = 60 * 60 * Number(Boolean(toRemember));
      const token = sign(
        {
          data: { checkedUser },
        },
        SECRET,
        { expiresIn: expiresIn }
      );
      console.log(token);

      response.cookie("jwt", token, {
        path: "/",
        maxAge: expiresIn,
        httpOnly: true,
        sameSite: true,
        domain: request.hostname() || "",
        // secure: true,
      });

      // request.user = checkedUser;
      // request.authorized = true;
      console.log("[OK] Cookie sent.");
      // Will it pass the request object when redirecting? We'll see! nope :|
      return response.redirect("/formation"); // Using redirect will result to unauthorization as the middleware checks strictly for a JWT...
    } else {
      console.log("Access denied! for some reason...");
      console.log(checkedUser?.password, hashedPassword);
      response.redirect().back();
    }
    console.log(email, password);
  }
  public async logout({ request, auth, response }: HttpContextContract) {
    console.log(`${request.user.id} logged out...`);
    request.user = null;
    request.authorized = false;
    response.clearCookie("jwt");
    return response.redirect().back();
  }
  public async register({ request, auth, response }: HttpContextContract) {
    const user = {
      email: request.input("email"),
      name: request.input("name"),
      password: request.input("password"),
      organisation: request.input("organisation"),
      toRemember: request.input("toRemember"),
    };

    console.log(user);
    let checkedUser = await User.findBy("email", user.email);
    if (checkedUser) {
      // User already exists
      response.redirect("/auth");
    }

    checkedUser = await userInsertion(user);

    const SECRET = Env.get("SECRET");
    const toRemember = true; // FIXME Don't know how to pass custom params to google oauth request
    // Grant access
    const expiresIn = 60 * 60 * Number(Boolean(toRemember));
    const token = sign(
      {
        data: { checkedUser },
      },
      SECRET,
      { expiresIn: expiresIn }
    );
    console.log(token);

    response.cookie("jwt", token, {
      path: "/",
      maxAge: expiresIn,
      httpOnly: true,
      sameSite: true,
      domain: request.hostname() || "",
      // secure: true,
    });

    return response.redirect("/formation"); // JWT!
  }
  public async profile({ request, response, auth, view }: HttpContextContract) {
    return view.render("auth.profile", { user: request.user });
  }

  public async googleCallback({
    ally,
    response,
    request,
  }: HttpContextContract) {
    const google = ally.use("google");
    if (google.accessDenied()) {
      return "Access was denied";
    }
    if (google.stateMisMatch()) {
      return "Request expired. Retry again";
    }
    if (google.hasError()) {
      return google.getError();
    }
    const userdata = await google.user();

    const user = {
      email: userdata.email,
      verification: userdata.emailVerificationState, // How to use this?
      name: userdata.name || userdata.email,
      password: userdata.id,
      organisation: null,
    };

    console.log(user);
    let checkedUser = await User.findBy("email", user.email);
    if (!checkedUser) {
      checkedUser = await userInsertion(user);
    }

    const SECRET = Env.get("SECRET");
    const toRemember = true; // FIXME Don't know how to pass custom params to google oauth request
    // Grant access
    const expiresIn = 60 * 60 * Number(Boolean(toRemember));
    const token = sign(
      {
        data: { checkedUser },
      },
      SECRET,
      { expiresIn: expiresIn }
    );
    console.log(token);

    response.cookie("jwt", token, {
      path: "/",
      maxAge: expiresIn,
      httpOnly: true,
      sameSite: true,
      domain: request.hostname() || "",
      // secure: true,
    });

    return response.redirect("/formation");
  }
}
