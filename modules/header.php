<style>
.admin-header{
height:80px;
position:fixed;
top:0;
left:0;
right:0;
z-index:1000;

display:flex;
justify-content:space-between;
align-items:center;

padding:0 25px;
box-sizing:border-box;

/* GIỮ NGUYÊN NỀN */
background: linear-gradient(135deg, #0072ff, #00c6ff);

box-shadow:0 4px 20px rgba(0,0,0,0.25);
}

/* LEFT */
.header-left{
display:flex;
align-items:center;
gap:18px;
}

/* LOGO (GIẢM CHÓI) */
.logo{
font-size:22px;
font-weight:800;
color:#f1f5f9; /* trắng dịu */
letter-spacing:0.5px;
}

.logo span{
color:rgba(255,255,255,0.7);
font-weight:300;
font-size:13px;
margin-left:5px;
}

/* TITLE */
.page-title h2{
margin:0;
font-size:18px;
font-weight:600;
color:#f8fafc; /* trắng nhẹ */
}

.page-title small{
color:rgba(255,255,255,0.7);
font-size:12px;
}

/* RIGHT */
.header-right{
display:flex;
align-items:center;
}

/* USER */
.user-block{
display:flex;
align-items:center;
gap:10px;
background:rgba(255,255,255,0.12); /* dịu hơn */
padding:6px 10px 6px 15px;
border-radius:10px;
backdrop-filter:blur(4px);
}

/* TEXT */
.user-text strong{
display:block;
font-size:14px;
color:#ffffff;
}

.user-text small{
font-size:11px;
color:rgba(255,255,255,0.75);
}

/* AVATAR */
.user-block img{
width:40px;
height:40px;
border-radius:8px;
object-fit:cover;
border:2px solid rgba(255,255,255,0.7);
}
</style>

<header class="admin-header">
    
    <div class="header-left">

        <!-- ĐÃ BỎ NÚT TOGGLE -->

        <div class="logo">
            ✈ CAMEO SKY <span>Admin</span>
        </div>

        <div class="page-title">
            <h2>TỔNG QUAN HỆ THỐNG</h2>
            <small><?= date("d/m/Y") ?></small>
        </div>

    </div>

    <div class="header-right">

        <div class="user-block">
            <div class="user-text">
                <strong>Admin</strong>
                <small>Quản trị viên</small>
            </div>

            <img src="images/meomeo.jpg" alt="User Avatar">
        </div>

    </div>

</header>