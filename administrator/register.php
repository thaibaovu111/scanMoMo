<?php 
require_once '../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
else if($user['level'] != 'admin') {
    header('Location: '. $site_url);
    die();
}

if(!empty($_POST)) {
    $_POST['password'] = md5($_POST['password']);
    $db->insert('table_user', $_POST);
    header('Location: '. $site_url. 'administrator/register');
}

$site['title'] = 'Đăng ký tài khoản người dùng ' . $site['site_name'];
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="page-wrapper">
        <div class="page-content--bge5">
            <div class="container">
                <div class="login-wrap">
                    <div class="login-content">
                        <div class="login-logo">
                            <a href="#">
                                <img src="<?php echo $site_public ?>/images/icon/logo.png" alt="CoolAdmin">
                            </a>
                        </div>
                        <div class="login-form">
                            <form action="<?= $site_url ?>administrator/register" id="form_login" method="post">
                                <div class="form-group">
                                    <label>Địa chỉ email</label>
                                    <input class="au-input au-input--full" type="text" name="email" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <label>Tên Người Dùng</label>
                                    <input type="text" class="au-input au-input--full" name="username" placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <label>Mật Khẩu</label>
                                    <input class="au-input au-input--full" autocomplete="new-password"  type="password" name="password" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <select name="level" class="form-control" >
                                        <option value="admin">Quản Trị Viên</option>
                                        <option value="user">Nhân Viên</option>
                                    </select>
                                </div>
                                <button class="au-btn au-btn--block au-btn--green m-b-20" id="submit" type="submit">Đăng Ký</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../site/foot.php';
?>