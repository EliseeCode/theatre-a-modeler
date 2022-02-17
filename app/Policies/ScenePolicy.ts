import { BasePolicy } from '@ioc:Adonis/Addons/Bouncer'
import User from 'App/Models/User'
import Scene from 'App/Models/Scene'

export default class ScenePolicy extends BasePolicy {
	public async viewList(user: User) {}
	public async view(user: User, scene: Scene) {}
	public async create(user: User) {}
	public async update(user: User, scene: Scene) {}
	public async delete(user: User, scene: Scene) {}
}
