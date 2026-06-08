<?php
require_once 'app/helpers/SessionHelper.php';
include 'app/views/shares/header.php';
?>

<div class="d-flex align-items-center mb-4" style="gap:15px;">
    <a href="/project1/Product/" class="back-btn btn" style="width:auto;padding:12px 18px;">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="mb-0" style="flex:1;">
        <i class="fa-solid fa-plus-circle"></i>
        Thêm sản phẩm mới
    </h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- form enctype multipart để hỗ trợ upload ảnh -->
        <form id="add-product-form" novalidate enctype="multipart/form-data">

            <!-- TÊN SẢN PHẨM -->
            <div class="form-group mb-3">
                <label for="name">
                    <i class="fa-solid fa-box" style="color:#ff4d4d;margin-right:6px;"></i>
                    Tên sản phẩm <span style="color:#ff003c;">*</span>
                </label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Nhập tên sản phẩm...">
                <div class="invalid-feedback" id="err-name"></div>
            </div>

            <!-- MÔ TẢ -->
            <div class="form-group mb-3">
                <label for="description">
                    <i class="fa-solid fa-file-lines" style="color:#ff4d4d;margin-right:6px;"></i>
                    Mô tả <span style="color:#ff003c;">*</span>
                </label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Nhập mô tả sản phẩm..."></textarea>
                <div class="invalid-feedback" id="err-description"></div>
            </div>

            <div class="row">
                <!-- GIÁ -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="price">
                            <i class="fa-solid fa-tag" style="color:#ff4d4d;margin-right:6px;"></i>
                            Giá (VND) <span style="color:#ff003c;">*</span>
                        </label>
                        <input type="number" id="price" name="price" class="form-control" min="0" step="1000" placeholder="0">
                        <div class="invalid-feedback" id="err-price"></div>
                        <small id="price-preview" style="color:#00ff99;margin-top:5px;display:block;"></small>
                    </div>
                </div>

                <!-- DANH MỤC -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="category_id">
                            <i class="fa-solid fa-layer-group" style="color:#ff4d4d;margin-right:6px;"></i>
                            Danh mục <span style="color:#ff003c;">*</span>
                        </label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">-- Đang tải danh mục... --</option>
                        </select>
                        <div class="invalid-feedback" id="err-category_id"></div>
                    </div>
                </div>
            </div>

            <!-- HÌNH ẢNH SẢN PHẨM -->
            <div class="form-group mb-3">
                <label for="image">
                    <i class="fa-solid fa-image" style="color:#ff4d4d;margin-right:6px;"></i>
                    Hình ảnh sản phẩm
                </label>
                <div class="image-upload-area" id="image-upload-area">
                    <input type="file" id="image" name="image" accept="image/*" style="display:none;">
                    <div id="image-placeholder" onclick="$('#image').click()" style="cursor:pointer;text-align:center;padding:30px;border:2px dashed rgba(255,77,77,.4);border-radius:12px;transition:.3s;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:36px;color:#ff4d4d;margin-bottom:10px;display:block;"></i>
                        <p style="color:#94a3b8;margin:0;">Nhấn để chọn ảnh<br><small>JPG, PNG, GIF, WEBP - Tối đa 5MB</small></p>
                    </div>
                    <div id="image-preview-wrap" style="display:none;position:relative;text-align:center;">
                        <img id="image-preview-img" src="" alt="Preview" style="max-height:200px;border-radius:12px;border:2px solid rgba(255,77,77,.3);">
                        <button type="button" id="remove-image" style="position:absolute;top:5px;right:5px;background:#ff003c;border:none;color:#fff;border-radius:50%;width:28px;height:28px;cursor:pointer;font-size:14px;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                <div class="invalid-feedback" id="err-image"></div>
            </div>

            <!-- SUBMIT -->
            <div class="d-flex" style="gap:12px;margin-top:10px;">
                <button type="submit" class="btn add-product-btn" id="submit-btn" style="flex:1;">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Thêm sản phẩm
                </button>
                <a href="/project1/Product/" class="btn back-btn" style="flex:0 0 auto;padding:12px 20px;">
                    Hủy
                </a>
            </div>

        </form>
    </div>

    <!-- PREVIEW PANEL -->
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div style="background:#1e293b;border-radius:20px;padding:25px;position:sticky;top:20px;">
            <h5 style="color:#ff4d4d;margin-bottom:20px;">
                <i class="fa-solid fa-eye"></i> Xem trước
            </h5>
            <div style="background:#0f172a;border-radius:15px;padding:20px;min-height:200px;">
                <div id="preview-img-wrap" style="margin-bottom:12px;display:none;">
                    <img id="preview-img" src="" alt="" style="width:100%;border-radius:10px;max-height:150px;object-fit:cover;">
                </div>
                <div id="preview-name" style="font-size:18px;font-weight:bold;color:#ff4d4d;margin-bottom:10px;">Tên sản phẩm</div>
                <div id="preview-cat" style="color:#94a3b8;font-size:13px;margin-bottom:10px;">
                    <i class="fa-solid fa-layer-group"></i> Chưa chọn danh mục
                </div>
                <div id="preview-desc" style="color:#cbd5e1;font-size:14px;margin-bottom:15px;min-height:60px;">
                    Mô tả sản phẩm sẽ hiển thị ở đây...
                </div>
                <div id="preview-price" style="color:#00ff99;font-size:28px;font-weight:bold;">0 đ</div>
            </div>
            <div class="mt-3" style="color:#64748b;font-size:12px;text-align:center;">
                <i class="fa-solid fa-info-circle"></i> Bản xem trước theo thời gian thực
            </div>
        </div>
    </div>
</div>

<!-- TOAST -->
<div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:99999;"></div>

