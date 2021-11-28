import Route from "@ioc:Adonis/Core/Route";
import { sign, verify } from "jsonwebtoken";

// TODO: Sanitize inputs
// TODO: Create a httponly cookie based JWT (+)
// TODO: CSRF protection (if it's really possible)
// TODO: Find a more elegant way for middleware mechanism [eliminate repetitive auth checks ?]
// TODO: Refresh token system
// Today (after interphase :)) :
// TODO: if logged in, restrict access to /login (!) restrict or redirect it to home??
// TODO: create a register form (+)
// TODO: integrate a mysql database
// TODO: logout mechanism

// Roadmap for this session:
// TODO: User auth with mysql database
// TODO: Input sanitization

const creds = {
  email: "abcd@abcd.com",
  password: "12345",
}; // A dummy cred object to replace the database for now...

const SECRET = "hello";

const tokenVerifier = async (ctx, next) => {
  const token = ctx.request.cookie("jwt", undefined);
  console.log(`Here's the JWT: ${token}`);
  verify(token, SECRET, (err, decoded) => {
    if (err)
      return (
        Boolean(console.log(err.message)) && (ctx.request.authorized = false)
      );
    ctx.request.session = decoded.data;
    ctx.request.authorized = true;
    console.log(decoded.data);
  });
  await next();
};

/*const userAuth = async (ctx, next) => {

};*/

Route.get("/", async ({ request, /*response,*/ view }) => {
  //console.log(request, response);
  // TODO: Check if user has a JWT for us to remember it.
  console.log("[OK] Getting the root.");
  if (request.authorized)
    return view.render("welcome", { data: request.session });
  else return view.render("login");
}).middleware(tokenVerifier);

Route.get("/login", async ({ request, /*response,*/ view }) => {
  // TODO: Check if user has a JWT for us to remember it.
  console.log("[OK] Getting the /login");
  if (request.authorized)
    return view.render("errors/unauthorized", {
      error: "You can't login twice, can you?",
    });
  return view.render("login");
}).middleware(tokenVerifier);

Route.post("/login", async ({ request, response, view }) => {
  if (request.authorized)
    return view.render("errors/unauthorized", {
      error:
        "How could you even make a POST request without having a form... wait... you little hacker!",
    });
  const { email, password, toRemember } = request.body();
  const data = { email };
  // Authentication logic
  switch (true) {
    case creds.email !== email:
      return view.render("errors/unauthorized", {
        error: "It seems we don't to know you...",
      });
    case creds.password !== password:
      return view.render("errors/unauthorized", {
        error: "It seems you can't prove that this is you...",
      }); // whaaa
    default:
      // Generate a JWT
      const expiresIn = 60 * 60 * Number(Boolean(toRemember));
      const token = sign(
        {
          data: { email },
        },
        SECRET,
        { expiresIn: expiresIn }
      );
      console.log(token);

      response.cookie("jwt", token, {
        path: "/",
        // expires: new Date(new Date().getTime() + expiresIn),
        maxAge: expiresIn,
        httpOnly: true,
        sameSite: true,
        domain: request.hostname() || "",
        // secure: true,
      });

      request.session = data;
      request.authorized = true;
      console.log("[OK] Cookie sent.");
      // Will it pass the request object when redirecting? We'll see! nope :|
      return response.redirect("/"); // Using redirect will result to unauthorization as the middleware checks strictly for a JWT...
    // response.location("/");
    // return view.render("welcome", { data: request.session });
  }
}).middleware(tokenVerifier);

Route.get("/register", ({ request, view }) => {
  return view.render("register");
});

Route.post("/register", ({ request, response, view }) => {});
