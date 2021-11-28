import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import { verify } from "jsonwebtoken";

const SECRET = "hello";

const tokenExtractor = async (token) =>
  new Promise((resolve) => {
    console.log(`Here's the JWT: ${token}`);
    verify(token, SECRET, (err, decoded) => {
      console.log(err);
      console.log(`DecodedData: ${decoded && decoded.data}`); // ERROR when decoding session cookies
      if (!err) return resolve(decoded.data);
      return resolve(null);
    });
  });

export default class Authentication {
  public async handle(
    { request }: HttpContextContract,
    next: () => Promise<void>
  ) {
    const token = request.cookie("jwt", undefined);
    request.userData = await tokenExtractor(token);
    console.log(`UserData: ${request.userData}`);
    request.authorized = request.userData ? true : false;
    console.log(`Authorized: ${request.authorized}`);
    await next();
  }
}
