<?php

class AviaryUpload extends Controller
{
    private static $allowed_actions = array(
        'update',
        'localupdate',
    );

    public function update() {
        if(
            $this->canEdit() &&
            ($newFile = $this->request->requestVar('url')) &&
            ($imageID = $this->request->requestVar('imageID')) &&
            ($image = Image::get()->byID(Convert::raw2sql($imageID)))
        ) {
            $imageData = file_get_contents($newFile);
            $path = $image->getFullPath();

            // if we have a new file + old path, overwrite old image wiith new image.
            if($imageData && $path) {
                file_put_contents($path, $imageData);
                $image->forceChange();
                $image->deleteFormattedImages();
                $image->write();

                // return new thumbnail
                $formattedImage = $image->getFormattedImage(
                    'SetWidth',
                    Config::inst()->get('Image', 'asset_preview_width')
                );
                $thumbnail = $formattedImage ? $formattedImage->URL : '';

                if($this->request->isAjax()) {
                    $this->request->addHeader('Content-type', 'application/json');
                    return json_encode([
                        'thumbnail' => $thumbnail
                    ]);
                }

                return $thumbnail;
            }
        }
    }

    public function localupdate() {
        if(
            $this->canEdit() &&
            ($imageDataString = $this->request->requestVar('imageData')) &&
            ($imageID = $this->request->requestVar('imageID')) &&
            ($image = Image::get()->byID(Convert::raw2sql($imageID)))
        ) {
            //$imageDataString = file_get_contents($newFile);
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageDataString));
            $path = $image->getFullPath();

            // if we have a new file + old path, overwrite old image wiith new image.
            if($imageData && $path) {
                // Do we need to validate it the filetype is still the same as the original?
                //$f = finfo_open();
                //$mime_type = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
                //finfo_close($f);

                file_put_contents($path, $imageData);
                $image->forceChange();
                $image->deleteFormattedImages();
                $image->write();

                // return new thumbnail
                $formattedImage = $image->getFormattedImage(
                    'SetWidth',
                    Config::inst()->get('Image', 'asset_preview_width')
                );
                $thumbnail = $formattedImage ? $formattedImage->URL : '';

                if($this->request->isAjax()) {
                    $this->request->addHeader('Content-type', 'application/json');
                    return json_encode([
                        'thumbnail' => $thumbnail
                    ]);
                }

                return $thumbnail;
            }
        }
    }

    public function canEdit($member = null) {
        if(!$member) $member = Member::currentUser();

        return Permission::checkMember($member, array('CMS_ACCESS_AssetAdmin', 'CMS_ACCESS_LeftAndMain'));
    }
}