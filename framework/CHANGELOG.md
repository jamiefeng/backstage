#(2016-02-26)
- 404页面功能支持，支持自定义及默认404页面

#(2015-12-08)
- 增加儿童医院就诊卡验证器

#(2015-11-25)
- 增加SQL语句开关常量，用于记录SQL语句
#(2015-11-25)
- 使用线上代码替换仓库中代码，保证稳定;
- 修改Phalcon\Net\Http请求方式改为curl请求
- 修复部分身份证号源规则符合国标，但生日不对的身份证验证Bug
- 修复Session配置Bug,恢复默认Session配置
- 调整客户端提交过来的各种数据的获取方式顺序

#(2015-08-18)
- 新增91160用网分页代码样式
- 修复框架错误的session配置文件位置

#(2015-08-11)
- 新增网络协议请求工具RESTful请求工具与 Socket请求工具类
使用方式:
use Phalcon\Net\Http;
use Phalcon\Net\Socket;
- 新增网络数据格式 ProtocolBuffer 解析工具包，用于对PB数据格式进行解析。
使用方式：
use Phalcon\Logger\Protocol\ProtocolBuffer\Message\PBMessage;
- 新增Pb日志驱动，可以通过配置的形式集成pb日志，不需要再另行实现pb日志处理类。
  使用方式：见详细说明 docs/Logger日志.md
