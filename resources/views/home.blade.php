<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QuickPoll</title>
</head>
<body>
<h1>Sveiki atvykę į QuickPoll</h1>
<p>Kurkite apklausas ir balsuokite greitai.</p>
<a href="{{ route('polls.create') }}">Nauja apklausa</a>
<h2>Apklausos</h2>
<ul>
    @forelse ($polls as $poll)
        <li><a href="{{ route('polls.show', $poll) }}">{{ $poll->title }}</a></li>
    @empty
        <li>Apklausų dar nėra. Sukurkite pirmąją apklausą.</li>
    @endforelse
</ul>
</body>
</html>
