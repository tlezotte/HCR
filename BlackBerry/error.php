<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $language['label']['title1']; ?></title>
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 your company" />
<link href="handheld.css" rel="stylesheet" type="text/css" media="handheld">
</head>

<body>
<table width="240"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><div align="center"><a href="home.php"><img src="/Common/images/company200.gif" alt="your company" name="company" width="200" height="50" border="0"></a></div></td>
  </tr>
  <tr>
    <td align="center"><span class="DarkHeaderSubSub">
      <?= $default['title0']; ?>
      </span><br>
      <span class="DarkHeaderSub">
        <?= $language['label']['title1']; ?>
        's<br>
        Error Report</span></td>
  </tr>
  <tr>
    <td height="25">&nbsp;</td>
  </tr>
  <tr>
    <td class="ErrorNameText"><div align="center">
      <?= $_SESSION['error']; ?>
    </div></td>
  </tr>
  <tr>
    <td height="25">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><a href="javascript:history.go(-1)"><input name="back" type="button" value="Back" class="button"></a></div></td>
  </tr>
</table>
</body>
</html>
