#=GetNews Class
#This class to update the news of database from `http://physics.seu.edu.cn/`
#==toUtf8 
#Change encode of the page
#==getNews
#Get news information and insert into database

class GetNews
  require 'rubygems'
  require 'nokogiri'
  require 'open-uri'
  require 'rchardet19'
  require 'mysql'

  def toUtf8(_string)
    cd = CharDet.detect(_string)
    if cd.confidence > 0.6
      _string.force_encoding(cd.encoding)
    end
    _string.encode!("utf-8", :undef => :replace, :replace => "?", :invalid => :replace)
    return _string
  end

  def getNews
    con = Mysql.new 'localhost', 'root', '123456', 'seuphysics'
    con.query "SET NAMES utf8"
    base_url = 'http://physics.seu.edu.cn/'
    doc = Nokogiri::HTML(toUtf8(open(base_url).read))
    for i in 1..4
      for j in 1..12
        if i==3
          break
        end
        puts title = doc.css("table#tb#{i}").css('tr')[j].css('td')[1].text
        date = doc.css("table#tb#{i}").css('tr')[j].css('td')[2].text
        content_url = doc.css("table#tb#{i}").css('tr')[j].css('td')[1].css('a')[0]['href']
        content_base = Nokogiri::HTML(toUtf8(open(base_url+content_url).read))
        if !content_base.css('table').css('tr')[2].nil? then
          content = content_base.css('table').css('tr')[2].text
          re = con.query "SELECT * FROM `news` WHERE `title`='#{title}'"
          if !re
            con.query "INSERT INTO `news` (title, content, date) VALUES ('#{title}','#{content}','#{date}')"
          end
        end
      end
      puts "Update Success"
    end
    
    rescue Mysql::Error => e
      puts e.errno
      puts e.error
    
    ensure
      con.close if con
  end
end

test = GetNews.new
test.getNews