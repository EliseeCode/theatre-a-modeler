import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import Group from 'App/Models/Group';
import { newGroupSchema } from 'App/Schemas/newGroupSchema';
import Logger from "@ioc:Adonis/Core/Logger";

export default class GroupsController {
  public async index ({}: HttpContextContract) {
  }

  public async create ({view}: HttpContextContract) {
    return view.render("group/create");
  }
  public generateCode()
  {
    var characters = 'abcdefghjklmnpqrstuvwxyz0123456789';
    var result = ""
    var charactersLength = characters.length;

    for ( var i = 0; i < 6 ; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
  } 
  public async store ({auth,request,response}: HttpContextContract) {
      
      Logger.info("group is being created");
      // await request.validate({
      //   schema:newGroupSchema,
      //   messages:{
      //     'name.required':'Un nom pour ce groupe est nécessaire',
      //   }
      // });
      Logger.info("validation passed");
      const user=await auth.authenticate();
      const creatorId=user.id;
      var isValidCode=false;
      var code="";
      while(!isValidCode){
        code=this.generateCode();
        Logger.info("test code "+code);
        isValidCode=await Group.query().where('code',code).count?true:false;
      }
      Logger.info("code will be "+code);
      
      const group = await Group.create({
        name:request.all().name,
        description:request.all().description,
        creatorId:creatorId,
        code:code,
      });
      Logger.info("Group created");
      await user.related("groups").save(group,undefined,{'roleId':1});

      return response.redirect().toRoute("/dashboard");
  }

  public async show ({}: HttpContextContract) {
  }

  public async edit ({}: HttpContextContract) {
  }

  public async update ({}: HttpContextContract) {
  }

  public async destroy ({}: HttpContextContract) {
  }
}
