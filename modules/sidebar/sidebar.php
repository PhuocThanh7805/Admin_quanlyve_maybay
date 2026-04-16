<style>
:root{
--sidebar-bg:#0f172a;
--accent:#00c6ff;
--text:#FFFFFF;
}

/* SIDEBAR */
.sidebar{
width:260px;
height:calc(100vh - 80px);
position:fixed;
top:80px; /* QUAN TRỌNG */
left:0;
background: linear-gradient(135deg, #0072ff);

z-index:900; /* thấp hơn header */
}

/* HEADER */
.sidebar-header{
padding:15px 20px;
font-weight:700;
font-size:12px;
letter-spacing:1px;
color:var(--accent);
border-bottom:1px solid rgba(255,255,255,0.08);
}

/* LIST */
.list_sidebar{
list-style:none;
padding:0;
margin:0;
}

.list_sidebar li a{
display:flex;
align-items:center;
padding:14px 20px;
color:var(--text);
text-decoration:none;
font-size:18px;
transition:0.25s;
border-left:3px solid transparent;
}

/* HOVER */
.list_sidebar li a:hover{
background:rgba(0,198,255,0.08);
color:#fff;
border-left:3px solid var(--accent);
padding-left:24px;
}

/* SUBMENU */
.submenu{
list-style:none;
padding:0;
margin:0;
background:rgba(255,255,255,0.03);
display:none;
}

.submenu li a{
padding:10px 20px 10px 40px;
font-size:13px;
}

/* ACTIVE mở submenu */
.menu-parent.open .submenu{
display:block;
}

/* ICON ARROW */
.toggle::after{
content:'▾';
margin-left:auto;
transition:0.3s;
}

.menu-parent.open .toggle::after{
transform:rotate(180deg);
}
.list_sidebar li a.active{
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    color: #ffffff;
    border-left: 3px solid #fdfdfd;
    padding-left: 24px;
}
</style>

<aside class="sidebar">

<div class="sidebar-header">
ADMIN PANEL
</div>

<?php $action = $_GET['action'] ?? ''; ?>

<ul class="list_sidebar">
<li>
    <a href="index.php" class="<?= $action==''?'active':'' ?>">Thống kê</a>
</li>

<li>
    <a href="index.php?action=lietke" class="<?= $action=='lietke'?'active':'' ?>">
        Quản lý chuyến bay
    </a>
</li>

<li>
    <a href="index.php?action=lietke_tuyenbay" class="<?= $action=='lietke_tuyenbay'?'active':'' ?>">
        Quản lý tuyến bay
    </a>
</li>

<li>
    <a href="index.php?action=lietke_maybay" class="<?= $action=='lietke_maybay'?'active':'' ?>">
        Danh sách máy bay
    </a>
</li>

<li>
    <a href="index.php?action=hienthive" class="<?= $action=='hienthive'?'active':'' ?>">
        Quản lý vé
    </a>
</li>


        <li>
            <a href="index.php?action=khachhang" class="<?= $action=='khachhang'?'active':'' ?>">
                Danh sách Hành khách
            </a>
        </li>

       
</li>
</ul>

</aside>

<script>
document.querySelectorAll('.menu-parent > a.toggle').forEach(item=>{
item.addEventListener('click',function(e){
e.preventDefault();
this.parentElement.classList.toggle('open');
});
});
</script>