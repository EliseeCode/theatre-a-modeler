import { BasePolicy } from '@ioc:Adonis/Addons/Bouncer'
import User from 'App/Models/User'
import Scene from 'App/Models/Scene'
import Role from 'Contracts/enums/Role'
import Play from 'App/Models/Play'
import Group from 'App/Models/Group'
import Database from '@ioc:Adonis/Lucid/Database'
import Status from 'Contracts/enums/Status'

export default class ScenePolicy extends BasePolicy {
	public async before(user: User | null) {
		// allow admins authorization to perform all comment actions
		if (user?.roleId == Role.ADMIN) {
			return true
		}
	}
	public async viewList(user: User) { }
	public async view(user: User, scene: Scene) {
		if (scene.play.creatorId != user.id) { return true; }
		if (scene.play.status == Status.PUBLIC) {
			return true;
		}
		const total = await Database.query()
			.select()
			.from('group_play')
			.join('group_user', 'group_play.group_id', 'group_user.group_id')
			.where("group_user.user_id", user.id)
			.andWhere("group_play.play_id", scene.play.id)
			.andWhere("group_play.status", Status.PUBLISHED).count('* as total');

		if (total[0]?.total > 0) {
			return true;
		}

	}
	public async create(user: User, play: Play) {

		if (play.creatorId == user.id) {
			return true;
		}
		else {
			const total = await Database.query()
				.select()
				.from('group_play')
				.join('group_user', 'group_play.group_id', 'group_user.group_id')
				.where("group_user.user_id", user.id)
				.andWhere("group_play.play_id", play.id)
				.andWhere("group_play.status", Status.CHANGEABLE).count('* as total');

			if (total[0]?.total > 0) {
				return true;
			}
		}
	}

	public async update(user: User, scene: Scene) {
		if (scene.creatorId == user.id || scene.play.creatorId == user.id) {
			return true;
		}
		else {
			const total = await Database.query()
				.select()
				.from('group_play')
				.join('group_user', 'group_play.group_id', 'group_user.group_id')
				.where("group_user.user_id", user.id)
				.andWhere("group_play.play_id", scene.play.id)
				.andWhere("group_play.status", Status.CHANGEABLE).count('* as total');

			if (total[0]?.total > 0) {
				return true;
			}
		}
	}
	public async delete(user: User, scene: Scene) {
		if (scene.creatorId == user.id || scene.play.creatorId == user.id) {
			return true;
		}
		else {
			const total = await Database.query()
				.select()
				.from('group_play')
				.join('group_user', 'group_play.group_id', 'group_user.group_id')
				.where("group_user.user_id", user.id)
				.andWhere("group_play.play_id", scene.play.id)
				.andWhere("group_play.status", Status.CHANGEABLE).count('* as total');

			if (total[0]?.total > 0) {
				return true;
			}
		}
	}
}
