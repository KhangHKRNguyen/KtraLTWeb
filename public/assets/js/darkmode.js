$(function () {
    const darkModeBtn = $('#toggle-dark-mode');
    const isDark = localStorage.getItem('darkMode') === 'true';

    // Áp dụng trạng thái darkmode lưu từ localStorage
    if (isDark) {
        $('body').addClass('dark-mode');
        if (darkModeBtn.length) darkModeBtn.html('<i class="fas fa-sun"></i> Light Mode');
    }

    // Gắn sự kiện toggle nếu có nút
    if (darkModeBtn.length) {
        darkModeBtn.on('click', function () {
            $('body').toggleClass('dark-mode');
            const enabled = $('body').hasClass('dark-mode');
            localStorage.setItem('darkMode', enabled);

            if (enabled) {
                darkModeBtn.html('<i class="fas fa-sun"></i> Light Mode');
            } else {
                darkModeBtn.html('<i class="fas fa-moon"></i> Dark Mode');
            }
        });
    }
});
