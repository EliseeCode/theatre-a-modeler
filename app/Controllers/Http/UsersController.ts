import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import User from 'App/Models/User'


export default class UsersController {
  public async index ({view}: HttpContextContract) {
   
    const users=await User.all()
    return view.render("user.index", {
      users});
  }
  
  public async giveAdminRole({params,response}:HttpContextContract){
    const id=params.id;
    const user=await User.findOrFail(id);
    user.role="admin";
    await user.save();
    return response.redirect('back');
  }
  public async removeAdminRole({params,response}:HttpContextContract){
    const id=params.id;
    const user=await User.findOrFail(id);
    user.role="null";
    await user.save();
    return response.redirect('back');
  }
  public async create ({view}: HttpContextContract) {

    return view.render('formation.create') 

  }

  public async destroy({ params,response }: HttpContextContract) {
    await User.query()
    .where("id", params.id)
    .delete();
        
     
    return response.redirect('back')
    //return 404;
  }
}
