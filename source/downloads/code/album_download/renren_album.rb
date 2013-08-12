class RenrenAlbum < Album
  
  def initialize(url)
    super(url)
    @page_item = 15
  end
  
  protected
  
  def start_downloading
    create_album_folder(get_folder_name)
    (@sum/@page_item + 1).times do |i|
      @page_threads << Thread.new do        
        url = @current_url+"?curpage="+i.to_s
        page = Nokogiri::HTML(open(url))
        puts "Analyzing url #{url} ..."
        
        page.css("td.photoPan a").each do |link|
          @pic_threads << Thread.new do 
            url = @current_url.scan(/http:\/\/[\w+\.]+/)[0].to_s + link.attribute("href").to_s
            source = open(url).read.scan(/"large":"(http:[\\\/\w.]+\.jpg)"/)[0][0].gsub(/\\/, "")
            dest = /[\w_]+\.(jpg|jpeg|png)/i.match(source).to_s
            write_file(source, File.join(@folder_name, dest)) 
            @lock.synchronize { puts "Created file #{dest} to #{@folder_name} from #{source} ... #{@download_count += 1}/#{@sum}" }
          end
        end
        
      end
    end
    
    @page_threads.each { |t| t.join }
    @pic_threads.each { |t| t.join }
  end
  
  def get_folder_name
    @folder_name = /\d+$/.match(@current_url).to_s + "_" + @doc.css("div.pager-top span h3").inner_text.to_s
    @folder_name = Iconv.iconv("GB2312", "UTF-8", @folder_name).to_s if on_windows?
  end
  
  def get_album_pic_count
    re = @doc.css("div.pager-top span").inner_text.to_s.scan(/共(\d+)张/)
    raise InvalidAlbumError, "This is an invalid ablum!" if re.nil?
    @sum = re[0][0].to_i
  end
end