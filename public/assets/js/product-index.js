// File: assets/js/main.js

$(document).ready(function () {
  // ========== Biến toàn cục ==========
  const loadingOverlay = $(".loading-overlay");
  const tableBody = $("#product-table-body");
  const paginationContainer = $("#pagination-container");
  const toastContainer = $(".toast-container");
  const searchForm = $("#search-form");
  const productCount = $("#product-count");

  // ========== Request Control Variables ==========
  let currentXHR = null;
  let currentRequestId = 0;

  // ========== Toast Notification System ==========
  function showToast(message, type = "success") {
    const toastId = "toast-" + Date.now();
    const bgClass =
      {
        success: "bg-success",
        error: "bg-danger",
        warning: "bg-warning",
        info: "bg-info",
      }[type] || "bg-success";

    const iconClass =
      {
        success: "fa-check-circle",
        error: "fa-exclamation-circle",
        warning: "fa-exclamation-triangle",
        info: "fa-info-circle",
      }[type] || "fa-check-circle";

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
    const toastEl = new bootstrap.Toast($("#" + toastId), {
      delay: 3000,
    });
    toastEl.show();

    // Tự động xóa sau khi ẩn
    $("#" + toastId).on("hidden.bs.toast", function () {
      $(this).remove();
    });
  }

  // ========== Load Products Function ==========
  function loadProducts(page = 1) {
    console.log("📌 loadProducts called with page:", page);
    
    const search = $("#search").val().trim();
    const min_price = $("#min_price").val();
    const max_price = $("#max_price").val();
    const category_id = $("#category_id").val();
    const supplier_id = $("#supplier_id").val();

    if (currentXHR) {
      currentXHR.abort();
    }

    currentRequestId++;
    const thisRequestId = currentRequestId;

    // Hiển thị loading
    loadingOverlay.fadeIn(200);

    console.log("🔄 Sending AJAX request with:", {page, search, category_id, supplier_id, min_price, max_price});

    $.ajax({
      url: SITE_URL + "?url=product/ajax_list",
      type: "GET",
      dataType: "json",
      timeout: 15000,
      data: {
        page: page,
        search: search,
        category_id: $("#category_id").val(),
        supplier_id: $("#supplier_id").val(),
        min_price: min_price,
        max_price: max_price,
      },
      success: function (response) {
        console.log("✅ AJAX response received:", response);
        
        if (thisRequestId !== currentRequestId) {
          console.warn("Bỏ qua response từ request cũ");
          return;
        }

        if (response.success) {
          console.log("📊 Updating table with", response.table_html.split("<tr>").length - 1, "products");
          console.log("📄 Pagination HTML length:", response.pagination_html.length, "chars");
          
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
          $("html, body").animate(
            {
              scrollTop: $(".modern-card").offset().top - 20,
            },
            300,
          );
        } else {
          tableBody.html(`
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <h5>${response.message || "Có lỗi xảy ra"}</h5>
                                </div>
                            </td>
                        </tr>
                    `);
        }
      },
      error: function (xhr, status, error) {
        if (thisRequestId !== currentRequestId) {
          console.warn("Bỏ qua error từ request cũ");
          return;
        }

        if (status === "abort") {
          console.log("Request bị cancel (hủy bỏ)");
          return;
        }

        console.error("❌ AJAX Error! Status:", status, "Error:", error);
        console.error("📄 Response text:", xhr.responseText);
        
        showToast("Lỗi kết nối. Vui lòng thử lại!", "error");
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
        console.error("AJAX Error:", error, status);
      },
      complete: function () {
        if (thisRequestId === currentRequestId) {
          loadingOverlay.fadeOut(200);
        }
      },
    });
  }

  // ========== Delete Product Function ==========
  function deleteProduct(productId, productName, button) {
    const $button = $(button);
    const originalHTML = $button.html();

    // Disable button và hiển thị spinner
    $button
      .prop("disabled", true)
      .html('<i class="fas fa-spinner fa-spin"></i> Đang xóa...');

    $.ajax({
      url: SITE_URL + "?url=product/ajaxDelete",
      type: "POST",
      dataType: "json",
      timeout: 10000,
      data: {
        id: productId,
      },
      success: function (response) {
        if (response.success) {
          // Animation xóa row
          $button.closest("tr").addClass("table-danger");
          setTimeout(function () {
            $button.closest("tr").fadeOut(400, function () {
              $(this).remove();

              // Kiểm tra nếu không còn sản phẩm nào
              if (tableBody.find("tr").length === 0) {
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

          showToast(response.message || "Xóa sản phẩm thành công!", "success");
        } else {
          showToast(response.message || "Không thể xóa sản phẩm!", "error");
          $button.prop("disabled", false).html(originalHTML);
        }
      },
      error: function (xhr, status, error) {
        if (status === "timeout") {
          showToast("Kết nối timed out, vui lòng thử lại!", "error");
        } else if (status !== "abort") {
          showToast("Lỗi kết nối, không thể xóa!", "error");
        }
        $button.prop("disabled", false).html(originalHTML);
      },
    });
  }

  // ========== Event Handlers ==========

  // 1. Form search submit
  searchForm.on("submit", function (e) {
    e.preventDefault();
    loadProducts(1); // Reset về trang 1
  });

  // 2. Realtime search (debounce)
  let searchTimeout;
  $("#search").on("input", function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function () {
      if ($("#search").val().length >= 3 || $("#search").val().length === 0) {
        loadProducts(1);
      }
    }, 500);
  });

  // 3. Price filter change
  $("#min_price, #max_price").on("change", function () {
    loadProducts(1);
  });

  // 4. Pagination click
  //   $(document).on("click", "#pagination-container .page-link", function (e) {
  //     e.preventDefault();

  //     const href = $(this).attr("href");
  //     if (!href || href === "#") return;

  //     const urlParams = new URLSearchParams(href.split("?")[1]);
  //     const page = urlParams.get("page") || 1;

  //     loadProducts(page);
  //   });
  // 4. Pagination click
  $(document).on("click", "#pagination-container .page-link", function (e) {
    e.preventDefault();
    
    const page = $(this).data("page") || 1;
    console.log("🖱️ Pagination clicked! data-page attribute:", $(this).data("page"), "calculated page:", page);
    console.log("🔗 Full element:", $(this).html());
    
    loadProducts(page);
  });
  // 5. Delete button click
  $(document).on("click", ".btn-delete-product", function (e) {
    e.preventDefault();

    const productId = $(this).data("id");
    const productName = $(this).data("name");
    const button = this;

    // SweetAlert2 or native confirm
    if (typeof Swal !== "undefined") {
      Swal.fire({
        title: "Xác nhận xóa?",
        html: `Bạn có chắc chắn muốn xóa sản phẩm<br><strong>"${productName}"</strong>?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Xóa",
        cancelButtonText: "Hủy",
      }).then((result) => {
        if (result.isConfirmed) {
          deleteProduct(productId, productName, button);
        }
      });
    } else {
      if (
        confirm(
          `Bạn có chắc chắn muốn xóa sản phẩm "${productName}"?\n\nMọi biến thể và hình ảnh liên quan cũng sẽ bị xóa.`,
        )
      ) {
        deleteProduct(productId, productName, button);
      }
    }
  });

  // 6. Keyboard shortcuts
  $(document).on("keydown", function (e) {
    // Ctrl/Cmd + K = Focus search
    if ((e.ctrlKey || e.metaKey) && e.key === "k") {
      e.preventDefault();
      $("#search").focus();
    }
  });

  // ========== Initialize ==========
  // Load products on page load
  console.log("🚀 Page initialized - loading products...");
  loadProducts(1);

  // Show keyboard shortcut hint
  $("#search").attr("placeholder", "Tìm kiếm (Ctrl+K)...");

  // Add smooth scrolling
  $('a[href^="#"]').on("click", function (e) {
    const target = $(this.getAttribute("href"));
    if (target.length) {
      e.preventDefault();
      $("html, body")
        .stop()
        .animate(
          {
            scrollTop: target.offset().top - 20,
          },
          400,
        );
    }
  });

  // ========== Product Detail Modal ==========
  let detailAbortController = null;
  let detailRequestId = 0;

  $(document).on("click", ".btn-view-product", function (e) {
    e.preventDefault();
    const productId = $(this).data("id");
    const modal = $("#productDetailModal");
    const modalBody = $("#product-detail-content");
    const modalEditBtn = $("#modal-edit-button");

    if (detailAbortController) {
      detailAbortController.abort();
    }

    detailRequestId++;
    const thisDetailRequestId = detailRequestId;
    detailAbortController = new AbortController();

    // Hiển thị loading
    modalBody.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        `);

    // Show modal
    const bsModal = new bootstrap.Modal(modal, {
      backdrop: "static",
      keyboard: false,
    });
    bsModal.show();

    // Fetch detail
    $.ajax({
      url: SITE_URL + "?url=product/ajax_get_details",
      type: "GET",
      dataType: "json",
      timeout: 10000,
      data: {
        id: productId,
      },
      success: function (response) {
        if (thisDetailRequestId !== detailRequestId) {
          return;
        }

        if (response.success) {
          renderDetailModal(response.product, response.variants, productId);
          modalEditBtn.attr(
            "href",
            "index.php?url=product/edit&id=" + productId,
          );
        } else {
          modalBody.html(`
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-circle"></i> ${response.message || "Có lỗi xảy ra"}
                        </div>
                    `);
        }
      },
      error: function (xhr, status, error) {
        if (thisDetailRequestId !== detailRequestId) {
          return;
        }

        if (status === "abort") {
          return;
        }

        modalBody.html(`
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-circle"></i> Không thể tải chi tiết sản phẩm. Vui lòng thử lại.
                    </div>
                `);
      },
    });
  });

  function renderDetailModal(product, variants, productId) {
    const modalBody = $("#product-detail-content");

    let html = `
            <div class="modern-card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 text-center">
                            <img src="${product.image || SITE_URL.split("?")[0] + "/../assets/images/default-product.png"}"
                                class="img-fluid rounded shadow-sm"
                                alt="${escapeHtml(product.name)}"
                                style="max-height: 150px; object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <h5 class="mb-2">${escapeHtml(product.name)}</h5>
                            <span class="badge bg-secondary fs-6 mb-3">SKU: ${escapeHtml(product.sku)}</span>
                            <p class="text-muted small mb-2">
                                ${escapeHtml(product.description || "Chưa có mô tả")}
                            </p>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Danh mục:</strong> ${escapeHtml(product.category_name || "N/A")}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nhà cung cấp:</strong> ${escapeHtml(product.supplier_name || "N/A")}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

    if (variants && variants.length > 0) {
      html += `
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h6><i class="fas fa-boxes"></i> Biến thể (${variants.length})</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Màu</th>
                                        <th>Dung lượng</th>
                                        <th>Giá</th>
                                        <th>Tồn kho</th>
                                    </tr>
                                </thead>
                                <tbody>
            `;

      variants.forEach((v) => {
        const price = new Intl.NumberFormat("vi-VN").format(v.price);
        const stock = new Intl.NumberFormat("vi-VN").format(v.stock);
        html += `
                    <tr>
                        <td>${escapeHtml(v.color || "N/A")}</td>
                        <td>${escapeHtml(v.storage || "N/A")}</td>
                        <td><strong>${price} đ</strong></td>
                        <td><span class="badge bg-info">${stock}</span></td>
                    </tr>
                `;
      });

      html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
    }

    modalBody.html(html);
  }

  function escapeHtml(text) {
    if (!text) return "";
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  console.log("Product Management System Initialized");
});
