import { BasePolicy } from '@ioc:Adonis/Addons/Bouncer'
import User from 'App/Models/User'
import Play from 'App/Models/Play'
import Group from 'App/Models/Group'
import Role from 'Contracts/enums/Role'
import Status from 'Contracts/enums/Status'
import Database from '@ioc:Adonis/Lucid/Database'

export default class PlayPolicy extends BasePolicy {
	public async before(user: User | null) {
		// allow admins authorization to perform all comment actions
		if (user?.roleId === Role.ADMIN) {
			return true
		}
	}

	public async update(user: User, play: Play) {
		if (user.id == play.creatorId) {
			return true;
		}
		if (play?.$extras?.pivot_status == Status.CHANGEABLE) {
			return true;
		}
		return false;
	}
	public async delete(user: User, play: Play) {
		if (user.id == play.creatorId) {
			return true;
		}
	}
	public async link(user: User, play: Play, groupsId: number[]) {
		//user Teacher des group
		const res = await Database.query().select()
			.from('group_user')
			.where('group_user.role_id', Role.TEACHER)
			.andWhere('group_user.user_id', user.id)
			.andWhereIn('group_user.group_id', groupsId)
			.countDistinct('group_user.group_id as nbreGroupTeacher');
		const groupTeacherFlag = res[0].nbreGroupTeacher == groupsId.length;
		//user owner of the Play	
		const creatorPlayFlag = play.creatorId == user.id;
		console.log(creatorPlayFlag, groupTeacherFlag)
		return groupTeacherFlag && creatorPlayFlag;
	}
}
