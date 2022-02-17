import { BasePolicy } from '@ioc:Adonis/Addons/Bouncer'
import User from 'App/Models/User'
import Group from 'App/Models/Group'
import Role from '../../contracts/enums/Role'

export default class GroupPolicy extends BasePolicy {
	public async view(user: User, group: Group) {
		await user.load("groups")
		return user.groups.map((el) => el.id).includes(group.id)
	}
	public async create(user: User) {
		return user.roleId == Role.TEACHER
	}
	public async update(user: User, group: Group) {
		await group.load('users', (userQuery) => { userQuery.where('group_user.role_id', Role.TEACHER) })
		return group.users.map(el => el.id).includes(user.id);
	}
	public async delete(user: User, group: Group) {
		await group.load('users', (userQuery) => { userQuery.where('group_user.role_id', Role.TEACHER) })
		return group.users.map(el => el.id).includes(user.id);
	}
}
