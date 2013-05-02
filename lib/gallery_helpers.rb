# image identifiers are of the format /images/<album_1>/<subalbum_1>/<image_name>/
# album identifier would be /gallery/<album_1>/<subalbum_1>/
# folder identifier /gallery/<album_1>/

require 'exifr'

def create_gallery_structure(folders, albums, images)
  images.each {|image| set_image_attributes(image) }

  add_images_to_albums(images, albums)
  add_albums_to_folders(albums, folders)
end


def add_images_to_albums(images, albums)
  groups = images.group_by { |i| i[:album] }
  groups.each do |album_id, album_images|
    if not albums.one? { |a| a.identifier == album_id }
      raise(ArgumentError, "None or multiple albums with id #{album_id} found.")
    end
    album = albums.select { |a| a.identifier == album_id }.first
    album[:images] = album_images
    album[:sample] = album_images.sample
  end
end

def add_albums_to_folders(albums, folders)
  groups = albums.group_by { |a| a.identifier.gsub(/[^\/]*\/$/, '') }
  groups.select { |folder_id, _| folder_id != '/'  }.each do |folder_id, folder_albums|    
    if not folders.one? { |f| f.identifier == folder_id }
      raise(ArgumentError, "None or multiple folders with id #{folder_id} found.")
    end
    folder = folders.select { |f| f.identifier == folder_id }.first()
    folder[:albums] = folder_albums
    folder[:sample] = folder_albums.sample[:images].sample
  end
end

def set_image_attributes(item)
  item[:type] = 'image'
  item[:original] = get_original_link(item)
  item[:preview] = get_preview_link(item)
  item[:thumbnail] = get_thumbnail_link(item)
  item[:album] = get_album(item)
  item[:created] = EXIFR::JPEG.new(item.raw_filename).date_time 
end

def get_thumbnail_link(image)  
  get_image_link(image, 'thumbnails')
end

def get_preview_link(image)  
  get_image_link(image, 'previews')
end

def get_original_link(image)  
  get_image_link(image, 'originals')
end

def get_image_link(image, type)
  # Here the extension is alrady present since these come from a static datasource
  image.identifier.gsub(/^\/images\//, "/gallery/images/#{type}/").chop 
end

def get_album(image)
  image.identifier.gsub(/^\/images\//, '/gallery/').gsub(/\/[^\/]*\/$/, '/')
end

def sort_images(images)
  images.sort_by { |i| i[:created].to_i }
end

def get_parents(item)
  if item.parent.nil?
    []
  else
    get_parents(item.parent) << item.parent
  end
end

def get_child_folders(item)
  @items.select { |i| i.identifier.start_with?(item.identifier) and i.identifier.count('/') == item.identifier.count('/') - 1 and ( i[:type] == 'folder' or i[:type] == 'album' ) } 
end

def number_of_images(item)
  if item[:type] == 'album'
    item[:images].count
  else
    sum = 0
    item.children.each { |album| sum = sum + number_of_images(album) }        
    sum    
  end  
end

