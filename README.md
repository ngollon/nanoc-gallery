nanoc-gallery
=============
Work in progress
----------------

This is a work in progress. Some gears are in place, but most of them are rather rough. This is planned to be the nanoc configuration I use for my private photo gallery, so if you are looking for something similar, take a look.
There will be a demo site some day.

Configuration
-------------

- Set site name in nanoc.yaml
- Move images to /images/originals. The directory structure represents the albums (with sub-albums). No directory may contain both images and subdirectories.
- Run prepare_images.rb to create preview and thumbnail images.
- Make /content/gallery contain the same directory structure and add an index.md everywhere. Directories containing images should be marked 'album' in the YAML front matter, others 'folder'.
- Link to /images as /output/gallery/images

Structure
---------

| Directory         | Purpose 
| ----------------- | ------- 
| /content          | Your main nanoc site 
| /content/gallery  | The album structure for the gallery 
| /images/          | The images, in the same structure as /content/gallery 
| /static/          | Some static files 


Giants
------

This project uses [nanoc][nanoc], [jQuery] [jquery], [photobox] [photobox] and [plupload] [plupload]

[nanoc]: http://nanoc.ws
[plupload]: http://www.plupload.com/
[jquery]: http://jquery.com
[photobox]: http://dropthebit.com/500/photobox-css3-image-gallery-jquery-plugin/

