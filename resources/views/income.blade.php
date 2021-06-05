<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Income</title>
</head>
<body>
    <div>
        <form method="POST" action="{{route('income.storef')}}">
            @csrf
            <div class="form-group">
              <label for="name">income name:</label>
              <input type="name" class="form-control" name="name">
            </div>
            <div class="form-group">
              <label for="type">type:</label>
              <input type="type" class="form-control" name="type">
            </div>
            <div class="form-group">
                <label for="value">value:</label>
                <input type="value" class="form-control" name="value">
              </div>
            
            <button type="store" class="btn btn-default">store</button>
          </form>
    </div>
</body>
</html>