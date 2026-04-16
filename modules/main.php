<style>
body{
    margin:0;
    font-family:'Plus Jakarta Sans',sans-serif;
}

/* WRAPPER */
.wrapper{
    display:flex;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    position:fixed;
    top:80px; /* 🔥 QUAN TRỌNG: né header */
    left:0;
    bottom:0;
    background:#3461ba;
    overflow-y:auto;
    z-index:900;
}

/* MAIN */
.main{
    margin-left:260px; /* chừa sidebar */
    margin-top:80px;   /* chừa header */
    padding:20px;
    width:calc(100% - 260px);
    min-height:calc(100vh - 80px);
    box-sizing:border-box;
    background:#f1f5f9;
}
</style>

<div class="wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <?php include("sidebar/sidebar.php"); ?>
    </aside>

    <!-- MAIN -->
    <main class="main">
        <?php
        $action = $_GET['action'] ?? '';
        $query  = $_GET['query'] ?? '';

        switch ($action) {

            case 'them':
                include("modules/quanlychuyenbay/them.php");
                break;

            case 'sua':
                include("modules/quanlychuyenbay/sua.php");
                break;

            case 'xuly':
                include("modules/quanlychuyenbay/xuly.php");
                break;

            case 'lietke':
                include("modules/quanlychuyenbay/lietke.php");
                break;

            case 'chitiet_chuyenbay':
                include("modules/quanlychuyenbay/chitiet.php");
                break;

            case 'them_maybay':
                include("modules/quanlymaybay/them_maybay.php");
                break;

            case 'sua_maybay':
                include("modules/quanlymaybay/sua_maybay.php");
                break;

            case 'xuly_maybay':
                include("modules/quanlymaybay/xuly_maybay.php");
                break;

            case 'lietke_maybay':
                include("modules/quanlymaybay/lietke_maybay.php");
                break;

            case 'quanlyghe':
                if ($query === 'chon_maybay') {
                    include("modules/quanlyghe/chon_maybay.php");
                } elseif ($query === 'sodo_ghe') {
                    include("modules/quanlyghe/sodo_ghe.php");
                }
                break;

            case 'hienthive':
                include("modules/quanlyve/hienthive.php");
                break;

            case 'chitietve':
                include("modules/quanlyve/chitietve.php"); 
                break;

            case 'khachhang':
                include("modules/quanlynguoidung/khachhang.php");
                break;

            case 'lichsu_hanhkhach':
                include("modules/quanlynguoidung/lichsu_hanhkhach.php");
                break;

            case 'nhanvien':
                include("modules/quanlynguoidung/nhanvien.php");
                break;

            case 'sua_nguoidung':
                include("modules/quanlynguoidung/sua_nguoidung.php");
                break;

            case 'lietke_tuyenbay':
                include("modules/quanlytuyenbay/lietke_tuyen.php");
                break;

            case 'them_tuyenbay':
                include("modules/quanlytuyenbay/them_tuyen.php");
                break;

            case 'sua_tuyenbay':
                include("modules/quanlytuyenbay/sua_tuyen.php");
                break;

            case 'lietke_lichbay':
                include("modules/quanlylichbay/lietke_lichbay.php");
                break;

            case 'themlichbay':
                include("modules/quanlylichbay/them_lichbay.php");
                break;

            case 'sualichbay':
                include("modules/quanlylichbay/sua_lichbay.php");
                break;

            case 'phantich':
                include("modules/phantich.php");
                break;

            case 'gui':
                include("modules/guimail/gui.php");
                break;

            default:
                include("modules/dashboard.php");
                break;
        }
        ?>
    </main>

</div>