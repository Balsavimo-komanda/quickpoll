<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prisijungti prie balsavimo</title>
</head>
<body style="font-family: sans-serif; max-width: 400px; margin: 40px auto; padding: 0 16px;">
    <h1>Prisijungti prie balsavimo</h1>

    @if (session('error'))
        <p style="color:#b91c1c">{{ session('error') }}</p>
    @endif
    @error('code')
        <p style="color:#b91c1c">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('polls.join') }}">
        @csrf
        <input type="text" name="code" value="{{ old('code') }}"
               placeholder="Įvesk kodą, pvz. K7M2QX"
               maxlength="16" autofocus required style="width:100%; padding:8px; text-transform:uppercase;">
        <button type="submit" style="margin-top:12px; padding:8px 16px;">Prisijungti</button>
    </form>
        </form>

    <p style="margin-top:20px;">
        <a href="{{ url('/') }}">← Grįžti į pradžią</a>
    </p>
</body>
</body>
</html>