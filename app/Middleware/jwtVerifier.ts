import { verify } from "jsonwebtoken";
import Env from "@ioc:Adonis/Core/Env";
import { Exception } from "@poppinss/utils";

export default function JWTVerifier(jwt) {
  const SECRET = Env.get("SECRET");
  const request = {};
  const token = jwt;

  console.log(`Here's the JWT: ${token?.slice(0, 5)}`);
  try {
    const decoded = verify(token, SECRET);
    console.log(
      `Here's the decoded data: ${JSON.stringify(decoded?.data, null, 4)}`
    );
    request.user = decoded?.data.checkedUser;
    request.authorized = true;
  } catch (error) {
    Boolean(console.log(error.message)) || (request.authorized = false);
  }
  return request;
}
