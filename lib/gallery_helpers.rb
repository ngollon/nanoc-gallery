# album identifier would be /gallery/<album_1>/<subalbum_1>/
# folder identifier /gallery/<album_1>/
require 'pp'

def create_gallery_structure(folders, albums)
  albums.each { |a|  add_images_to_album(a) }
  add_albums_to_folders(albums, folders)
end


def add_images_to_album(album)
  album_images = album[:images].each { |i| add_image_properties(album, i) }

  album[:images] = album_images
  album[:sample] = album_images.sample
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

def add_image_properties(album, image)
  image[:original] = get_original_link(album, image[:id])
  image[:preview] = get_preview_link(album, image[:id])
  image[:thumbnail] = get_thumbnail_link(album, image[:id])
end

def get_thumbnail_link(album, id)  
  get_image_link(album, id, 'thumbnails')
end

def get_preview_link(album, id)  
  get_image_link(album, id, 'previews')
end

def get_original_link(album, id)  
  get_image_link(album, id, 'originals')
end

def get_image_link(album, id, type)
  "/gallery/images/#{type}/#{album.identifier.gsub(/^\/gallery\//, '')}#{id}"
end

def get_album(image)
  image.identifier.gsub(/^\/images\//, '/gallery/').gsub(/\/[^\/]*\/$/, '/')
end

def sort_images(images)
  images.sort_by { |i| i[:date].to_i }
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

def number_of_images_formatted(item)
  number = number_of_images(item)
  if(number == 1)
    "1 Bild"
  else
    "#{number} Bilder"
  end
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

