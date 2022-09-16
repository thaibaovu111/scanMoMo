<?php 
require_once '../config.php';
if(!empty($user_info['username'])){
    header('Location: '. $site_url. 'home');

    die();
}
$site['title'] = 'Đăng nhập ' . $site['site_name'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="<?php  ?>">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title><?php echo $site['title'] ?></title>

    <!-- Fontfaces CSS-->
    <link href="<?php echo $site_public ?>/css/font-face.css" rel="stylesheet" media="all">
    <link href="<?php echo $site_public ?>/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $site_public ?>/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $site_public ?>/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="<?php echo $site_public ?>/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Vendor CSS-->
    <link href="<?php echo $site_public ?>/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $site_public ?>/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $site_public ?>/vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="<?php echo $site_public ?>/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $site_public ?>/vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="<?php echo $site_public ?>/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $site_public ?>/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Main CSS-->
    <link href="<?php echo $site_public ?>/css/theme.css" rel="stylesheet" media="all">

</head>

<body class="animsition">
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
                            <form action="javascript:void(0)" id="form_login" method="post">
                                <div class="form-group">
                                    <label>Địa chỉ email hoặc tên người dùng</label>
                                    <input class="au-input au-input--full" type="text" name="email" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <label>Mật Khẩu</label>
                                    <input class="au-input au-input--full" type="password" name="password" placeholder="Password">
                                </div>
                                <div class="login-checkbox">
                                    <label>
                                        <input type="checkbox" name="remember">Ghi nhớ tôi
                                    </label>
                                    <label>
                                        <a href="#">Bạn quên mật khẩu</a>
                                    </label>
                                </div>
                                <button class="au-btn au-btn--block au-btn--green m-b-20" id="submit" type="submit">Đăng Nhập</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Jquery JS-->
    <script src="<?php echo $site_public ?>/vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="<?php echo $site_public ?>/vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="<?php echo $site_public ?>/vendor/slick/slick.min.js">
    </script>
    <script src="<?php echo $site_public ?>/vendor/wow/wow.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/animsition/animsition.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="<?php echo $site_public ?>/vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="<?php echo $site_public ?>/vendor/circle-progress/circle-progress.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?php echo $site_public ?>/vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="<?php echo $site_public ?>/js/main.js"></script>
    <script>
        $('#submit').on('click', () => {
                var email = $('input[name="email"]').val();
                var password = $('input[name="password"]').val();
                if(!email || !password){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops..',
                        text: 'Vui lòng nhập đầy đủ thông tin để đăng nhập'
                    });
                    return false;
                }
                $.ajax({
                    url: '<?php echo $site_modun ?>/auth/login',
                    dataType: 'JSON',
                    type: 'POST',
                    data: $('#form_login').serialize(),
                    success: (data) => {
                        if(data.error){
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: data.message
                            })
                            return false;
                        }
                        else if(data.success){
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: data.message
                            })
                            setTimeout(() => {
                                window. location. href = '<?php echo $site_url ?>home';
                            }, 2000);
                        }
                    }
                })
        })
    </script>
</body>

</html>
<!-- end document-->