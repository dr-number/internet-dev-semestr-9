<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test server</title>
</head>
<body>
    SERVER_NAME: <b><?php echo $_SERVER['SERVER_NAME'];?></b>
    </br>
    gethostbyname: <b><?php echo gethostbyname($_SERVER['SERVER_NAME']);?></b>
</body>
</html>