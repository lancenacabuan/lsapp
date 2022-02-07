<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br/>Your new account password is:<br /></p>
    <strong>{{$details['password']}}</strong><br />
    <p>Kindly login via link below and change your password...<br />Thank you!<br /></p>
    <a href="https://lance.idsi.com.ph/login">https://lance.idsi.com.ph/login</a><br/>
    <img src="{{ asset('/storage/changepassword.jpg') }}">
</body>
</html>