import { schema,rules } from "@ioc:Adonis/Core/Validator";

export const newGroupSchema = schema.create({
    name: schema.string({ trim: true},[
      rules.minLength(1)
    ]),
    description: schema.string({ escape: true },[
      rules.maxLength(255)
    ]),
  })
