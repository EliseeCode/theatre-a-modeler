import { BasePolicy } from '@ioc:Adonis/Addons/Bouncer'
import User from 'App/Models/User'
import Audio from 'App/Models/Audio'

export default class AudioPolicy extends BasePolicy {
	public async create(user: User) {}
	public async update(user: User, audio: Audio) {}
	public async delete(user: User, audio: Audio) {}
}
