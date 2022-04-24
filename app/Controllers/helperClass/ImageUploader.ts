import User from "App/Models/User";
import Image from "App/Models/Image";

export default class ImageUploader {

    public async uploadImage(imageFile, request, user: User) {

        console.log("Image à uploader");

        let message: string;
        try {
            await imageFile?.moveToDisk(
                "./images/",
                { contentType: request.header("Content-Type") },
                "local"
            );
            message = `The image file has been successfully saved!`;
            console.log(message);
        } catch (err) {
            message = `An error occured during the save of the image file.\nHere's the details: ${err} `;
            console.log(message);
            return null;
        }

        if (!imageFile.fileName) {
            message = `An error occured during the save of the image file.\nHere's the details: imageFile.fileName is not defined.`;
            console.log(message);
            return null;
        }

        const locationOrigin = new URL(request.completeUrl()).origin;
        // eval(entityType) -> Play is not defined... Why can't we use import aliases in eval? #FIXME

        const newImage = await Image.create({
            name: imageFile.fileName,
            publicPath: `${locationOrigin}/uploads/images/${imageFile.fileName}`,
            relativePath: `/uploads/images/${imageFile.fileName}`,
            creatorId: user.id,
            size: imageFile.size,
            type: imageFile.extname,
            // mimeType: request.header("Content-Type"), // It's getting as multipart/form-data
            mimeType: `${imageFile.fieldName}/${imageFile.extname}`,
        });
        console.log(newImage.publicPath);
        return newImage;
    }

}



