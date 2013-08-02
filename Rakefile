require 'yaml'
require 'pp'
require 'exifr'
require 'colorize'
require 'fileutils'

@config = YAML.load_file('/srv/nanoc-gallery/nanoc.yaml')

desc "Shows current site config"
task :show_config do
  pp @config
end


desc "Updates everything if an update is required"
task :update_everything do
  trigger = File.join(@config['cache_directory'], '.updated')
  if File.exists?(trigger) and File.mtime(trigger) + 5 * 60 < Time.now
    Rake::Task['cache_all_images'].invoke
    Rake::Task['update_all_albums'].invoke
    `nanoc 2>&1 /dev/null`
    File.unlink(trigger)
  end
end

desc "Updates an album to contain the image links"
task :update_album, [:id] do |t, args| 
  id = args[:id]
  image_directory = @config['image_directory']
  abort "image_directory not defined in nanoc.yaml" if image_directory.nil?
  
  update(image_directory, id)
end

desc "Updates all albums and folders"
task :update_all_albums do
  image_directory = @config['image_directory']

  update(image_directory, '/' )

  Dir.glob(image_directory + '/**/*/').each do |dir|
    l1 = image_directory.length
    l2 = dir.length - l1
    id = dir[l1, l2]
    update(image_directory, id) 
  end
end

desc "Create thumbnail and preview image"
task :cache_image, [:image] do |t,args|
  image = File.expand_path(args[:image])

  abort "File not found #{image}" unless File.exists?(image)

  create_preview(image)
  create_thumbnail(image)
end

desc "Create thumbnails and previews for all images"
task :cache_all_images do
  images = Dir[@config['image_directory'] + '/**/*'].reject {|fn| File.directory?(fn) }
  images.each do |i|
    create_thumbnail(i)
    create_preview(i)
  end
end

def update(image_directory, id)
  id_directory = File.join(image_directory, id)
  abort "directory #{id_directory} does not exist" unless Dir.exists?(id_directory)
  
  content_directory = 'content/gallery'
  index_file = File.join(content_directory, id, 'index.yaml')
  abort "File #{index_file} does not exist" unless File.exists?(index_file)

  if not Dir[id_directory + '/*/'].any?
    update_album(id_directory, index_file)
  end
end

def update_album(album_directory, index_file) 
  index = YAML.load_file(index_file)
  index['images'] = [] if index['images'].nil?
 
  old_count = index['images'].count

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
  total = index['images'].count
  puts "Updated album #{album_directory}. Total: #{total}. New: #{total - old_count}" if verbose == true 
end


def get_images(directory)
  Dir.glob(directory + '/*.jpg', File::FNM_CASEFOLD).collect { |i| { 'id' => File.basename(i), 'date' => nil } }
end

def get_date(image)
  date = EXIFR::JPEG.new(image).date_time
  if date.nil?
    date = File.mtime(image)
  end
  if date.nil?
    date = DateTime.now
  end
  date.inspect
end

def get_directory(path)
    path.gsub(/[^\/]*$/, '')
end

def convert_and_crop(original, target, size)
  call = "convert -auto-orient -resize #{size.inspect} -gravity Center -crop #{(size+"+0+0").inspect} +repage #{original.inspect} #{target.inspect}"
  system(call)
end

def convert(original, target, size)
  call = "convert -auto-orient -resize #{size.inspect} #{original.inspect} #{target.inspect}"
  system(call)
end

def create(image, directory, size, mode)
  cache = File.join(@config['cache_directory'], directory)
  target = image.gsub(/^#{Regexp.escape(@config['image_directory'])}/, "#{cache}")
  dirname = File.dirname(target)
  if not Dir.exists?(dirname)
    puts "Creating #{dirname}".green if verbose == true
    FileUtils.mkdir_p(dirname)
  end
  if File.exists?(target) 
    puts "Skipping #{target}".yellow if verbose == true
  else
    puts "Creating #{target}".green if verbose == true
    if mode == :crop
      convert_and_crop(image, target, size)
    else
      convert(image, target, size)
    end    
  end
end

def create_thumbnail(image)
  create(image, 'thumbnails', '160x160^', :crop)
end

def create_preview(image)
  create(image, 'previews', '800x800>', :default)
end
