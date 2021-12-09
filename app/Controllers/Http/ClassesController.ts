import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'

export default class ClassesController {
  public async index ({}: HttpContextContract) {
    return "Hello index";
  }

  public async create ({}: HttpContextContract) {
  }

  public async store ({}: HttpContextContract) {
  }

  public async show ({params}: HttpContextContract) {
    return params.id;
  }

  public async edit ({}: HttpContextContract) {
  }

  public async update ({}: HttpContextContract) {
  }

  public async destroy ({}: HttpContextContract) {
  }
}
