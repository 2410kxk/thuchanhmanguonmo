<?php require_once 'app/helpers/SessionHelper.php'; SessionHelper::requireLogin(); ?>
<?php include 'app/views/shares/header.php'; ?>

<style>
.pw-card { background:#fff; border-radius:16px; box-shadow:0 2px 20px rgba(0,0,0,.08); padding:36px; max-width:520px; margin:0 auto; }
.pw-header { font-size:1.2rem; font-weight:800; color:#222; margin-bottom:24px; }
.form-control:focus { border-color:#e63946; box-shadow:0 0 0 .2rem rgba(230,57,70,.15); }
.btn-pw { background:#e63946; color:#fff; border:none; border-radius:8px; padding:10px 28px; font-weight:700; }
.btn-pw:hover { background:#c1121f; color:#fff; }
.strength-bar { height:5px; border-radius:3px; transition:all .3s; margin-top:5px; }
</style>

<div class="pw-card">
    <div class="pw-header"><i class="fa fa-lock mr-2 text-danger"></i>Đổi mật khẩu</div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><i class="fa fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?><div><i class="fa fa-exclamation-circle mr-1"></i><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/project1/User/changePasswordPost" method="POST" novalidate>

        <div class="form-group">
            <label class="font-weight-bold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" style="background:#f4f6fb;border-right:none"><i class="fa fa-lock text-muted"></i></span>
                </div>
                <input type="password" name="current_password" class="form-control" style="border-left:none" required placeholder="Nhập mật khẩu hiện tại">
            </div>
        </div>

        <div class="form-group">
            <label class="font-weight-bold">Mật khẩu mới <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" style="background:#f4f6fb;border-right:none"><i class="fa fa-key text-muted"></i></span>
                </div>
                <input type="password" name="new_password" id="newPw" class="form-control" style="border-left:none" required placeholder="Tối thiểu 6 ký tự" oninput="checkStrength(this.value)">
            </div>
            <div id="strengthBar" class="strength-bar bg-secondary" style="width:0%"></div>
            <small id="strengthText" class="text-muted"></small>
        </div>

        <div class="form-group">
            <label class="font-weight-bold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" style="background:#f4f6fb;border-right:none"><i class="fa fa-check-double text-muted"></i></span>
                </div>
                <input type="password" name="confirm_password" id="confirmPw" class="form-control" style="border-left:none" required placeholder="Nhập lại mật khẩu mới" oninput="checkMatch()">
            </div>
            <small id="matchText"></small>
        </div>

        <div class="d-flex align-items-center gap-3 mt-3">
            <button type="submit" class="btn btn-pw">
                <i class="fa fa-floppy-disk mr-1"></i> Đổi mật khẩu
            </button>
            <a href="/project1/User/profile" class="btn btn-outline-secondary ml-3">Quay lại</a>
        </div>
    </form>
</div>

<script>
function checkStrength(val){
    var bar=document.getElementById('strengthBar'), txt=document.getElementById('strengthText');
    if(val.length===0){bar.style.width='0%';txt.textContent='';return;}
    var score=0;
    if(val.length>=6)score++;
    if(val.length>=10)score++;
    if(/[A-Z]/.test(val))score++;
    if(/[0-9]/.test(val))score++;
    if(/[^A-Za-z0-9]/.test(val))score++;
    var colors=['#dc3545','#dc3545','#fd7e14','#ffc107','#28a745'];
    var labels=['Rất yếu','Yếu','Trung bình','Mạnh','Rất mạnh'];
    bar.style.width=(score*20)+'%';
    bar.style.background=colors[score-1]||'#aaa';
    txt.textContent=labels[score-1]||'';
    txt.style.color=colors[score-1]||'#aaa';
}
function checkMatch(){
    var a=document.getElementById('newPw').value,
        b=document.getElementById('confirmPw').value,
        t=document.getElementById('matchText');
    if(b.length===0){t.textContent='';return;}
    if(a===b){t.textContent='✓ Khớp'; t.style.color='#28a745';}
    else{t.textContent='✗ Chưa khớp'; t.style.color='#dc3545';}
}
</script>

<?php include 'app/views/shares/footer.php'; ?>
