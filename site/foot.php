    </div>
</div>
    <script>
        $(document).ready(()=>{
            var timeoutInter   = setInterval(()=>{
                $.ajax({
                    url: '<?php echo $site_url ?>modun/auth/update',
                    type: 'POST',
                    data: {type:'get-proxy'},
                    dataType: 'JSON',
                    success: (result)=>{
                        $('#proxy').html(result.data)
                    }
                });
            }, 6000);

            var timeoutInterval = setInterval(function () {
                var today = new Date();
                var time = today.getHours()+':'+today.getMinutes()+':'+today.getSeconds();
                $('#date-time').html(time);
            }, 1000);
        });

    </script>
    <!-- Bootstrap JS-->
    <script src="<?php echo $site_public ?>/vendor/bootstrap-4.1/popper.min.js"></script>
    <!-- Vendor JS       -->
    <!-- <script src="<?php echo $site_public ?>/vendor/slick/slick.min.js"></script> -->
    <script src="<?php echo $site_public ?>/vendor/wow/wow.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/animsition/animsition.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="<?php echo $site_public ?>/vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/counter-up/jquery.counterup.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/circle-progress/circle-progress.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?php echo $site_public ?>/vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="<?php echo $site_public ?>/vendor/select2/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
    <!-- Data Table -->
    <!-- Main JS-->
    <script src="<?php echo $site_public ?>/js/main.js"></script>
</body>

</html>
<!-- end document-->
