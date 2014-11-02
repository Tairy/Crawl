from pymongo import MongoClient
import time

db = MongoClient().renrendata

last_friend_ids = []
for persion in db.profile.find():
    persion['friend_ids'].append(persion['renren_id'])
    last_friend_ids += persion['friend_ids']

last_friend_ids_2 = list(set(last_friend_ids))
start = time.clock()

db.renrenids.insert({"renrenids":last_friend_ids_2})
elapsed = (time.clock() - start)

print("Time used:",elapsed)
print len(last_friend_ids)
print len(last_friend_ids_2)