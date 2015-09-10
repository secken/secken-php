

use `test`; /*将test换成第三方所需要的数据库名字*/

CREATE TABLE IF NOT EXISTS `id_mapping` (
    `my_uid` INT NOT NULL comment '第三方网站的用户id',
    `yangcong_uid` VARCHAR(64) NOT NULL comment '洋葱uid'
);

/*此表只是模拟存储用户信息，第三方可忽略*/
CREATE TABLE IF NOT EXISTS `user` (
    `user_id` INT NOT NULL PRIMARY KEY auto_increment comment '第三方网站的用户id',
    `username` VARCHAR(20) NOT NULL comment '第三方用户名',
    `pwd` VARCHAR(32) NOT NULL comment '第三方用户密码'
);
