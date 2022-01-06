import { schema,rules } from "@ioc:Adonis/Core/Validator";

export const newUserSchema = schema.create({
    username:schema.string({},[
      rules.maxLength(50),
      rules.minLength(3),
      rules.unique({table:'users',column:'username'})
    ]),
    email: schema.string.optional({ trim: true},[
      rules.unique({table:'users',column:'email'}),
      rules.email()
    ]),
    password: schema.string({ escape: true },[
      rules.maxLength(50),
      rules.minLength(6)
    ]),
  })
