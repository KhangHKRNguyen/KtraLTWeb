<footer>
    <div class="container-fluid">
        <div class="row justify-content-center g-4 mb-4">
            <div class="col-12 text-center">
                <h5 style="color: var(--dark-color); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                    Banking Academy - Group 1
                </h5>
            </div>
            
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="footer-member">
                    <h6>Nguyễn Ngọc Linh</h6>
                    <img src="<?php echo URLROOT; ?>/public/assets/images/team/linh.jpg" alt="Nguyễn Ngọc Linh" class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                    <p class="text-muted small">Build models/ Controllers/ Search</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="footer-member">
                    <h6>Phạm Trung Hiếu</h6>
                    <img src="<?php echo URLROOT; ?>/public/assets/images/team/linh.jpg" alt="Phạm Trung Hiếu" class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                    <p class="text-muted small">Import / View Products</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="footer-member">
                    <h6>Đào Thị Huyền</h6>
                    <img src="<?php echo URLROOT; ?>/public/assets/images/team/linh.jpg" alt="Đào Thị Huyền" class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                    <p class="text-muted small">Export/ Delete Products</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="footer-member">
                    <h6>Nguyễn Đức Huy Khang</h6>
                    <img src="<?php echo URLROOT; ?>/public/assets/images/team/linh.jpg" alt="Nguyễn Đức Huy Khang" class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                    <p class="text-muted small">Create Products</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="footer-member">
                    <h6>Lương Minh Đạt</h6>
                    <img src="<?php echo URLROOT; ?>/public/assets/images/team/linh.jpg" alt="Lương Minh Đạt" class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                    <p class="text-muted small">Resources / Database / Update Products</p>
                </div>
            </div>
        </div>

        <hr style="border-color: var(--border-color);">
        
        <div class="text-center mt-3" style="color: #6b7280;">
            &copy; 2026 Banking Academy Group 1 - All Rights Reserved
        </div>
    </div>
</footer>

</div> 

<?php
    if (!empty($page_js_file)) {
        $resolved_js_file = $page_js_file;
    } else {
        $route = explode('/', $_GET['url'] ?? 'product/index');
        $current_controller = $route[0] ?? 'product';
        $current_action = $route[1] ?? 'index';
        $resolved_js_file = "assets/js/{$current_controller}-{$current_action}.js";
    }

    // Khi in ra link cho trình duyệt
    if (file_exists($resolved_js_file)) {
        echo '<script src="' . URLROOT . '/public/' . $resolved_js_file . '"></script>';
    }
?>
</body>
</html>