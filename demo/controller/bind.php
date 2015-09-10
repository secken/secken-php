<?php
session_start();
/**
 * 请先确保您已经执行了table.sql中的语句
 */
$host = '127.0.0.1';  //您的数据库地址，例如：127.0.0.1
$user_name  = 'root';   //连接数据库所需用户名，例如: root
$password = '';     //连接数据库所需密码, 例如: 123456

//连接数据库
$pdo = new PDO("mysql:host=".$host.";dbname=test", $user_name, $password);

//判断是否来自绑定页面
if(isset($_POST['bind_form'])){
    $user_name = $_POST['username'];
    $pwd = md5($_POST['pwd']);

    $pdo->query('INSERT INTO `user`(`username`, `pwd`)VALUE("'.$user_name.'", "'.$pwd.'")');
    $user_id = $pdo->lastinsertid();

    if($user_id > 0){
        $yangcong_uid = $_SESSION['yangcong_uid'];
        $affected_rows = $pdo->exec('INSERT INTO `id_mapping`(`my_uid`, `yangcong_uid`)VALUE('.$user_id.', "'.$yangcong_uid.'")');

        if($affected_rows > 0){
            echo '<script>location.href="../myspace.html";</script>';
        }else{
            echo '<script>location.href="../bind.html";</script>';
        }
    }
}
?>
