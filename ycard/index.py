# -*- coding:utf-8 -*-
import requests
import re
from bs4 import BeautifulSoup

session = requests.Session()
headers = {
	"User-agent":"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36"
}
firstRequest = session.get("http://my.seu.edu.cn/login.portal")
firstCookie = dict(JSESSIONID = firstRequest.cookies['JSESSIONID'])

loginInfo = {
	"Login.Token1":"YOUR-CARD-NUM",
	"Login.Token2":"YOUR-PASSWORD",
	"goto":"http://my.seu.edu.cn/loginSuccess.portal",
	"gotoOnFail":"http://my.seu.edu.cn/loginFailure.portal"
}

reponse = session.post("http://my.seu.edu.cn/userPasswordValidate.portal",data = loginInfo, headers = headers, cookies= firstCookie)
secondCookie = dict(iPlanetDirectoryPro = reponse.cookies['iPlanetDirectoryPro'])
secondRequest = requests.get('http://allinonecard.seu.edu.cn/ecard/dongnanportalHome.action', cookies = secondCookie)

headers = {
	"User-agent":"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36",
	"Cookie":"JSESSIONID=" + secondRequest.cookies['JSESSIONID'] + "; iPlanetDirectoryPro=" + reponse.cookies['iPlanetDirectoryPro']
}

thirdRequest = requests.get('http://allinonecard.seu.edu.cn/accountcardUser.action',headers = headers)

firstSoup = BeautifulSoup(thirdRequest.text.encode("utf-8"))
userid = firstSoup.find_all(attrs={"align": "left"})[2].text.encode('utf-8')

fliter = ['九龙湖', '手持考', '行政楼', '网络中']
times = 0
for time in range(1, 1000):
  userinfo = {
    'account':userid,
    'startDate':'',
    'endDate':'',
    'pageno':time
  }

  forthRequest = requests.post("http://allinonecard.seu.edu.cn/mjkqBrows.action",headers = headers, data = userinfo)

  secondSoup = BeautifulSoup(forthRequest.text)
  re_select = secondSoup.find_all(attrs={"class": re.compile("listbg")})
  if not re_select:
    break;
  else:
    for i in re_select:
      item_list = list(i.children)
      if not item_list[9].contents[0][0:3].encode('utf-8') in fliter:
        print item_list[1].contents[0]
        print item_list[9].contents[0].encode('utf-8')
        times += 1
print times