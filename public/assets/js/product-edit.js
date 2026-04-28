$(document).ready(function () {

    const productId = $('#product-id').val();
    const variantTableBody = $('#variant-table-body');
    const imageGallery = $('#image-gallery');

    // Khởi tạo đối tượng Modal
    const editVariantModal = new bootstrap.Modal(document.getElementById('editVariantModal'));

    // Color map
    const colorMap = {
        'Đen': '#000000',
        'Trắng': '#FFFFFF',
        'Bạc': '#C0C0C0',
        'Xám': '#808080',
        'Titan Tự nhiên': '#A6A199',
        'Vàng': '#FFD700',
        'Đỏ': '#E74C3C',
        'Xanh Dương': '#3498DB',
        'Xanh Lá': '#2ECC71',
        'Tím': '#9B59B6',
        'Hồng': '#FFC0CB',
        'Beige': '#F5F5DC',
        'Platinum': '#E5E4E2'
    };

    // ========== TOAST NOTIFICATION ==========
    const showToast = (message, icon = 'success') => {
        // Xử lý để tương thích ngược với (message, true) hoặc (message, false)
        if (icon === true) icon = 'success';
        if (icon === false) icon = 'error';

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    };

    // ========== HELPER FUNCTIONS ==========
    
    // Format số tiền
    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + ' đ';
    };

    // Format số lượng
    const formatNumber = (number) => {
        return new Intl.NumberFormat('vi-VN').format(number);
    };

    // Validate file ảnh
    const validateImageFile = (file) => {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!allowedTypes.includes(file.type)) {
            showToast('Chỉ chấp nhận file ảnh JPG, PNG, GIF, WEBP!', 'error');
            return false;
        }

        if (file.size > maxSize) {
            showToast('Kích thước file không được vượt quá 5MB!', 'error');
            return false;
        }

        return true;
    };

    // ========== VARIANT MANAGEMENT ==========

    // === THÊM BIẾN THỂ MỚI (AJAX) ===
    $('#variant-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"]');
        const originalButtonHtml = $submitButton.html();

        // Validate inputs
        const color = $form.find('[name="color"]').val().trim();
        const storage = $form.find('[name="storage"]').val().trim();
        const price = parseFloat($form.find('[name="price"]').val());
        const stock = parseInt($form.find('[name="stock"]').val());

        if (!color || !storage) {
            showToast('Vui lòng điền đầy đủ màu sắc và dung lượng!', 'error');
            return;
        }

        if (isNaN(price) || price <= 0) {
            showToast('Giá không hợp lệ!', 'error');
            return;
        }

        if (isNaN(stock) || stock < 0) {
            showToast('Tồn kho không hợp lệ!', 'error');
            return;
        }

        // Validate image if exists
        const imageFile = $form.find('[name="image"]')[0]?.files[0];
        if (imageFile && !validateImageFile(imageFile)) {
            return;
        }

        $submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang thêm...');

        const formData = new FormData(this);

        $.ajax({
            url: SITE_URL,
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#no-variants-row').remove();
                    variantTableBody.append(response.variant_html);
                    $form[0].reset();
                    
                    // Scroll to new variant
                    if (response.variant_id) {
                        $('html, body').animate({
                            scrollTop: $('#variant-' + response.variant_id).offset().top - 100
                        }, 500);
                    }
                } else {
                    // Kiểm tra type để hiển thị đúng icon
                    let iconType = 'error';
                    if (response.type === 'info') iconType = 'info';
                    if (response.type === 'warning') iconType = 'warning';
                    
                    showToast(response.message, iconType);
                    
                    // Nếu có existing_id, highlight row đó
                    if (response.existing_id) {
                        const $existingRow = $('#variant-' + response.existing_id);
                        if ($existingRow.length) {
                            // Remove any existing highlights
                            $('.variant-highlight').removeClass('variant-highlight');
                            
                            // Add highlight
                            $existingRow.addClass('variant-highlight');
                            
                            // Scroll to existing variant
                            $('html, body').animate({
                                scrollTop: $existingRow.offset().top - 100
                            }, 500);
                            
                            // Remove highlight after 3 seconds
                            setTimeout(() => {
                                $existingRow.removeClass('variant-highlight');
                            }, 3000);
                        }
                    }
                }
            },
            error: function (jqXHR) {
                const errorMsg = jqXHR.responseJSON?.message || 'Lỗi không xác định. Vui lòng thử lại.';
                showToast(errorMsg, 'error');
                console.error('Error:', jqXHR.responseText);
            },
            complete: function () {
                $submitButton.prop('disabled', false).html(originalButtonHtml);
            }
        });
    });

    // === MỞ MODAL SỬA BIẾN THỂ ===
    $(document).on('click', '.btn-edit-variant', function () {
        const $btn = $(this);
        const id = $btn.data('id');
        const color = $btn.data('color');
        const storage = $btn.data('storage');
        const price = $btn.data('price');
        const stock = $btn.data('stock');
        const imageUrl = $btn.data('image_url') || '';

        // Điền dữ liệu vào form
        $('#edit-variant-id').val(id);
        $('#edit-variant-color').text(color);
        $('#edit-variant-storage').text(storage);
        $('#edit-variant-price').val(price);
        $('#edit-variant-stock').val(stock);

        // Set color circle
        const colorHex = colorMap[color] || '#CCCCCC';
        const border = (colorHex === '#FFFFFF' || colorHex === '#CCCCCC') ? 'border: 1px solid #ccc;' : '';
        $('#edit-variant-color-circle').css({
            'background-color': colorHex,
            'border': border ? '1px solid #ccc' : 'none'
        });

        // Display current image
        if (imageUrl) {
            $('#current-variant-image').html(`
                <img src="${imageUrl}" alt="Variant Image" 
                     class="img-fluid" 
                     style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover;">
            `);
        } else {
            $('#current-variant-image').html(`
                <div class="text-center p-3 bg-light rounded">
                    <i class="fas fa-image fa-3x text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Chưa có ảnh</p>
                </div>
            `);
        }

        // Reset file input and preview
        $('#edit-variant-image').val('');
        $('#new-variant-image-preview').hide().html('');

        // Mở modal
        editVariantModal.show();
    });

    // === PREVIEW ẢNH KHI CHỌN FILE (EDIT MODAL) ===
    $('#edit-variant-image').on('change', function () {
        const file = this.files[0];
        const $preview = $('#new-variant-image-preview');
        
        if (file) {
            if (!validateImageFile(file)) {
                $(this).val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                $preview.html(`
                    <div class="mt-3">
                        <p class="text-muted mb-2"><strong>Ảnh mới:</strong></p>
                        <img src="${e.target.result}" 
                             class="img-fluid" 
                             style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover;">
                    </div>
                `).show();
            };
            reader.readAsDataURL(file);
        } else {
            $preview.hide().html('');
        }
    });

    // === SUBMIT FORM SỬA BIẾN THỂ ===
    $('#edit-variant-form').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"]');
        const originalButtonHtml = $submitButton.html();

        const variantId = $('#edit-variant-id').val();
        const newPriceVal = parseFloat($('#edit-variant-price').val());
        const newStockVal = parseInt($('#edit-variant-stock').val());

        // Validate
        if (isNaN(newPriceVal) || newPriceVal <= 0) {
            showToast('Giá không hợp lệ!', 'error');
            return;
        }

        if (isNaN(newStockVal) || newStockVal < 0) {
            showToast('Tồn kho không hợp lệ!', 'error');
            return;
        }

        // Validate image if exists
        const imageFile = $('#edit-variant-image')[0]?.files[0];
        if (imageFile && !validateImageFile(imageFile)) {
            return;
        }

        $submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');

        const formData = new FormData(this);
        formData.append('controller', 'variant');
        formData.append('action', 'ajax_update');
        formData.append('product_id', productId);

        $.ajax({
            url: SITE_URL,
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    editVariantModal.hide();

                    // Cập nhật giá và tồn kho
                    $('#variant-price-' + variantId).text(response.price);
                    $('#variant-stock-' + variantId).text(response.stock);

                    // Cập nhật ảnh nếu có
                    if (response.image_url) {
                        const newImageHtml = `<img src="${response.image_url}" class="variant-thumbnail" alt="Variant Image">`;
                        $('#variant-image-wrapper-' + variantId).html(newImageHtml);
                    }

                    // Cập nhật data attributes
                    const $editButton = $('.btn-edit-variant[data-id="' + variantId + '"]');
                    $editButton.attr('data-price', newPriceVal);
                    $editButton.attr('data-stock', newStockVal);
                    if (response.image_url) {
                        $editButton.attr('data-image_url', response.image_url);
                    }

                    // Highlight updated row
                    const $row = $('#variant-' + variantId);
                    $row.addClass('table-success');
                    setTimeout(() => {
                        $row.removeClass('table-success');
                    }, 2000);

                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function (jqXHR) {
                const errorMsg = jqXHR.responseJSON?.message || 'Lỗi không xác định.';
                showToast(errorMsg, 'error');
                console.error('Error:', jqXHR.responseText);
            },
            complete: function () {
                $submitButton.prop('disabled', false).html(originalButtonHtml);
            }
        });
    });

    // === XÓA BIẾN THỂ ===
    $(document).on('click', '.btn-delete-variant', function () {
        const $thisButton = $(this);
        const variantId = $thisButton.data('id');
        const variantName = $thisButton.data('name');

        Swal.fire({
            title: 'Bạn có chắc không?',
            html: `Bạn sắp xóa biến thể <strong>"${variantName}"</strong><br>Hành động này không thể hoàn tác!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-trash"></i> Đồng ý, xóa!',
            cancelButtonText: '<i class="fas fa-times"></i> Hủy',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Đang xóa...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: SITE_URL,
                    type: 'POST',
                    data: {
                        controller: 'variant',
                        action: 'ajax_delete',
                        id: variantId,
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.close();
                            showToast(response.message, 'success');
                            
                            $('#variant-' + variantId).fadeOut(500, function () {
                                $(this).remove();
                                
                                // Kiểm tra nếu không còn variant nào
                                if ($('#variant-table-body tr:visible').length === 0) {
                                    $('#variant-table-body').html(`
                                        <tr id="no-variants-row">
                                            <td colspan="7" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                                    <h5>Chưa có biến thể nào</h5>
                                                    <p class="text-muted">Hãy thêm biến thể mới ở form bên trên</p>
                                                </div>
                                            </td>
                                        </tr>
                                    `);
                                }
                            });
                        } else {
                            Swal.close();
                            showToast(response.message, 'error');
                        }
                    },
                    error: function (jqXHR) {
                        Swal.close();
                        const errorMsg = jqXHR.responseJSON?.message || 'Lỗi không thể xóa.';
                        showToast(errorMsg, 'error');
                        console.error('Error:', jqXHR.responseText);
                    }
                });
            }
        });
    });

    // ========== IMAGE MANAGEMENT ==========

    // === PREVIEW ẢNH KHI CHỌN FILE ===
    $('#image_url').on('change', function () {
        const file = this.files[0];
        const $formText = $(this).next('.form-text');
        
        if (file) {
            if (!validateImageFile(file)) {
                $(this).val('');
                $formText.html('<i class="fas fa-exclamation-triangle text-danger"></i> File không hợp lệ');
                return;
            }

            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            $formText.html(`
                <i class="fas fa-file-image text-success"></i> 
                <strong>${fileName}</strong> (${fileSize} MB)
            `);
        } else {
            $formText.html('Chọn file ảnh (JPG, PNG, GIF, WEBP - Max 5MB)');
        }
    });

    // === UPLOAD ẢNH ===
    $('#image-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"]');
        const originalButtonHtml = $submitButton.html();

        const fileInput = $('#image_url')[0];
        
        if (!fileInput.files || fileInput.files.length === 0) {
            showToast('Vui lòng chọn ảnh!', 'error');
            return;
        }

        const file = fileInput.files[0];
        if (!validateImageFile(file)) {
            return;
        }

        $submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang upload...');

        const formData = new FormData(this);

        $.ajax({
            url: SITE_URL,
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#no-images-row').remove();
                    imageGallery.append(response.image_html);
                    $form[0].reset();
                    $('#image_url').next('.form-text').html('Chọn file ảnh (JPG, PNG, GIF, WEBP - Max 5MB)');
                    
                    // Scroll to new image
                    if (response.image_id) {
                        $('html, body').animate({
                            scrollTop: $('#image-item-' + response.image_id).offset().top - 100
                        }, 500);
                    }
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function (jqXHR) {
                const errorMsg = jqXHR.responseJSON?.message || 'Upload thất bại!';
                showToast(errorMsg, 'error');
                console.error('Error:', jqXHR.responseText);
            },
            complete: function () {
                $submitButton.prop('disabled', false).html(originalButtonHtml);
            }
        });
    });

    // === XÓA ẢNH ===
    $(document).on('click', '.btn-delete-image', function () {
        const $thisButton = $(this);
        const imageId = $thisButton.data('id');
        const $imageItem = $thisButton.closest('.image-item');

        Swal.fire({
            title: 'Xác nhận xóa?',
            text: 'Bạn có chắc chắn muốn xóa ảnh này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-trash"></i> Xóa',
            cancelButtonText: '<i class="fas fa-times"></i> Hủy',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Đang xóa...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: SITE_URL,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        controller: 'image',
                        action: 'ajax_delete',
                        product_id: productId,
                        id: imageId
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.close();
                            showToast(response.message, 'success');
                            
                            $imageItem.fadeOut(400, function () {
                                $(this).remove();
                                
                                // Kiểm tra nếu không còn ảnh nào
                                if ($('#image-gallery .image-item').length === 0) {
                                    $('#image-gallery').html(`
                                        <div class="col-12 text-center py-5" id="no-images-row">
                                            <div class="empty-state">
                                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                                <h5>Chưa có ảnh nào</h5>
                                                <p class="text-muted">Hãy upload ảnh mới ở form bên trên</p>
                                            </div>
                                        </div>
                                    `);
                                }
                            });
                        } else {
                            Swal.close();
                            showToast(response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.close();
                        const response = xhr.responseJSON;
                        showToast(response?.message || 'Xóa thất bại!', 'error');
                        console.error('Error:', xhr.responseText);
                    }
                });
            }
        });
    });

    // ========== FORM VALIDATION ==========
    
    // Prevent negative numbers in number inputs
    $('input[type="number"]').on('keypress', function (e) {
        if (e.which === 45) { // Minus sign
            e.preventDefault();
        }
    });

    // Format price input on blur
    $('input[name="price"], #edit-variant-price').on('blur', function () {
        const val = parseFloat($(this).val());
        if (!isNaN(val) && val > 0) {
            $(this).val(val.toFixed(0));
        }
    });

    // Format stock input on blur
    $('input[name="stock"], #edit-variant-stock').on('blur', function () {
        const val = parseInt($(this).val());
        if (!isNaN(val) && val >= 0) {
            $(this).val(val);
        }
    });

    // ========== KEYBOARD SHORTCUTS ==========
    
    $(document).on('keydown', function (e) {
        // ESC to close modal
        if (e.key === 'Escape' && editVariantModal._isShown) {
            editVariantModal.hide();
        }
    });

    // ========== INITIALIZATION ==========
    
    console.log('✅ Edit Product Page Initialized');
    console.log('📦 Product ID:', productId);
    console.log('🎨 Color Map:', Object.keys(colorMap).length, 'colors loaded');
});