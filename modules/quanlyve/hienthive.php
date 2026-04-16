<?php
$ngay = $_GET['ngay'] ?? '';
$machuyenbay = $_GET['machuyenbay'] ?? '';
?>

<!-- Import Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ... (Giữ nguyên phần Style của bạn) ... */
:root {
    --primary-gradient: linear-gradient(135deg, #0072ff, #00c6ff);
    --primary-blue: #0072ff;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
    --text-main: #1e293b;
    --text-muted: #64748b;
}
body { background: #f1f5f9; font-family: 'Inter', sans-serif; color: var(--text-main); }
.form-backend { background: #ffffff; padding: 30px; border-radius: 12px; max-width: 1300px; margin: 30px auto; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
.form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color); }
.form-header h3 { margin: 0; font-size: 20px; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.5px; }
.search-filter-container { background: var(--bg-light); padding: 20px; border-radius: 10px; border: 1px solid var(--border-color); margin-bottom: 30px; }
.search-box { display: flex; gap: 20px; align-items: flex-end; }
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-group label { font-size: 12px; font-weight: 700; color: var(--text-muted); }
.search-box input, .search-box select { padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; font-size: 14px; min-width: 200px; transition: all 0.2s; }
.table-container { overflow-x: auto; border-radius: 10px; border: 1px solid var(--border-color); }
.table-custom { width: 100%; border-collapse: collapse; background: white; }
.table-custom th { background: #f1f5f9; font-size: 15px; font-weight: 700; color: var(--text-muted); padding: 15px; text-align: left; text-transform: uppercase; }
.table-custom td { padding: 16px 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; vertical-align: middle; }
.table-custom tr:hover { background: #f8fafc; }
.status-pill { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-block; }
.paid-pill { background: #dcfce7; color: #166534; }
.hold-pill { background: #fef9c3; color: #854d0e; }
.unpaid-pill { background: #fee2e2; color: #991b1b; }
.group-title { margin: 35px 0 15px 0; padding: 12px 18px; border-left: 5px solid var(--primary-blue); background: #f1f5f9; display: flex; justify-content: space-between; align-items: center; border-radius: 0 8px 8px 0; }
.action-btn { padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #fff; text-decoration: none; background: var(--primary-gradient); transition: all 0.3s; text-align: center; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(0, 114, 255, 0.2); }
.action-btn.secondary { background: #fff; color: var(--text-main); border: 1px solid var(--border-color); box-shadow: none; }
.welcome-placeholder { text-align: center; padding: 80px 20px; background: var(--bg-light); border-radius: 12px; border: 2px dashed #cbd5e1; color: var(--text-muted); }
</style>

<div class="form-backend">
    <div class="form-header">
        <h3>🎫 Quản lý vé theo chuyến bay</h3>
    </div>

    <!-- FILTER SECTION -->
    <div class="search-filter-container">
        <form method="GET" action="index.php" class="search-box">
            <input type="hidden" name="action" value="hienthive">
            <div class="filter-group">
                <label>NGÀY KHỞI HÀNH</label>
                <input type="date" name="ngay" value="<?= $ngay ?>" onchange="this.form.submit()">
            </div>

            <?php if($ngay): ?>
                <div class="filter-group">
                    <label>CHỌN CHUYẾN BAY TRONG NGÀY</label>
                    <select name="machuyenbay" onchange="this.form.submit()">
                        <option value="">-- Danh sách chuyến bay --</option>
                        <?php
                        $q_cb = mysqli_query($mysqli, "
                            SELECT cb.*, dd1.TENDIADIEM AS DI, dd2.TENDIADIEM AS DEN
                            FROM chuyenbay cb
                            JOIN tuyenbay tb ON cb.MATUYEN = tb.MATUYEN
                            JOIN sanbay sb1 ON tb.SANBAYDI = sb1.MASANBAY
                            JOIN sanbay sb2 ON tb.SANBAYDEN = sb2.MASANBAY
                            JOIN diadiem dd1 ON sb1.MADIADIEM = dd1.MADIADIEM
                            JOIN diadiem dd2 ON sb2.MADIADIEM = dd2.MADIADIEM
                            WHERE DATE(cb.THOIGIANDI) = '$ngay'
                            ORDER BY cb.THOIGIANDI ASC
                        ");
                        while($r = mysqli_fetch_assoc($q_cb)){
                            $sel = ($machuyenbay == $r['MACHUYENBAY']) ? 'selected' : '';
                            echo "<option value='{$r['MACHUYENBAY']}' $sel>
                                [{$r['MACHUYENBAY']}] {$r['DI']} → {$r['DEN']} (".date('H:i', strtotime($r['THOIGIANDI'])).")
                            </option>";
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- DATA AREA -->
    <?php if(!$ngay): ?>
        <div class="welcome-placeholder">
            <p style="font-size: 16px;">Vui lòng chọn <b>Ngày khởi hành</b> để bắt đầu tra cứu.</p>
        </div>
    <?php elseif($ngay && !$machuyenbay): ?>
        <div class="welcome-placeholder">
            <p>Ngày <b><?= date('d/m/Y', strtotime($ngay)) ?></b> có các chuyến bay như danh sách phía trên.</p>
            <p>Vui lòng chọn một <b>Chuyến bay cụ thể</b> để quản lý danh sách vé.</p>
        </div>
    <?php else: 
        $id_cb = mysqli_real_escape_string($mysqli, $machuyenbay);
        
        //lay email ng dat ve
        $sql = "SELECT v.*, g.MALOAIGHE, 
                       u.EMAIL AS EMAIL_USER, 
                       u.FULLNAME AS TEN_NGUOI_DAT,
                CASE 
                    WHEN hd.TRANGTHAIHOADON = 'DA_THANH_TOAN' THEN 'paid'
                    WHEN gc.MAGIUCHO IS NOT NULL THEN 'hold'
                    ELSE 'unpaid'
                END AS TRANGTHAI
                FROM ve v
                JOIN ghe g ON v.MAGHE = g.MAGHE
                LEFT JOIN chitiethoadon ct ON v.MAVE = ct.MAVE
                LEFT JOIN hoadon hd ON ct.MAHOADON = hd.MAHOADON
                LEFT JOIN users u ON hd.ID_USER = u.ID_USER
                LEFT JOIN giu_cho_tam gc ON v.MACHUYENBAY = gc.MACHUYENBAY AND v.MAGHE = gc.MAGHE
                WHERE v.MACHUYENBAY = '$id_cb'
                ORDER BY v.MAGHE ASC";

        $res = mysqli_query($mysqli, $sql);
        $groups = ['paid' => [], 'hold' => [], 'unpaid' => []];
        while($r = mysqli_fetch_assoc($res)){ $groups[$r['TRANGTHAI']][] = $r; }

        $labels = [
            'paid' => ['title' => 'DANH SÁCH VÉ ĐÃ THANH TOÁN', 'class' => 'paid-pill', 'text' => 'Đã thanh toán'],
            'hold' => ['title' => 'DANH SÁCH VÉ ĐANG GIỮ CHỖ', 'class' => 'hold-pill', 'text' => 'Đang giữ chỗ'],
            'unpaid' => ['title' => 'DANH SÁCH VÉ CHƯA THANH TOÁN', 'class' => 'unpaid-pill', 'text' => 'Chưa thanh toán']
        ];

        $has_data = false;
        foreach($labels as $key => $meta):
            if(!empty($groups[$key])):
                $has_data = true;
    ?>
                <div class="group-title">
                    <span><?= $meta['title'] ?></span>
                    <span style="font-weight: 500; font-size: 13px; color: var(--text-muted);">Số lượng: <?= count($groups[$key]) ?></span>
                </div>
                
                <div class="table-container">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Mã vé</th>
                                <th>Ghế</th>
                                <th>Thông tin Hành khách & Người đặt</th>
                                <th>Giá vé</th>
                                <th>Trạng thái</th>
                                <th width="120">Xem vé</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($groups[$key] as $v): ?>
                            <tr>
                                <td><b style="color:var(--primary-blue);"><?= $v['MAVE'] ?></b></td>
                                <td>
                                    <div style="font-weight: 700;"><?= $v['MAGHE'] ?></div>
                                    <div style="font-size: 11px; color: var(--text-muted);"><?= $v['MALOAIGHE'] ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a;"><?= mb_uppercase($v['HOTEN']) ?></div>
                                    
                                    <!-- HIỂN THỊ EMAIL VÀ TÊN NGƯỜI ĐẶT ĐỂ QUẢN LÝ -->
                                    <div style="margin-top: 5px; padding: 5px 8px; background: #f0f7ff; border-radius: 4px; display: inline-block;">
                                        <div style="color: #2563eb; font-size: 12px; font-weight: 600;">
                                            ✉ <?= $v['EMAIL_USER'] ?? 'Chưa có Email' ?>
                                        </div>
                                        <div style="color: #64748b; font-size: 11px;">
                                            Người đặt: <?= $v['TEN_NGUOI_DAT'] ?? 'Khách vãng lai' ?>
                                        </div>
                                    </div>

                                    <div style="font-size:11px; color: var(--text-muted); margin-top: 5px;">
                                        <?= $v['GIOITINH'] ?> • SĐT: <?= $v['SDT'] ?>
                                    </div>
                                </td>
                                <td><span style="font-weight: 700; color: #e11d48;"><?= number_format($v['GIAVE'], 0, ',', '.') ?>đ</span></td>
                                <td><span class="status-pill <?= $meta['class'] ?>"><?= $meta['text'] ?></span></td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:6px;">
                                        <a href="index.php?action=chitietve&mave=<?= $v['MAVE'] ?>" class="action-btn secondary">Chi tiết</a>                        
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
    <?php 
            endif;
        endforeach;

        if(!$has_data): ?>
            <div class="welcome-placeholder">
                <p>Chuyến bay này hiện chưa có dữ liệu đặt vé.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
function mb_uppercase($str) {
    return mb_convert_case($str, MB_CASE_UPPER, "UTF-8");
}
?>