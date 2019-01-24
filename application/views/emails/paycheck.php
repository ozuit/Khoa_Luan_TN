<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
	<p>Xin chào <strong><?php echo $hoten ?></strong>,</p>
	<p>Đây là thông tin chi tiết lương tháng <?php echo $month ?>/<?php echo $year ?> của bạn:</p>
	<p>Họ và tên: <?php echo $hoten ?> - Mã số: <?php echo $manv ?></p>
	<p>Trừ tiền đi trễ: <?php echo $ditre ?> đồng</p>
	<p>Tiền tăng ca: <?php echo $tangca ?> đồng</p>
	<p>BHXH: <?php echo $bhxh_nv ?> đồng - BHYT <?php echo $bhyt_nv ?> đồng - BHTN: <?php echo $bhtn_nv ?> đồng</p>
	<p>Phí công đoàn: <?php echo $congdoan_nv ?> đồng</p>
	<p>Tổng lương: <?php echo $tongluong ?> đồng</p>
	<p>Phụ cấp: <?php echo $phucap ?> đồng</p>
	<p>Thuế thu nhập cá nhân: <?php echo $thuetncn ?> đồng</p>
	<p>Lương thực lãnh: <?php echo $thuclanh ?> đồng</p>
</body>
</html>