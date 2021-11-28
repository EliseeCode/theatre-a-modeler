import Route from "@ioc:Adonis/Core/Route";
import { sign } from "jsonwebtoken";

// TODO: Sanitize inputs
// TODO: Create a httponly cookie based JWT (+)

// TODO: Refresh token system [as there's not an authority server (no 3rd party access), so will use only AT]
// TODO: if logged in, restrict access to /login (!) restrict or redirect it to home??
// TODO: create a register form (+)
// TODO: integrate a mysql database
// TODO: User auth with mysql database

// Roadmap for this userData:

// TODO: Input sanitization
// TODO: Find a more elegant way for middleware mechanism [eliminate repetitive auth checks ?] | Global Middleware
// TODO: CSRF protection (if it's really possible)
// TODO: logout mechanism

const creds = {
  email: "abcd@abcd.com",
  password: "12345",
}; // A dummy cred object to replace the database for now...

const SECRET = "hello";

const tokenVerifier = () => {};

Route.get("/", async ({ request, /*response,*/ view }) => {
  console.log("[OK] Getting the root.");
  const userData = request.userData;
  if (request.authorized) return view.render("welcome", { data: userData });
  else return view.render("login"); // Change it to auth, 2 in 1
}).middleware(tokenVerifier);

Route.post("/", async ({ request, view }) => {
  console.log("[OK] Posting to the root.");
});

Route.get("/login", async ({ request, response, view }) => {
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
      const expiresIn = 60 * 60 * (toRemember ? 24 * 30 : 1); // Won't set the cookie session wide, due to expiration date tampering on JWT... else it fails!!
      const token = sign(
        {
          data: { email },
        },
        SECRET,
        { expiresIn: expiresIn }
      );
      console.log(token);

      const cookieSettings = {
        path: "/",
        httpOnly: true,
        sameSite: true,
        domain: request.hostname() || "",
        maxAge: expiresIn,
        // secure: true,
      };

      response.cookie("jwt", token, cookieSettings);

      request.userData = data;
      request.authorized = true;
      console.log("[OK] Cookie sent.");
      return response.redirect("/"); // Using redirect will result to unauthorization as the middleware checks strictly for a JWT... -> Figured this out by httponly cookie
  }
}).middleware(tokenVerifier);

Route.get("/register", ({ request, view }) => {
  return view.render("register");
});

Route.post("/register", ({ request, response, view }) => {});
