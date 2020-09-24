<!DOCTYPE html>

<html lang="en">

<body>

	<table class="container" style="width: 60%;margin-left:auto;margin-right:auto;height: 100px;position: relative;background: #5d3ee3;position: absolute;top: 50%;left: 50%;margin-right: -50%;transform: translate(-50%, -80%);">
		<tr class="head">
			<td class="pull-left" style="float: left;padding: 20px;font-size: 16px; color: #fff;font-weight: 600;">
				ImpoExpo
			</td>
			<td class="pull-right" style="float: right;color: #fff;padding: 20px;font-size: 14px;font-weight: 600;">
				Reset Password
			</td>
		</tr>
	</table> 
	<table class="content" cellspacing="20" cellpadding="0" style="margin-top:-45px;margin-left:auto;margin-right:auto;background: #fff;height: auto; width:54%;border: 1px solid #dedede;transform: translate(-50%, -80%) ;padding:20px;text-align: center;">
		<tr>
			<td class="img-container"><img width="100px" height="100px" src="https://i.pinimg.com/564x/8f/07/a5/8f07a54031fecf0c099b762b4420b673.jpg" /></td>
		</tr>
		<tr>
			<td style="margin: 40px;">Welcome to ImpoExpo <?php echo "$user->name"; ?></td>&nbsp;
		</tr>
		<tr>
			<td><p>Kindly click the button below to reset your password.</p></td>
		</tr>
		<tr>
			<td>
				<button style="background:#5d3ee3;border: none;height:50px;width:150px;">
					<a style="color:#fff; font-weight: 600; text-decoration: none;" href="http://134.209.248.217/#/verify/password/<?php echo $user->userID;?> ">RESET PASSWORD</a>
				</button>
			</td>
		</tr>
		<tr>
			<td>
				Questions
			</td>
		</tr>
		<tr>
			<td>
				Contact Us on <a href="mailto:hello@impoexpo.co.tz">hello@impoexpo.co.tz</a>
			</td>
		</tr>
		<tr>
			<td>
				&copy; Copyright <?php echo date("Y"); ?> ImpoExpo. All rights reserved
			</td>
		</tr>
	</table>

</body>

</html> 

