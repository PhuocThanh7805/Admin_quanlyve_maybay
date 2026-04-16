<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/PHPMailer/src/Exception.php');
require_once(__DIR__ . '/PHPMailer/src/PHPMailer.php');
require_once(__DIR__ . '/PHPMailer/src/SMTP.php');

if (!isset($mysqli)) {
    die("❌ Chưa kết nối database");
}

$mave = $_GET['mave'] ?? '';

if (empty($mave)) {
    die("❌ Thiếu mã vé");
}

$sql = "
SELECT 
    v.MAVE,
    hk.HOTEN AS TENHK,
    cb.MACHUYENBAY,
    cb.THOIGIANDI,
    dd1.TENDIADIEM AS DIEMDI,
    dd2.TENDIADIEM AS DIEMDEN,
    u.EMAIL,
    u.FULLNAME AS TENNGUOIDAT
FROM ve v
INNER JOIN hanhkhach hk 
    ON v.MAHANHKHACH = hk.MAHANHKHACH
INNER JOIN chuyenbay cb 
    ON v.MACHUYENBAY = cb.MACHUYENBAY
INNER JOIN tuyenbay tb 
    ON cb.MATUYEN = tb.MATUYEN
INNER JOIN sanbay sb1 
    ON tb.SANBAYDI = sb1.MASANBAY
INNER JOIN sanbay sb2 
    ON tb.SANBAYDEN = sb2.MASANBAY
INNER JOIN diadiem dd1 
    ON sb1.MADIADIEM = dd1.MADIADIEM
INNER JOIN diadiem dd2 
    ON sb2.MADIADIEM = dd2.MADIADIEM
LEFT JOIN chitiethoadon ct 
    ON v.MAVE = ct.MAVE
LEFT JOIN hoadon hd 
    ON ct.MAHOADON = hd.MAHOADON
INNER JOIN users u 
    ON hd.ID_USER = u.ID_USER
WHERE v.MAVE = ?
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $mave);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("❌ Không tìm thấy vé");
}

$row = $result->fetch_assoc();

$email = $row['EMAIL'];
if (empty($email)) {
    die("❌ Không có email người đặt vé");
}

//uu tien ng dat ve
$ten = $row['TENNGUOIDAT'] ?? $row['TENHK'];

//thoi gian 
$thoigian = !empty($row['THOIGIANDI']) 
    ? date('H:i d/m/Y', strtotime($row['THOIGIANDI'])) 
    : '---';

//noidung mail
$message = "
<div style='font-family:Arial;max-width:600px;margin:auto;border:1px solid #ddd;border-radius:10px;overflow:hidden;'>
    
    <div style='background:#004ecc;color:#fff;padding:15px;text-align:center;'>
        <h2>CAMEO SKY AIRLINES</h2>
    </div>

    <div style='padding:20px;'>

        <p>Xin chào <b>$ten</b>,</p>
        <p>Vé của bạn đã được xác nhận thành công.</p>

        <hr>

        <p><b>Mã vé:</b> {$row['MAVE']}</p>
        <p><b>Hành khách:</b> {$row['TENHK']}</p>
        <p><b>Chuyến bay:</b> {$row['MACHUYENBAY']}</p>
        <p><b>Lộ trình:</b> {$row['DIEMDI']} ➜ {$row['DIEMDEN']}</p>
        <p><b>Giờ khởi hành:</b> $thoigian</p>

        <hr>

        <p style='text-align:center;font-size:12px;color:gray;'>
            Cảm ơn bạn đã sử dụng dịch vụ 
        </p>

    </div>

</div>
";

/* GỬI MAIL */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'huynhphuocthanh.131019@gmail.com';
    $mail->Password   = 'gmvi hkjy puow rrvx'; // app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->CharSet = 'UTF-8';

    $mail->setFrom('huynhphuocthanh.131019@gmail.com', 'Cameo Sky Airlines');
    $mail->addAddress($email, $ten);

    $mail->isHTML(true);
    $mail->Subject = "✈️ Vé máy bay - " . $row['MAVE'];
    $mail->Body    = $message;

    $mail->send();

    echo "✅ Gửi mail thành công tới: $email";

} catch (Exception $e) {
    echo "❌ Lỗi gửi mail: " . $mail->ErrorInfo;
}
?>