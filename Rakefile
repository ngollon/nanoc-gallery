require 'yaml'
require 'pp'
require 'exifr'

def load_config
  YAML.load_file('/srv/nanoc-gallery/nanoc.yaml')
end

desc "Shows current site config"
task :show_config do
  pp load_config()
end

desc "Updates an album to contain the image links"
task :update, [:id] do |t, args| 
  id = args[:id]
  config = load_config() 
  image_directory = config['image_directory']
  abort "image_directory not defined in nanoc.yaml" if image_directory.nil?
  
  update(image_directory, id)
end

desc "Updates all albums and folders"
task :update_all do
  config = load_config()
  image_directory = config['image_directory']

  update(image_directory, '/' )

  Dir.glob(image_directory + '/**/*/').each do |dir|
    l1 = image_directory.length
    l2 = dir.length - l1
    id = dir[l1, l2]
    update(image_directory, id) 
  end
end

def update(image_directory, id)
  id_directory = File.join(image_directory, id)
  abort "directory #{id_directory} does not exist" unless Dir.exists?(id_directory)
  
  content_directory = 'content/gallery'
  index_file = File.join(content_directory, id, 'index.yaml')
  abort "File #{index_file} does not exist" unless File.exists?(index_file)

  if Dir[id_directory + '/*/'].any?
    puts "Updating folder #{id}" if verbose == true
    update_folder(id_directory, index_file)
  else
    puts "Updating album #{id}" if verbose == true
    update_album(id_directory, index_file)
  end
end

def update_album(album_directory, index_file) 
  index = YAML.load_file(index_file)
  index['images'] = [] if index['images'].nil?
 
  existing_images = get_images(album_directory)

  index['images'].reject! { |i| existing_images.index { |ei| ei['id'] == i['id'] }.nil? } 
  existing_images.select { |ei| index['images'].index { |i| i['id'] == ei['id'] }.nil? }.each do |new_image|
    new_image['date'] = get_date(File.join(album_directory, new_image['id']))
    index['images'] << new_image
  end

  index['images'].sort! { |a,b| a['date'] <=> b['date'] }
  File.open(index_file, 'w+') do |out|
    YAML.dump(index, out)
  end
end

def update_folder(folder_directory, index_file)
  index = YAML.load_file(index_file)
  index['children'] = [] if index['children'].nil?
 
  existing_children = get_subdirectories(folder_directory)

  index['children'].reject! { |i| existing_children.index { |ei| ei['id'] == i['id'] }.nil? } 

  last = index['children'].max_by { |i| i['sort_order'] } 
  max = 0
  max = last['sort_order'] unless last.nil?

  existing_children.select { |ei| index['children'].index { |i| i['id'] == ei['id'] }.nil? }.each do |new_child|
    new_child['sort_order'] = max + 1
    max = max + 1
    index['children'] << new_child
  end

  index['children'].sort! { |a,b| a['sort_order'] <=> b['sort_order'] }
  File.open(index_file, 'w+') do |out|
    YAML.dump(index, out)
  end
end


def get_images(directory)
  Dir.glob(directory + '/*.jpg').collect { |i| { 'id' => File.basename(i), 'date' => nil } }
end

def get_subdirectories(directory)
  Dir.glob(directory + '/*/').collect { |d| { 'id' => File.basename(d), 'sort_order' => nil } }
end

def get_date(image)
  EXIFR::JPEG.new(image).date_time.inspect  
end
