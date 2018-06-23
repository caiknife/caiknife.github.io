DOUBAN_PUBLIC_ALBUM_URL_EXP = /http:\/\/www\.douban\.com\/photos\/album\/\d+\//
RENREN_PUBLIC_ALBUM_URL_EXP = /http:\/\/page\.renren\.com\/\w+\/album\/\d+/

class InvalidPicError < Exception
end

class InvalidUrlError < Exception
end

class InvalidAlbumError < Exception
end

class Album
  def initialize(url)
    @current_url = url
    @doc = Nokogiri::HTML(open(@current_url))
    @page_threads = []
    @pic_threads = []
    @download_count = 0;
    @lock = Mutex.new
  end
  
  def download
    begin
      # 获取相册图片数量，在子类中重载
      get_album_pic_count
    rescue InvalidAlbumError => e
      puts e.class.to_s, e.message, e.backtrace
    end
    time_start = Time.now
    puts "You are downloading pics from #{@current_url} ..."
    # 下载，也是需要重载的
    start_downloading
    puts "Downloading pics from #{@current_url} completed ..."
    puts "Costing time #{Time.now - time_start} seconds ..."
  end
  
  class << self
    # 工厂方法，跟前面判断URL的地方有些重复
    def factory(url)
      case 
        when DOUBAN_PUBLIC_ALBUM_URL_EXP =~ url then DoubanAlbum.new(url)
        when RENREN_PUBLIC_ALBUM_URL_EXP =~ url then RenrenAlbum.new(url)
      else 
        raise InvalidUrlError, "The album url #{url} is invalid!"
      end
    end
  end
  
  protected
  
  # 创建相册文件夹
  def create_album_folder(folder_name)
    if not File.exist?(folder_name)
      puts "Creating folder #{folder_name} ..."
      Dir::mkdir(folder_name)
    end
    puts "Downloaidng pics to #{folder_name}"
  end
  
  # 写入单个图片
  def write_file(source, dest)
    stream = open(source) { |f| f.read }
    open(dest, "wb") { |f| f.write(stream) }
  end
  
  # 下载任务，在子类中重载  
  def start_downloading
    
  end
  
  # 获取相册名字，要重载
  def get_folder_name
     
  end
  
  # 获取相册图片数量，要重载
  def get_album_pic_count
    
  end
   
end

# 加载子类
require File.expand_path("../douban_album", __FILE__)
require File.expand_path("../renren_album", __FILE__)