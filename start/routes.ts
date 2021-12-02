/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| This file is dedicated for defining HTTP routes. A single file is enough
| for majority of projects, however you can define routes in different
| files and just make sure to import them inside this file. For example
|
| Define routes in following two files
| ├── start/routes/cart.ts
| ├── start/routes/customer.ts
|
| and then import them inside `start/routes.ts` as follows
|
| import './routes/cart'
| import './routes/customer''
|
*/
import { Response } from '@adonisjs/http-server/build/standalone'
import I18n from '@ioc:Adonis/Addons/I18n'
import Route from '@ioc:Adonis/Core/Route'
import { logger } from 'Config/app'
import authConfig from 'Config/auth'


Route.post('language/:locale', async ({ session, response, params }) => {
  /**
   * Only update locale when it is part of the supportedLocales
   */
  if (I18n.supportedLocales().includes(params.locale)) {
    session.put('locale', params.locale)
    logger.info("inside",params.locale)
  }
  logger.info("inside",params.locale)
  response.redirect().back()
}).as('language.update')

Route.on('/').render('welcome')


Route.resource('formation','FormationsController').middleware({
  create: ['auth'],
  store: ['auth'],
  destroy: ['auth'],
})
Route.resource('users','UsersController').middleware({  
})
Route.post('/user/:id/admin','UsersController.giveAdminRole');
Route.post('/user/:id/notAdmin','UsersController.removeAdminRole');

Route.post('/login', 'AuthController.login').as('auth.login')
Route.post('/register', 'AuthController.register').as('auth.register')
Route.get('/logout', 'AuthController.logout').as('auth.logout')
Route.get('/profile','AuthController.profile').middleware("auth");
Route.get('/login',async({ response, view,auth }) => {
  if(auth.isGuest){
  return view.render('auth/login')}
  else{
    response.redirect("/formation");
  }
}).middleware('silentAuth')

Route.get('/register',async({ view }) => {
  return view.render('auth/register')
})
