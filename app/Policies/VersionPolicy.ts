import { BasePolicy } from '@ioc:Adonis/Addons/Bouncer'
import User from 'App/Models/User'
import Version from 'App/Models/Version'
import Role from 'Contracts/enums/Role'

export default class ScenePolicy extends BasePolicy {
	public async before(user: User | null) {
		// allow admins authorization to perform all comment actions
		if (user?.roleId == Role.ADMIN) {
			return true
		}
	}

	public async delete(user: User, version: Version) {
		if (version.creatorId == user.id) {
			return true;
		}
		else {
			return false;
		}
	}
}
