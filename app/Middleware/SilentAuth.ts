import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import JWTVerifier from "./jwtVerifier";

/**
 * Silent auth middleware can be used as a global middleware to silent check
 * if the user is logged-in or not.
 *
 * The request continues as usual, even when the user is not logged-in.
 */

const creds = {
  email: "abcd@abcd.com",
  password: "12345",
}; // A dummy cred object to replace the database for now...

export default class SilentAuthMiddleware {
  /**
   * Handle request
   */
  public async handle(
    { request, auth, response }: HttpContextContract,
    next: () => Promise<void>
  ) {
    /**
     * Check if user is logged-in or not. If yes, then `ctx.auth.user` will be
     * set to the instance of the currently logged in user.
     */
    console.log("Silent Rejection! ssh...");
    const token = request.cookie("jwt", undefined);
    const authenticationData = JWTVerifier(token);
    if (!authenticationData.authorized) return response.redirect("/auth");

    await next();
  }
}
