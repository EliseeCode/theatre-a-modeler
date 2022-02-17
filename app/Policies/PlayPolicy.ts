import { BasePolicy } from '@ioc:Adonis/Addons/Bouncer'
import User from 'App/Models/User'
import Play from 'App/Models/Play'

export default class PlayPolicy extends BasePolicy {
	public async viewList(user: User) {}
	public async view(user: User, play: Play) {}
	public async create(user: User) {}
	public async update(user: User, play: Play) {}
	public async delete(user: User, play: Play) {}
}
