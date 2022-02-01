import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Audio from "App/Models/Audio";
import Scene from "App/Models/Scene";
import Line from "App/Models/Line";
import {
  LucidModel,
  ModelAdapterOptions,
  ModelQueryBuilderContract,
} from "@ioc:Adonis/Lucid/Orm";
export default class AudiosController {
  /*  protected async preloadExtractor(
    query: (
      m?: ModelAdapterOptions
    ) => ModelQueryBuilderContract<typeof Audio, Audio>,
    query_args,
    chain
  ) {
    // audios.lines.scene
    console.log(query());
    return;
    return query(query_args).preload("line", (linesQuery) => {
      linesQuery.preload("scene");
    });
  }*/

  protected async preloadSetter(
    query,
    preload_arg: any,
    query_args: ModelAdapterOptions = {}
  ) {
    return new Promise(async (resolve) => {
      return await query(query_args).preload(preload_arg, (preloadedQuery) => {
        return resolve(preloadedQuery);
      });
    });
  }

  protected async preoladUnchainnerDep(chain: string, query_args = {}) {
    // audios.lines.scene
    const preload_args = chain.split(".");
    let dummy: ModelQueryBuilderContract<any, any> | undefined;
    return preload_args.reduce(
      async (acc: ModelQueryBuilderContract<any, any>, curr: string) => {
        if (acc) {
          return await this.preloadSetter(acc, curr, query_args);
        } else {
          console.log("adşklsfjasdşlfksadş", Audio.query);
          return await this.preloadSetter(Audio.query, curr, query_args);
        }
      },
      dummy
    );
  }

  protected async preoladUnchainner(query, depth, query_args = {}) {
    // recursion
    if (!depth) return query;
  }
  public async index({ view }: HttpContextContract) {
    /*const audios = await Audio.query().preload("line", async (linesQuery) => {
      await linesQuery.preload("scene", async (scenesQuery) => {
        await scenesQuery.preload("play");
      });
    });*/
    console.log(await Audio.query());
    // console.log(await this.preoladUnchainnerDep("lines.scene", {}));
    // Line - One2Many Preloading
    // audios[].lines[].scene
    /*
      .preload("line", async (linesQuery) => {
        // console.log(await linesQuery); // why do we need to await a function parameter
        linesQuery.preload("character", async (charactersQuery) => {
          await charactersQuery;
          console.log(await charactersQuery.where("gender", "male"));
        });
      });
    */ return;
    audios.map(async (e) => {
      await Line.query();
      const query = await this.promisePreload(e.line.characterId, "scene");
      console.log(query?.name, query?.description);
    });
  }

  public async create({}: HttpContextContract) {}

  public async store({}: HttpContextContract) {}

  public async show({}: HttpContextContract) {}

  public async edit({}: HttpContextContract) {}

  public async update({}: HttpContextContract) {}

  public async destroy({}: HttpContextContract) {}
}
