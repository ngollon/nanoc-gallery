# All files in the 'lib' directory will be loaded
# before nanoc starts compiling.

def link_to(text, target, attributes={})
  # Find path
  if target.is_a?(String)
    path = target
  else
    path = target.path
    raise RuntimeError, "Cannot create a link to #{target.inspect} because this target is not outputted (its routing rule returns nil)" if path.nil?
### Remove internal, these paths are not accessible from outside
    path = strip_internal(path)
  end

  # Join attributes
  attributes = attributes.inject('') do |memo, (key, value)|
    memo + key.to_s + '="' + h(value) + '" '
  end

  # Create link
  "<a #{attributes}href=\"#{h path}\">#{text}</a>"
end


def relative_path_to(target)
  require 'pathname'

  # Find path
  if target.is_a?(String)
    path = target
  else
    path = target.path
    if path.nil?
      raise "Cannot get the relative path to #{target.inspect} because this target is not outputted (its routing rule returns nil)"
    end
  end

  # Handle Windows network (UNC) paths
  if path.start_with?('//') || path.start_with?('\\\\')
    return path
  end

  # Get source and destination paths
  dst_path   = Pathname.new(strip_internal(path))
  if @item_rep.path.nil?
    raise "Cannot get the relative path to #{path} because the current item representation, #{@item_rep.inspect}, is not outputted (its routing rule returns nil)"
  end
  src_path   = Pathname.new(strip_internal(@item_rep.path))
  
# Calculate the relative path (method depends on whether destination is
  # a directory or not).
  if src_path.to_s[-1,1] != '/'
    relative_path = dst_path.relative_path_from(src_path.dirname).to_s
  else
    relative_path = dst_path.relative_path_from(src_path).to_s
  end

  # Add trailing slash if necessary
  if dst_path.to_s[-1,1] == '/'
    relative_path << '/'
  end

  # Done
  relative_path
end

def strip_internal(path)
  path.gsub(/\/internal\//,'/')
end
