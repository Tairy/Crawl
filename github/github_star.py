# -*- coding:utf-8 -*-
# /usr/bin/python

import requests
import json
import sys 
from pymongo import MongoClient

db = MongoClient().github

def get_stared_repos():
    session = requests.Session()
    for i in range(5):
        page = i + 1
        re = session.get("https://api.github.com/users/Tairy/starred?page="+str(page))
        stared_repos = json.loads(re.text)
        for stared_repo in stared_repos:
            db.star.insert({
              "name":stared_repo['name'], 
              "url":"https://github.com/"+stared_repo['full_name'], 
              "language":stared_repo['language'],
              "description":stared_repo['description']
             })

def get_repo_lang():
    for repo in db.star.find():
      if not db.repolang.find_one({
          'name':repo['language']
          }):
          db.repolang.insert({
            'name':repo['language']
          })

def create_markdown():
    md_file = open('github.md','w')
    md_file_link = open('link.md','w')
    link_num = 1
    for lang in db.repolang.find():
        md_file.write("###"+str(lang['name'])+"\n")
        print lang['name']
        for repo in db.star.find({"language":lang['name']}):
            md_file.write("- ["+str(repo['name'])+"]["+str(link_num)+"]: "+str(repo['description'])+"\n")
            md_file_link.write("["+str(link_num)+"]: "+repo['url']+"\n")
            link_num += 1
    md_file.close()
    md_file_link.close()

if __name__ == "__main__":
    reload(sys)  
    sys.setdefaultencoding('utf8') 
    get_stared_repos()
    get_repo_lang()
    create_markdown()
