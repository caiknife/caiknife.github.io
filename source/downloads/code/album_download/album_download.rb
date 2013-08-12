#!/usr/bin/ruby
require "rubygems"
require "nokogiri"
require "open-uri"
require "thread"
require "iconv"
require "rbconfig"
require File.expand_path("../album", __FILE__)

# Win环境？
def on_windows?
  if Config::CONFIG["host_os"] == "mingw32"
    true
  else
    false
  end
end

# 任务类
class AlbumDownload
  def initialize
    begin
      get_cmd_params
    rescue ArgumentError => e
      puts e.class.to_s, e.message, e.backtrace
    end
    @verified_urls = []
  end
  
  def go
    begin
      verify_urls
      @verified_urls.each do |url|
        Album.factory(url).download
      end
    rescue InvalidUrlError => e
      puts e.class.to_s, e.message, e.backtrace
      exit
    end
  end
  
  def download
    
  end
  
  protected 
  
  def get_cmd_params
    # 没有命令行参数，抛出异常
    raise ArgumentError, "Please input an album url!" if ARGV.empty?
    @args = ARGV
  end
  
  def verify_urls
    # 如果是有效链接，放入@verify_urls变量中
    # 命令行可以接收多个URL，但是实际用起来还是每次只输入一个就好。
    @args.each do |arg|
      @verified_urls << arg if valid_album?(arg)
    end
    raise InvalidUrlError, "Please input an valid album url!" if @verified_urls.empty?
  end
  
  def valid_album?(url)
    # 如果是豆瓣相册链接或者是人人相册链接
    douban_album?(url) || renren_album?(url)
  end
  
  def douban_album?(url)
    # 豆瓣相册URL正则验证
    DOUBAN_PUBLIC_ALBUM_URL_EXP =~ url
  end
  
  def renren_album?(url)
    # 人人相册URL正则验证
    RENREN_PUBLIC_ALBUM_URL_EXP =~ url
  end
  
end

if __FILE__ == $0
  AlbumDownload.new.go
end