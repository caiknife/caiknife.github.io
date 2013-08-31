# encoding: utf-8

module Jekyll
  class PageCategoryListTag < Liquid::Tag
    def render(context)
      html = ""
      categories = context.registers[:site].categories.keys
      
      categories.sort.each do |category|
        posts_in_category = context.registers[:site].categories[category].size
        category_dir = context.registers[:site].config['category_dir']
        #category_url = File.join(category_dir, category.gsub(/_|\P{Word}/, '-').gsub(/-{2,}/, '-').downcase)
        category_url = File.join(category_dir, category.to_url)
        html << "<span class='category-list'><a href='/#{category_url}/'>#{category}</a> (#{posts_in_category})</span>&nbsp;&nbsp;\n"
      end

      html
    end
  end

  class SideCategoryListTag < Liquid::Tag
    def render(context)
      html = ""

      categories = context.registers[:site].categories.keys

      categories.sort.each do |category|
          posts_in_category = context.registers[:site].categories[category].size
          category_dir = context.registers[:site].config['category_dir']
          #category_url = File.join(category_dir, category.gsub(/_|\P{Word}/, '-').gsub(/-{2,}/, '-').downcase)
          category_url = File.join(category_dir, category.to_url)
          html << "<span class='category-list'><a href='/#{category_url}/'>#{category}</a> (#{posts_in_category})</span> \n"
      end
      
      html
    end
  end
end

Liquid::Template.register_tag('page_category_list', Jekyll::PageCategoryListTag)
Liquid::Template.register_tag('side_category_list', Jekyll::SideCategoryListTag)