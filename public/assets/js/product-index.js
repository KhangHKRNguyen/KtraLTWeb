// File: assets/js/main.js

$(document).ready(function () {

    // ========== Biến toàn cục ==========
    const loadingOverlay = $('.loading-overlay');
    const tableBody = $('#product-table-body');
    const paginationContainer = $('#pagination-container');
    const toastContainer = $('.toast-container');
    const searchForm = $('#search-form');
    const productCount = $('#product-count');

    // ========== Toast Notification System ==========
    function showToast(message, type = 'success') {
        const toastId = 'toast-' + Date.now();
        const bgClass = {
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning',
            'info': 'bg-info'
        }[type] || 'bg-success';

        const iconClass = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        }[type] || 'fa-check-circle';

        const toastHTML = `
            <div id="${toastId}" class="toast toast-modern align-items-center text-white ${bgClass} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${iconClass} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.append(toastHTML);
        const toastEl = new bootstrap.Toast($('#' + toastId), {
            delay: 3000
        });
        toastEl.show();

        // Tự động xóa sau khi ẩn
        $('#' + toastId).on('hidden.bs.toast', function () {
            $(this).remove();
        });
    }

    // ========== Load Products Function ==========
    function loadProducts(page = 1) {
        const search = $('#search').val().trim();
        const min_price = $('#min_price').val();
        const max_price = $('#max_price').val();
        const category_id = $('#category_id').val();
        const supplier_id = $('#supplier_id').val();

        // Hiển thị loading
        loadingOverlay.fadeIn(200);

        $.ajax({
            url: SITE_URL,
            type: 'GET',
            dataType: 'json',
            data:{
                    controller: 'product',
                    action: 'ajax_list',
                    page: page,
                    search: search,
                    category_id: $('#category_id').val(),
                    supplier_id: $('#supplier_id').val(),
                    min_price: min_price,
                    max_price: max_price
                },
            success: function (response) {
                if (response.success) {
                    // Cập nhật table body
                    tableBody.html(response.table_html);

                    // Cập nhật pagination
                    paginationContainer.html(response.pagination_html);

                    // Cập nhật số lượng sản phẩm
                    if (response.total_products !== undefined) {
                        productCount.html(`
                            <i class="fas fa-box"></i> ${response.total_products} sản phẩm
                        `);
                    }

                    // Smooth scroll to top
                    $('html, body').animate({
                        scrollTop: $('.modern-card').offset().top - 20
                    }, 300);
                } else {
                    tableBody.html(`
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <h5>${response.message || 'Có lỗi xảy ra'}</h5>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            },
            error: function (xhr, status, error) {
                showToast('Lỗi kết nối. Vui lòng thử lại!', 'error');
                tableBody.html(`
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-times-circle"></i>
                                <h5>Không thể tải dữ liệu</h5>
                                <p>Vui lòng kiểm tra kết nối và thử lại</p>
                            </div>
                        </td>
                    </tr>
                `);
                console.error('AJAX Error:', error);
            },
            complete: function () {
                loadingOverlay.fadeOut(200);
            }
        });
    }

    // ========== Delete Product Function ==========
    function deleteProduct(productId, productName, button) {
        const $button = $(button);
        const originalHTML = $button.html();

        // Disable button và hiển thị spinner
        $button.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Đang xóa...');

        $.ajax({
            url: SITE_URL,
            type: 'POST',
            dataType: 'json',
            data: {
                controller: 'product',
                action: 'ajax_delete',
                id: productId
            },
            success: function (response) {
                if (response.success) {
                    // Animation xóa row
                    $button.closest('tr').addClass('table-danger');
                    setTimeout(function () {
                        $button.closest('tr').fadeOut(400, function () {
                            $(this).remove();

                            // Kiểm tra nếu không còn sản phẩm nào
                            if (tableBody.find('tr').length === 0) {
                                tableBody.html(`
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-box-open"></i>
                                                <h5>Không có sản phẩm nào</h5>
                                                <p>Hãy thêm sản phẩm mới để bắt đầu</p>
                                            </div>
                                        </td>
                                    </tr>
                                `);
                            }
                        });
                    }, 300);

                    showToast(response.message || 'Xóa sản phẩm thành công!', 'success');
                } else {
                    showToast(response.message || 'Không thể xóa sản phẩm!', 'error');
                    $button.prop('disabled', false).html(originalHTML);
                }
            },
            error: function () {
                showToast('Lỗi kết nối, không thể xóa!', 'error');
                $button.prop('disabled', false).html(originalHTML);
            }
        });
    }

    // ========== Event Handlers ==========

    // 1. Form search submit
    searchForm.on('submit', function (e) {
        e.preventDefault();
        loadProducts(1); // Reset về trang 1
    });

    // 2. Realtime search (debounce)
    let searchTimeout;
    $('#search').on('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            if ($('#search').val().length >= 3 || $('#search').val().length === 0) {
                loadProducts(1);
            }
        }, 500);
    });

    // 3. Price filter change
    $('#min_price, #max_price').on('change', function () {
        loadProducts(1);
    });

    // 4. Pagination click
    $(document).on('click', '#pagination-container .page-link', function (e) {
        e.preventDefault();

        const href = $(this).attr('href');
        if (!href || href === '#') return;

        const urlParams = new URLSearchParams(href.split('?')[1]);
        const page = urlParams.get('page') || 1;

        loadProducts(page);
    });

    // 5. Delete button click
    $(document).on('click', '.btn-delete-product', function (e) {
        e.preventDefault();

        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const button = this;

        // SweetAlert2 or native confirm
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc chắn muốn xóa sản phẩm<br><strong>"${productName}"</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteProduct(productId, productName, button);
                }
            });
        } else {
            if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${productName}"?\n\nMọi biến thể và hình ảnh liên quan cũng sẽ bị xóa.`)) {
                deleteProduct(productId, productName, button);
            }
        }
    });

    // 6. Keyboard shortcuts
    $(document).on('keydown', function (e) {
        // Ctrl/Cmd + K = Focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('#search').focus();
        }
    });

    // ========== Initialize ==========
    // Load products on page load
    loadProducts(1);

    // Show keyboard shortcut hint
    $('#search').attr('placeholder', 'Tìm kiếm (Ctrl+K)...');

    // Add smooth scrolling
    $('a[href^="#"]').on('click', function (e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 20
            }, 400);
        }
    });

    console.log('✅ Product Management System Initialized');
});