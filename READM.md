# web-
使用WebSocket 协议，使用Workerman自定义框架-简单的聊天系统
##
目录结构
html -保存当前页面的静态文件
  group -保存频道聊天的静态页面
    group -房间号
    room - 房间的聊天室
  private -私聊的静态页面
    login - 登录的页面
    room - 私聊的页面
 php -保存php文件和workerman包
  workerman-master 
  login - 服务器处理登录数据的程序
  room - workerman服务器处理程序
 public 公共的文件
  axios --ajax文件
  chat.css --静态页面的css文件
  vue.js --vue文件
 vendor - 扩展包
  commposer -composer包
  firebse -firebase 包
 
---
项目需要启动两个服务器，一个服务器是php的服务器，一个是Workerman 
php 服务器的地址是  localhost:9898 
Workerman php的目录下的room.php 直接启动 通过命令行启动 cd到该目录下  执行 php room.php statr 
## 私聊
需要手动切换，例：localhost:9898\html\private\login.html --登录页
##频道聊天
房间的数据固定的，后期连接数据库就行
##数据库结构
  chat表
  字段
    id -用户自增id
    name - 用户名称
    password -用户密码
