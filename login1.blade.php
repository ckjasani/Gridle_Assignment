<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<form action="login" method="post">
{{csrf_field()}}
<input type="text" name="email">
<input type="password" name="pwd">
<input type="submit">
</form>
</body>
</html>
