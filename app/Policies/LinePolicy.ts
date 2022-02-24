import { BasePolicy } from "@ioc:Adonis/Addons/Bouncer";
import User from "App/Models/User";
import Line from "App/Models/Line";

export default class LinePolicy extends BasePolicy {
  public async create(user: User) {}
  public async update(user: User, line: Line) {
    return user.id == line.creatorId || user.id == line.version.creatorId;
  }
  public async delete(user: User, line: Line) {}
}
