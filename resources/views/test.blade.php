<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploader un fichier</title>
</head>
<body>
<form action="{{ route('test.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <label for="file">Choisir un fichier :</label>
    <input type="file" name="file" id="file">
    <button type="submit">Uploader</button>
</form>
</body>
</html>
