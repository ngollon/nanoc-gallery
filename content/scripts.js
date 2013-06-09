---
combined: true
---
- @items.select{ |i| i.identifier.start_with?('/javascript/') }.sort_by { |i| i.identifier }.each do |i|
  = i.raw_content
