---
combined: true
---
- @items.select{ |i| i.identifier.start_with?('/javascript/') }.each do |i|
  = i.raw_content
