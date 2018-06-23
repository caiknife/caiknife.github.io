class DoubanAlbum < Album
  
  def initialize(url)
    super(url)
    @page_item = 18
  end
  
  protected
  
  def start_downloading
    # 创建文件夹
    create_album_folder(get_folder_name)
    
    # 分页下载
    (@sum/@page_item + 1).times do |i|
      
      # 多线程下载
      @page_threads << Thread.new do
        url = @current_url + "?start=" + (i*@page_item).to_s
        page = Nokogiri::HTML(open(url))
        puts "Analyzing url #{url} ..."
        
        # 页内下载
        page.css(".photo_wrap a img").each do |img|
          
          # 每一页内也要进行多线程下载
          @pic_threads << Thread.new do
            source = img.attribute("src").to_s.sub(/thumb/, "photo")
            dest = /[\w_]+\.(jpg|jpeg|png)/i.match(source).to_s
            write_file(source, File.join(@folder_name, dest)) 
            
            # 在命令行内同步输出
            @lock.synchronize { puts "Created file #{dest} to #{@folder_name} from #{source} ... #{@download_count += 1}/#{@sum}" }
          end
        end
      end 
    end
    
    # 线程同步
    @page_threads.each { |t| t.join }
    @pic_threads.each { |t| t.join }
  end
  
  def get_folder_name
    @folder_name = /\d+\/$/.match(@current_url).to_s.sub(/\//, "") + "_" + @doc.css('h1').inner_text
    # Win下要转码
    @folder_name = Iconv.iconv("GB2312", "UTF-8", @folder_name).to_s if on_windows?
  end
  
  def get_album_pic_count
    re = @doc.css('.wr span.pl')
    raise InvalidAlbumError, "This is an invalid ablum!" if re.empty?
    @sum = re.inner_text.to_i
  end
  
end