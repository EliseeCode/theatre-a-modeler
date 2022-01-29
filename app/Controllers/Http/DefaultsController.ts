import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'

export default class DefaultsController {
  public async index ({params}: HttpContextContract) {
  return "Default controller with "+params.objet;
  }

  public async create ({}: HttpContextContract) {
  }

  public async store ({}: HttpContextContract) {
  }

  public async show ({}: HttpContextContract) {
  }

  public async edit ({}: HttpContextContract) {
  }

  public async update ({}: HttpContextContract) {
  }

  public async destroy ({}: HttpContextContract) {
  }
}