<style>
.form-control.is-invalid { border-color: #ff003c !important; box-shadow: 0 0 8px rgba(255,0,60,.3) !important; }
.invalid-feedback { display:none; color:#ff4d4d; font-size:13px; margin-top:5px; }
.form-control.is-invalid + .invalid-feedback,
.is-invalid ~ .invalid-feedback { display: block; }
.toast-msg { background:#1e293b; color:#fff; border-left:4px solid #00ff99; padding:15px 20px; border-radius:10px; margin-bottom:10px; box-shadow:0 4px 15px rgba(0,0,0,.4); animation: slideIn .3s ease; min-width:280px; }
.toast-msg.error { border-left-color: #ff003c; }
@keyframes slideIn { from { transform: translateX(100px); opacity:0; } to { transform: translateX(0); opacity:1; } }
#submit-btn:disabled { opacity:.6; cursor:not-allowed; }
#image-placeholder:hover { border-color: #ff4d4d !important; background:rgba(255,77,77,.05); }
</style>

<script>
$(function () {
    const BASE = window.location.protocol + '//' + window.location.host + '/project1';

    // ========== TOAST ==========
    function showToast(msg, type = 'success') {
        const t = $('<div>').addClass('toast-msg' + (type === 'error' ? ' error' : '')).html(
            `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-xmark'}" style="margin-right:8px;"></i>${msg}`
        );
        $('#toast-container').append(t);
        setTimeout(() => t.fadeOut(400, () => t.remove()), 3500);
    }

    // ========== LOAD CATEGORIES ==========
    $.ajax({
        url: BASE + '/api/category',
        method: 'GET',
        success: function (cats) {
            const sel = $('#category_id').empty().append('<option value="">-- Chọn danh mục --</option>');
            cats.forEach(c => sel.append(`<option value="${c.id}">${c.name}</option>`));
        },
        error: function () {
            showToast('Không thể tải danh mục!', 'error');
        }
    });

    // ========== IMAGE UPLOAD PREVIEW ==========
    $('#image').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            showToast('File ảnh không được vượt quá 5MB!', 'error');
            $(this).val('');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            $('#image-placeholder').hide();
            $('#image-preview-img').attr('src', e.target.result);
            $('#image-preview-wrap').show();
            // Preview panel
            $('#preview-img').attr('src', e.target.result);
            $('#preview-img-wrap').show();
        };
        reader.readAsDataURL(file);
    });

    $('#remove-image').on('click', function () {
        $('#image').val('');
        $('#image-preview-img').attr('src', '');
        $('#image-preview-wrap').hide();
        $('#image-placeholder').show();
        $('#preview-img-wrap').hide();
    });

    // ========== LIVE PREVIEW ==========
    $('#name').on('input', function () {
        $('#preview-name').text($(this).val() || 'Tên sản phẩm');
    });
    $('#description').on('input', function () {
        $('#preview-desc').text($(this).val() || 'Mô tả sản phẩm sẽ hiển thị ở đây...');
    });
    $('#price').on('input', function () {
        const v = parseInt($(this).val()) || 0;
        const fmt = v.toLocaleString('vi-VN') + ' đ';
        $('#preview-price').text(fmt);
        $('#price-preview').text(v > 0 ? '≈ ' + fmt : '');
    });
    $('#category_id').on('change', function () {
        const txt = $(this).find('option:selected').text();
        $('#preview-cat').html(`<i class="fa-solid fa-layer-group"></i> ${txt || 'Chưa chọn danh mục'}`);
    });

    // ========== VALIDATE ==========
    function clearErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();
    }

    function setError(field, msg) {
        $(`#${field}`).addClass('is-invalid');
        $(`#err-${field}`).text(msg).show();
    }

    function validateForm() {
        clearErrors();
        let valid = true;
        if (!$('#name').val().trim()) { setError('name', 'Tên sản phẩm không được để trống'); valid = false; }
        if (!$('#description').val().trim()) { setError('description', 'Mô tả không được để trống'); valid = false; }
        const price = $('#price').val();
        if (!price || isNaN(price) || Number(price) < 0) { setError('price', 'Giá sản phẩm không hợp lệ'); valid = false; }
        if (!$('#category_id').val()) { setError('category_id', 'Vui lòng chọn danh mục'); valid = false; }
        return valid;
    }

    // ========== SUBMIT (dùng FormData để gửi cả file) ==========
    $('#add-product-form').on('submit', function (e) {
        e.preventDefault();

        if (!validateForm()) return;

        const btn = $('#submit-btn');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...');

        const formData = new FormData(this);

        $.ajax({
            url: BASE + '/api/product',
            method: 'POST',
            data: formData,
            processData: false,    // QUAN TRỌNG: không encode FormData
            contentType: false,    // QUAN TRỌNG: để browser tự set multipart boundary
            success: function (res) {
                if (res.message === 'Product created successfully') {
                    showToast('Thêm sản phẩm thành công!');
                    setTimeout(() => { window.location.href = BASE + '/Product/'; }, 1200);
                } else {
                    showToast('Thêm sản phẩm thất bại!', 'error');
                    btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Thêm sản phẩm');
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Thêm sản phẩm');
                try {
                    const res = xhr.responseJSON || JSON.parse(xhr.responseText);
                    if (res && res.errors) {
                        $.each(res.errors, (field, msg) => setError(field, msg));
                        showToast('Vui lòng kiểm tra lại thông tin!', 'error');
                    } else {
                        showToast('Đã xảy ra lỗi: ' + (res.message || 'Unknown'), 'error');
                    }
                } catch(ex) {
                    showToast('Đã xảy ra lỗi không xác định!', 'error');
                }
            }
        });
    });

    // ========== CLEAR ERROR ON INPUT ==========
    $('.form-control').on('input change', function () {
        $(this).removeClass('is-invalid');
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
