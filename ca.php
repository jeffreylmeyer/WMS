<?php
// login check
ob_start();
$user = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER']:"";


function loginForm($msgt)
{
?>
<h3><?=$msgt;?></h3>
<div style="margin:auto;width:400px">
	<form method ="post" action="<?=$_SERVER['PHP_SELF'];?>">
		Username: <input type="text" name="user" size="20" maxlength="30" /><br /><br />
		Password: <input type="password" name="pwd" size="20" maxlength="30" /><br /><br />
		<input type="submit" value="Login" />
	
	</form>
</div>
<?

}

?>