# -*- coding:utf-8 -*-
import requests
import re
from bs4 import BeautifulSoup

from pymongo import MongoClient

db = MongoClient().renrendata

class StoreRenrenPersion(object):
    def __init__(self, email, password):
        self.login = RenrenLogin(email,password)

        self.friend = RenrenFriendList(self.login.session, self.login.uid)
        # self.persioninfo = RenrenPersionInfo(self.login.session, "400222823")
        dbstored = db.profile.find_one({"renren_id": self.login.uid})
        if not dbstored:
            self.persioninfo = RenrenPersionInfo(self.login.session, self.login.uid)
            self.persioninfo.profile['friend_ids'] = self.friend.friends_uids
            self.persioninfo.profile['renren_id'] = self.login.uid
            db.profile.insert(self.persioninfo.profile)
        
        for friend in self.friend.friends_uids:
            dbstored = db.profile.find_one({"renren_id": friend})
            if not dbstored:
                friend_ids = RenrenFriendList(self.login.session, friend).friends_uids
                friend_info = RenrenPersionInfo(self.login.session, friend).profile
                if friend_info:
                    friend_info['friend_ids'] = friend_ids
                    friend_info['renren_id'] = friend
                    db.profile.insert(friend_info)
            else:
                continue

class GetMuitFriendInfo(object):
    def __init__(self, email, password):
        self.login = RenrenLogin(email,password)
        for dataset in db.renrenids.find():
            for renren_id in dataset['renrenids']:
                persion = RenrenPersionInfo(self.login.session, renren_id)
                if persion.profile:
                    dbstored = db.profile.find_one({"renren_id": renren_id})
                    if not dbstored:
                        friend = RenrenFriendList(self.login.session, renren_id)
                        persion.profile['friend_ids'] = friend.friends_uids
                        persion.profile['renren_id'] = renren_id
                        db.profile.insert(persion.profile)

class RenrenLogin(object):
    def __init__(self, email, password):
        self.email = email
        self.password = password
        self.login()

    def login(self):
        self.session = requests.Session()
        headers = {
            "User-agent":"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1"
        }
        logininfo = {
            "email":self.email,
            "password":self.password
        }
        reponse = self.session.post("http://www.renren.com/PLogin.do",data=logininfo,headers=headers)
        uid_pattern = re.compile('renren\.com/(\d+)')
        uid = uid_pattern.findall(reponse.url)
        self.uid = uid[0]

class RenrenFriendList(object):
    def __init__(self, session, uid):
        self.session = session
        self.uid = uid
        self.getFriendIds()

    def getFriendIds(self, uid=None):
        uid = uid or self.uid

        URL = 'http://friend.renren.com/GetFriendList.do?curpage={0}&id=' + str(uid)

        first_page = self.session.get(URL.format(0))
        soup = BeautifulSoup(first_page.text)
        
        friends_count = soup.find_all(attrs={"class": "count"})[0].text
        friends_pages = int(friends_count) / 20
        friends_pages += 1

        self.friends_uids = []

        self.friends_uids.extend(self.get_page_friends(soup))

        for page_num in range(1, friends_pages):
            page = self.session.get(URL.format(page_num))
            soup = BeautifulSoup(page.text)
            self.friends_uids.extend(self.get_page_friends(soup))

        self.friends_uids

    def get_page_friends(self, soup):
        page_friends = []
        for link in soup.find_all('a'):
            result = re.match("http://www\.renren\.com/profile\.do\?id=(\d+)$", link.get('href'))
            if result and link.text and result.group(1) != self.uid:
                page_friends.append(result.group(1))
        return page_friends


class RenrenPersionInfo(object):
    def __init__(self, session, uid):
        self.session = session
        self.uid = uid
        self.getProfile()

    def getProfile(self):
        url = "http://www.renren.com/" + self.uid + "/profile?v=info_timeline"
        re_profile = self.session.get(url)
        soup = BeautifulSoup(re_profile.text)

        if len("".join(soup.title.text.split()).split('-')) <= 1:
            self.profile = ""
            return 

        print url
        print "".join(soup.title.text.split()).split('-')[1]

        profile_info =  soup.find_all(attrs={"class": "info"})
        self.profile = {}
        self.profile['name'] = "".join(soup.title.text.split()).split('-')[1]

        if profile_info:
            if not profile_info[0].dt:
                url_second = "http://www.renren.com/" + self.uid + "/profile?v=info_ajax"
                re_profile_second = self.session.get(url_second)
                soup_second = BeautifulSoup(re_profile_second.text)
                profile_info =  soup_second.find_all(attrs={"class": "info"})
                if not "".join(soup_second.find_all(attrs={"id": "personalInfo"})):
                    self.profile = ""
                    return 
                self.profile['some_info'] = "".join(soup_second.find_all(attrs={"id": "personalInfo"})[0].text.split())
        else:
            url_second = "http://www.renren.com/" + self.uid + "/profile?v=info_ajax"
            re_profile_second = self.session.get(url_second)
            soup_second = BeautifulSoup(re_profile_second.text)
            profile_info =  soup_second.find_all(attrs={"class": "info"})
            if soup_second.find_all(attrs={"id": "personalInfo"}):
                self.profile['some_info'] = "".join(soup_second.find_all(attrs={"id": "personalInfo"})[0].text.split())
       
        for link in profile_info:
            if link.dt and link.dd:
                self.profile[link.dt.text] = "".join(link.dd.text.split())