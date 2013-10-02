#coding: utf-8

module Jekyll

  class FancyboxTag < Liquid::Tag
    def initialize(tag_name, markup, tokens)
      #  /(?<filename>\S+)(?:\s+(?<thumb>\S+))?(?:\s+(?<title>.+))?/i
      if /(?<filename>\S+)(?:\s+(?<title>.+))?/i =~ markup
        @filename = filename
        @title = title
      end
    end

    def render(context)
      if @filename
        "<a href=\"#{@filename}\" title=\"#{@title}\" class=\"fancybox\"><img src=\"#{@filename}\" alt=\"#{@title}\" /></a>"
      else
        "Error. Expect: {% fancybox filename title %}"
      end
    end

  end
  
end

Liquid::Template.register_tag('fancybox', Jekyll::FancyboxTag)