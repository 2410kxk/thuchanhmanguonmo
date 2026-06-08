<?php require_once 'app/helpers/SessionHelper.php'; SessionHelper::start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu – 2410KXK Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/project1/public/css/style.css">
    <style>
        body { background:#f4f6fb; }
        .auth-wrapper { min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .auth-card { background:#fff; border-radius:16px; box-shadow:0 4px 32px rgba(0,0,0,.10); padding:40px 36px; width:100%; max-width:460px; }
        .auth-logo { font-size:1.8rem; font-weight:800; color:#e63946; letter-spacing:1px; text-decoration:none; }
        .auth-logo:hover { color:#c1121f; text-decoration:none; }
        .auth-title { font-size:1.2rem; font-weight:700; color:#222; margin:8px 0 24px; }
        .form-control:focus { border-color:#e63946; box-shadow:0 0 0 .2rem rgba(230,57,70,.18); }
        .btn-auth { background:#e63946; color:#fff; font-weight:700; border:none; border-radius:8px; padding:10px; width:100%; font-size:1rem; }
        .btn-auth:hover { background:#c1121f; color:#fff; }
        .input-group-text { background:#f4f6fb; border-right:none; }
        .form-control { border-left:none; }
        .strength-bar { height:5px; border-radius:3px; transition:all .3s; margin-top:4px; }
        .auth-link { color:#e63946; font-weight:600; }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <a class="auth-logo" href="/project1/Product">2410KXK</a>
        <div class="auth-title"><i class="fa fa-key mr-2"></i>Đặt lại mật khẩu</div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $err): ?>
                    <div><i class="fa fa-circle-exclamation mr-1"></i><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/project1/User/resetPasswordPost" method="POST" novalidate>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label class="font-weight-bold">Mật khẩu mới <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-lock text-muted"></i></span>
                    </div>
                    <input type="password" name="new_password" id="newPw" class="form-control"
                           placeholder="Tối thiểu 6 ký tự" required oninput="checkStrength(this.value)">
                </div>
                <div id="strengthBar" class="strength-bar" style="width:0%;background:#aaa"></div>
                <small id="strengthText" class="text-muted"></small>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-check-double text-muted"></i></span>
                    </div>
                    <input type="password" name="confirm_password" id="confirmPw" class="form-control"
                           placeholder="Nhập lại mật khẩu mới" required oninput="checkMatch()">
                </div>
                <small id="matchText"></small>
            </div>

            <button type="submit" class="btn-auth mt-2">
                <i class="fa fa-floppy-disk mr-1"></i> Đặt lại mật khẩu
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="/project1/User/login" class="auth-link" style="font-size:.9rem;">
                <i class="fa fa-arrow-left mr-1"></i>Quay lại đăng nhập
            </a>
        </div>
    </div>
</div>
<script>
function checkStrength(val){
    var bar=document.getElementById('strengthBar'),txt=document.getElementById('strengthText');
    if(!val.length){bar.style.width='0%';txt.textContent='';return;}
    var s=0;
    if(val.length>=6)s++;if(val.length>=10)s++;
    if(/[A-Z]/.test(val))s++;if(/[0-9]/.test(val))s++;if(/[^A-Za-z0-9]/.test(val))s++;
    var c=['#dc3545','#dc3545','#fd7e14','#ffc107','#28a745'];
    var l=['Rất yếu','Yếu','Trung bình','Mạnh','Rất mạnh'];
    bar.style.width=(s*20)+'%';bar.style.background=c[s-1]||'#aaa';
    txt.textContent=l[s-1]||'';txt.style.color=c[s-1]||'#aaa';
}
function checkMatch(){
    var a=document.getElementById('newPw').value,b=document.getElementById('confirmPw').value,t=document.getElementById('matchText');
    if(!b.length){t.textContent='';return;}
    t.textContent=a===b?'✓ Khớp':'✗ Chưa khớp';
    t.style.color=a===b?'#28a745':'#dc3545';
}
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
