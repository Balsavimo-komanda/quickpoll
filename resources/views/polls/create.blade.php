<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Nauja apklausa</title>
</head>
<body>
<h1>Nauja apklausa</h1>

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('polls.store') }}">
    @csrf

    <label>Klausimas</label>
    <input type="text" name="title" value="{{ old('title') }}" required>

    <div id="options">
        <input type="text" name="options[]" placeholder="Variantas 1" required>
        <input type="text" name="options[]" placeholder="Variantas 2" required>
    </div>

    <button type="button" onclick="addOption()">+ Pridėti variantą</button>
    <button type="submit">Sukurti</button>
</form>

<script>
    function addOption() {
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'options[]';
        input.placeholder = 'Variantas';
        document.getElementById('options').appendChild(input);
    }
</script>
</body>
</html>
