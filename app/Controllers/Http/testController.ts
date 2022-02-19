import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import Group from 'App/Models/Group';
import Character from 'App/Models/Character';
import Logger from "@ioc:Adonis/Core/Logger";
import Role from 'Contracts/enums/Role';
import CharacterFetcher from '../helperClass/CharacterFetcher';
import User from 'App/Models/User';


export default class GroupsController {
  public async index({ auth, view }: HttpContextContract) {
    const user = await auth.authenticate();
    await user.load("groups");
    return view.render('test', { user })
  }
}
