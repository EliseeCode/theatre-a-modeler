import BaseSeeder from "@ioc:Adonis/Lucid/Seeder";
import Image from "App/Models/Image";
export default class ImageSeeder extends BaseSeeder {
  public async run() {
    await Image.createMany([
      {
        name: "Henry saluts",
        publicPath:
          "https://cdn2.rsc.org.uk/sitefinity/images/productions/productions-2010-2014/henry-iv-part-1/henry_iv_part_1_production_photos_2014_kwame_lestrade_c_rsc_8189.tmb-img-912.jpg?sfvrsn=bc3b2221_1",
        relativePath:
          "/images/productions/productions-2010-2014/henry-iv-part-1/henry_iv_part_1_production_photos_2014_kwame_lestrade_c_rsc_8189.tmb-img-912.jpg?sfvrsn=bc3b2221_1",
        creatorId: 3,
        size: 345,
        type: "jpeg",
        mimeType: "image/jpeg",
      },
      {
        name: "Vladimir asks",
        publicPath:
          "https://thesamuelbeckettsociety.files.wordpress.com/2012/10/post_image_test.png",
        relativePath: "/2012/10/post_image_test.png",
        creatorId: 3,
        size: 532,
        type: "png",
        mimeType: "image/png",
      },
    ]);
  }
}
