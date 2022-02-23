import { BasePolicy } from "@ioc:Adonis/Addons/Bouncer";
import User from "App/Models/User";
import Audio from "App/Models/Audio";
import Role from "Contracts/enums/Role";
import ScenePolicy from "App/Policies/ScenePolicy";
import Version from "App/Models/Version";
import Group from "App/Models/Group";

export default class AudioPolicy extends BasePolicy {
  public async before(user: User | null) {
    // allow admins authorization to perform all comment actions
    if (user?.roleId == Role.ADMIN) {
      return true;
    }
  }
  public async create(user: User, audioVersion: Version) {
    console.log(user.id, audioVersion.creatorId);

    if (user.id == audioVersion.creatorId) {
      return true;
    } else if ([Role.TEACHER, Role.EDITOR].includes(user.roleId)) {
    }
  }
  public async update(user: User, audio: Audio) {
    // Normally we're going to allow audioversion creation
    if (
      (user.id == audio.creatorId && user.id == audio.version.id) ||
      user.id == audio.line.scene.play.creatorId
    ) {
      return true;
    } else if ([Role.TEACHER, Role.EDITOR].includes(user.roleId)) {
      const audioScene = audio.line.scene;
      const scenePolicy = new ScenePolicy();
      if (await scenePolicy.update(user, audioScene)) {
        // You belong to this play
        return true;
      } else {
        return false;
      }
    }
  }
  public async delete(user: User, audio: Audio) {
    if (
      user.id == audio.creatorId ||
      user.id == audio.line.scene.play.creatorId
    ) {
      return true;
    } else if ([Role.TEACHER, Role.EDITOR].includes(user.roleId)) {
      const audioScene = audio.line.scene;
      const policy = new ScenePolicy();
      if (await policy.update(user, audioScene)) {
        // You belong to this play
        return true;
      } else {
        return false;
      }
    }
  }
}
