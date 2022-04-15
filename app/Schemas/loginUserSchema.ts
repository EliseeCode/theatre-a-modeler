import { schema, rules } from "@ioc:Adonis/Core/Validator";

export const loginUserSchema = schema.create({
  // username:schema.string({},[
  //   rules.maxLength(50),
  //   rules.minLength(3),
  //   rules.unique({table:'users',column:'username'})
  // ]),
  loginId: schema.string({ trim: true }, [
    rules.required(),
  ]),
  password: schema.string({ escape: true }, [
    rules.maxLength(50),
    rules.minLength(4)
  ]),
  remember: schema.boolean()
})
