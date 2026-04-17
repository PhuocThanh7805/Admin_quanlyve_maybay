<style>
    .form-container {
        max-width: 750px;
        margin: 30px auto;
        background: #fff;
        border-radius: 12px;
        border: 1px solid #ddd;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        font-family: 'Segoe UI', Tahoma, sans-serif;
        overflow: hidden;
    }
    .form-container .page-title {
        margin: 0 !important; 
        padding: 18px 25px;
        font-size: 18px;
        font-weight: bold;
        background: linear-gradient(135deg, #0072ff, #00c6ff) !important; /* Xanh dương */
        color: #fff !important;
        text-transform: uppercase;
        border: none;
    }
    .form-container form {
        padding: 25px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #555;
        margin-bottom: 6px;
        text-transform: uppercase;
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 13px;
        transition: 0.2s;
        box-sizing: border-box; /* Đảm bảo padding không làm tràn khung */
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #0072ff;
        box-shadow: 0 0 5px rgba(0,114,255,0.3);
        outline: none;
    }
    .btn-submit {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: bold;
        color: #fff !important;
        border: none;
        cursor: pointer;
        background: linear-gradient(135deg, #0072ff, #00c6ff) !important;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(0,114,255,0.3);
        display: inline-block;
        text-decoration: none;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,114,255,0.4);
        opacity: 0.9;
    }
    .btn-back {
        font-size: 13px;
        margin-left: 15px;
        color: #666;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-back:hover {
        color: #e74c3c;
        text-decoration: underline;
    }
</style>

<div class="form-container">
    <h2 class="page-title">Thêm máy bay mới</h2>
    <form method="POST" action="modules/quanlymaybay/xuly_maybay.php">
        <div class="form-group">
            <label>Mã máy bay</label>
            <input type="text" name="MAMAYBAY" placeholder="Ví dụ: VN-A321" required>
        </div>
        <div class="form-group">
            <label>Tên máy bay</label>
            <input type="text" name="TENMAYBAY" placeholder="Ví dụ: Airbus A321" required>
        </div>

        <div style="display: flex; gap: 10px;">
            <div class="form-group" style="flex: 1;">
                <label>Ghế hạng nhất (FC)</label>
                <input type="number" name="SOGHE_FC" value="0" min="0" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Ghế thương gia (BC)</label>
                <input type="number" name="SOGHE_BC" value="0" min="0" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Ghế phổ thông (EC)</label>
                <input type="number" name="SOGHE_EC" value="0" min="0" required>
            </div>
        </div>
        <div class="form-group">
            <label>Hãng bay</label>
            <select name="MAHANG" required>
                <option value="">-- Chọn hãng --</option>
                <?php
                $sql_hang = "SELECT * FROM hangmaybay ORDER BY TENHANG ASC";
                $query_hang = mysqli_query($mysqli, $sql_hang);
                while($row = mysqli_fetch_array($query_hang)){
                    echo '<option value="'.$row['MAHANG'].'">'.$row['TENHANG'].'</option>';
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="TRANGTHAI">
                <option value="1">Đang hoạt động</option>
                <option value="0">Ngưng hoạt động</option>
            </select>
        </div>

        <div style="margin-top: 25px; display: flex; align-items: center;">
            <button type="submit" name="them_maybay" class="btn-submit">XÁC NHẬN THÊM</button>
            <a href="index.php?action=lietke_maybay" class="btn-back">
    Quay lại
</a>
        </div>
    </form>
</div>
