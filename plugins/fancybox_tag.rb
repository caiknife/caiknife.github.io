#coding: utf-8

module Jekyll

  # Usage:
  # {% fancybox @filename [thumb:@thumb] [@title] %}

  class FancyboxTag < Liquid::Tag
    def initialize(tag_name, markup, tokens)
      #  /(?<filename>\S+)(?:\s+(?<thumb>\S+))?(?:\s+(?<title>.+))?/i
      #  /(?<filename>\S+)(?:\s+(?<title>.+))?/i
      if /(?<filename>\S+)(?:\s+thumb:(?<thumb>\S+))?(?:\s+(?<title>.+))?/i =~ markup
        @filename = filename
        @thumb = thumb
        @title = title
      end
    end

    def render(context)
      if @filename
        "<a href=\"#{@filename}\" title=\"#{@title}\" class=\"fancybox\"><img src=\"#{thumb_for(@filename, @thumb)}\" alt=\"#{@title}\" /></a>"
      else
        "Error. Usage: {% fancybox @filename [thumb:@thumb] [@title] %}"
      end
    end

    def thumb_for(filename, thumb)
      if thumb.nil?
        filename
      else
        thumb
      end
    end

  end
  
end

Liquid::Template.register_tag('fancybox', Jekyll::FancyboxTag)