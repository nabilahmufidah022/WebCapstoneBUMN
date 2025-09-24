<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    @if(Auth::user()->usertype == 'user')
        <h1>
            Hello user
        </h1> 
    @elseif(Auth::user()->usertype == 'admin')
        <h1>
            Hello Admin
        </h1> 
    @endif
</body>
</html>
