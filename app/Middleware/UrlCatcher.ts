import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'

export default class UrlCatcher {
  public async handle({ request, session }: HttpContextContract, next: () => Promise<void>) {

    // code for middleware goes here. ABOVE THE NEXT CALL
    session.put('originalUrl', request.url());
    await next()
  }
}
