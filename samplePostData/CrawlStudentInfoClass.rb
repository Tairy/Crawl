#CrawlStudentInfo Class 
#This class can get information of SEU 
# encoding: UTF-8

class CrawlStudentInfo
  require 'rubygems'
  require 'nokogiri'
  require 'open-uri'
  require 'rchardet19'
  require 'mysql'
  
  #attr_accessor :course_id,  :place_id

  def initialize(year, card_num)
    @url = "http://xk.urp.seu.edu.cn/jw_service/service/stuCurriculum.action?queryStudentId=#{card_num}&queryAcademicYear=#{year}"
    @con = Mysql.new 'localhost', 'root', '123456', 'seuphysics'
    @con.query "SET NAMES utf8"
    @userinfo = {}
    @courseinfo = {}
  end

  def crawSite
    @doc = Nokogiri::HTML(open(@url).read)
  end

  def getUserInfo
    @userinfo["college"] = @doc.css("td[align='left']")[0].text.split("]")[1]
    @userinfo["specialfic"] = @doc.css("td[align='left']")[1].text.split("]")[1]
    @userinfo["study_num"] = @doc.css("td[align='left']")[2].text.split(":")[1]
    @userinfo["card_num"] = @doc.css("td[align='left']")[3].text.split(":")[1]
    @userinfo["name"] = @doc.css("td[align='left']")[4].text.split(":")[1]
  end

  def getCourseInfo
    for i in 1..100
      if @doc.css('table.tableline')[0].css('td')[5*i+1].text.length != 1
        course_name = @doc.css('table.tableline')[0].css('td')[5*i+1].text
        manager = @doc.css('table.tableline')[0].css('td')[5*i+2].text
        score = @doc.css('table.tableline')[0].css('td')[5*i+3].text
        week = @doc.css('table.tableline')[0].css('td')[5*i+4].text
        @courseinfo["#{course_name}"] = {"manager" => "#{manager}","score" => "#{score}","week" => "#{week}"}
        saveCourseInfo(course_name, score, manager, week)
      else
        break
      end
    end
  end

  def getClassInfo
    for part in [8, 19, 30]
      for times in [0, 6]
        for day in part..part+4
          morning = @doc.css('table.tableline')[1].css('td')[day]
          #get course id
          course_name_temp = morning.children[times].content if !morning.children[times].nil?
          #has course
          if course_name_temp.length > 1
            getCourseIdByCourseName(course_name_temp)
            #get time id
            aliases_temp = morning.children[times+2].content.split(']')[1][0..-2] if !morning.children[times+2].nil?
            getTimeIdByAliases(aliases_temp)
            if @time_id.nil?
              addTime(aliases_temp)
            end
            #get place id
            place_temp = morning.children[times+4].content if !morning.children[times+4].nil?
            if place_temp[2] == ')'
              @append = place_temp.split(')')[0][1]
              place_temp = place_temp.split(')')[1]
            else
              @append = ""
            end
            getPlaceIdByPlaceName(place_temp)
            if @place_id.nil?
              addPlace(place_temp)
            end

            if part == 8
              addUserActivity(day-7)
            elsif part == 19
              addUserActivity(day-18)
            elsif part == 30
              addUserActivity(day-29)
            end

          end
        end
      end
    end
  end

  def getCourseIdByCourseName(coursename)
    #puts "SELECT `id` FROM `course` WHERE `name`='#{coursename}' AND `score`='#{@courseinfo[coursename]['score']}' AND `manager`='#{@courseinfo[coursename]['manager']}' AND `week`='#{@courseinfo[coursename]['week']}'"
    rs = @con.query("SELECT `id` FROM `course` WHERE `name`='#{coursename}' AND `score`='#{@courseinfo[coursename]['score']}' AND `manager`='#{@courseinfo[coursename]['manager']}' AND `week`='#{@courseinfo[coursename]['week']}'")
    rs.each_hash do |c|
      @course_id=c['id']
    end
  end

  def getTimeIdByAliases(aliases)
    rs = @con.query("SELECT `id` FROM `time` WHERE `aliases`='#{aliases}'")
    rs.each_hash do |t|
      @time_id=t['id']
    end
  end

  def getPlaceIdByPlaceName(place)
    rs = @con.query("SELECT `id` FROM `place` WHERE `place_name`='#{place}'")
    rs.each_hash do |p|
      @place_id = p['id']
    end
  end

  def addTime(aliases)
    start = aliases.split('-')[0]
    over = aliases.split('-')[1]
    start_time = @con.query("SELECT `start_time` FROM `class_time` WHERE `id`='#{start}'").fetch_row[0]
    last_time = (over.to_i - start.to_i)*45 + 45
    @con.query("INSERT INTO `time` (`last_time`, `start_time`, `creater_id`, `aliases`) VALUES ('#{last_time}', '#{start_time}', '1', '#{start}-#{over}')")
    getTimeIdByAliases(aliases)
  end

  def addPlace(place)
    @con.query("INSERT INTO `place` (`place_name`) VALUES ('#{place}')")
    getPlaceIdByPlaceName(place)
  end

  def addUserActivity(day)
    @con.query("INSERT INTO `useractivity` (`user_id`,`time_id`,`course_id`,`place_id`,`day`,`append`) VALUES ('#{@userinfo['card_num']}','#{@time_id}','#{@course_id}','#{@place_id}','#{day}','#{@append}')")
    @time_id = nil
    @place_id = nil
  end

  def saveUserInfo
    rs = @con.query("SELECT * FROM `user` WHERE `card_num`='#{@userinfo['card_num']}'")
    if rs.fetch_row.nil?
      @con.query("INSERT INTO `user` (`card_num`,`study_num`,`name`,`college`,`specialfic`) VALUES ('#{@userinfo['card_num']}','#{@userinfo['study_num']}','#{@userinfo['name']}','#{@userinfo['college']}','#{@userinfo['specialfic']}')")
    end
  end

  def saveCourseInfo(course_name, score, manager, week)
    re = @con.query("SELECT * FROM `course` WHERE `name`='#{course_name}' AND `score`='#{score}' AND `manager` = '#{manager}' AND `week` = '#{week}'")
    if re.fetch_row.nil?
      @con.query("INSERT INTO `course` (`name`,`manager`,`score`,`week`) VALUES ('#{course_name}','#{manager}','#{score}','#{week}')")
    end
  end

  def getCourseNameById(course_id)
    rs = @con.query("SELECT `name` FROM `course` WHERE `id` = '#{course_id}'")
    rs.each_hash do |c|
      return c['name']
    end
  end

  def getPlaceNameById(place_id)
    rs = @con.query("SELECT `place_name` FROM `place` WHERE `id`='#{place_id}'")
    rs.each_hash do |c|
      return c['place_name']
    end
  end

  def getTimeAliasesById(time_id)
    rs = @con.query("SELECT `aliases` FROM `time` WHERE `id`='#{time_id}'")
    rs.each_hash do |c|
      return c['aliases']
    end
  end

  def showUserCurriculum(cardnum)
    rs = @con.query("SELECT * FROM `useractivity` WHERE `user_id`='#{cardnum}'")
    rs.each_hash do |c|
      time = getTimeAliasesById(c['time_id'])
      place = getPlaceNameById(c['place_id'])
      course = getCourseNameById(c['course_id'])
      # print "星期"
      # print "#{c['day']}"
      # print "第"
      # print "#{time}"
      # print "节在"
      # print "#{place}"
      # print "上"
      # print "#{course}"
      # print "\n"
    end
  end

  def main
    crawSite
    getUserInfo
    saveUserInfo
    getCourseInfo
    getClassInfo
    #showUserCurriculum("213111516")


    rescue Mysql::Error => e
    puts e.errno
    puts e.error
  
    ensure
      @con.close if @con
  end
end

for cardnum in 213111352..213114999
  test = CrawlStudentInfo.new('13-14-2', cardnum)
  test.main
  puts cardnum
end