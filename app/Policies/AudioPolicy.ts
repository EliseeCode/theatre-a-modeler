import { BasePolicy } from "@ioc:Adonis/Addons/Bouncer";
import User from "App/Models/User";
import Audio from "App/Models/Audio";
import Role from "Contracts/enums/Role";
import Version from "App/Models/Version";
import Group from "App/Models/Group";

export default class AudioPolicy extends BasePolicy {
  public async before(user: User | null) {
    // allow admins authorization to perform all comment actions
    if (user?.roleId == Role.ADMIN) {
      return true;
    }
  }
  public async create(
    user: User,
    audioVersion: Version,
    group: Group | null = null
  ) {
    if (user.id == audioVersion.creatorId) {
      return true;
    } else {
      if (group) {
        await user.load("groups", (groupQuery) => {
          groupQuery.where("group_id", group.id).pivotColumns(["role_id"]);
        });
        const roleId = user.groups[0].$extras.pivot_role_id;

        if ([Role.TEACHER, Role.EDITOR].includes(roleId)) {
          return true;
        } else {
          return false;
        }
      } else {
        return true;
      }
    }
  }
  public async update(user: User, audio: Audio, group: Group | null = null) {
    if (user.id == audio.creatorId) {
      return true;
    } else {
      if (group) {
        await user.load("groups", (groupQuery) => {
          groupQuery.where("group_id", group.id).pivotColumns(["role_id"]);
        });
        const roleId = user.groups[0].$extras.pivot_role_id;

        if ([Role.TEACHER, Role.EDITOR].includes(roleId)) {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    }
  }
  public async delete(user: User, audio: Audio, group: Group | null = null) {
    if (user.id == audio.creatorId) {
      return true;
    } else {
      if (group) {
        await user.load("groups", (groupQuery) => {
          groupQuery.where("group_id", group.id).pivotColumns(["role_id"]);
        });
        const roleId = user.groups[0].$extras.pivot_role_id;

        if (user.id == audio.creatorId) {
          return true;
        } else if ([Role.TEACHER, Role.EDITOR].includes(roleId)) {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    }
  }
}
