### 依赖 
---

- python
-  request
-  BeautifulSoup
-  re

### 请求流程
---

#### 0x01.

`GET http://my.seu.edu.cn/login.portal`

获取名为`JSESSIONID` 的cookie

构造登录信息

```
logininfo = {
    "Login.Token1":"一卡通号",
    "Login.Token2":"密码",
    "goto":"http://my.seu.edu.cn/loginSuccess.portal",
    "gotoOnFail":"http://my.seu.edu.cn/loginFailure.portal"
}
```

####0x02.

` POST http://my.seu.edu.cn/userPasswordValidate.portal`

params {cookie + logininfo}

获取名为`iPlanetDirectoryPro` 的 cookie

#### 0x03.

`GET http://allinonecard.seu.edu.cn/ecard/dongnanportalHome.action`

获取名为 `JSESSIONID` 的cookie

构造cookie 
```
headers = {
"Cookie":"JSESSIONID=" + re_s.cookies['JSESSIONID'] + "; iPlanetDirectoryPro=" + reponse.cookies['iPlanetDirectoryPro']
}
```
####0x04.

`GET http://allinonecard.seu.edu.cn/accountcardUser.action`

BeautifulSoup解析获取 userid 

#### 0x05.
这里要循环请求, 获取每个分页的内容.
构造数据包: 
```
userinfo = {
    'account':userid,
    'startDate':'',
    'endDate':'',
    'pageno':pagenum
  }
```

`POST http://allinonecard.seu.edu.cn/mjkqBrows.action`

即可获取到数据, 然后用`BeautifulSoup` 解析一下.
