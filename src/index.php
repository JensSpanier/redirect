<?php
$env_redirect_home = getenv('REDIRECT_HOME');
define('HOME', $env_redirect_home === false ? '127.0.0.1' : $env_redirect_home);
require_once __DIR__ . '/redirect.php';

$original_host = explode(':', $_SERVER['HTTP_HOST'])[0];
if ($original_host !== HOME) {
    $txt_records = dns_get_record("_redirect.$original_host", DNS_TXT);
    new Redirect($txt_records);
}
?>
<!doctype html>
<html lang="de">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <title>Weiterleitung</title>
</head>

<body>
    <div class="container">
        <h1>Weiterleitung</h1>
        <p>
            Lege einen CNAME-Eintrag für die Domain an, die du weiterleiten möchtest. Dieser muss auf <code>redirect.spnr.de</code> zeigen.
            <br>
            Beispiel:
            <br>
            <code>www.example.com IN CNAME redirect.spnr.de</code>
            <br>
            Oder verwende die IP-Adresse <code>82.165.96.230</code>, falls du keinen CNAME anlegen kannst.
            <br>
            Beispiel:
            <br>
            <code>example.com IN A 82.165.96.230</code>
            <br>
            IPv6 wird auch unterstützt:
            <br>
            <code>example.com IN AAAA 2001:8d8:1801:8546::1</code>
        </p>
        <hr>
        <p>
            Lege als nächstes einen TXT-Eintrag an, der anzeigt wohin weitergeleitet werden soll. Das Format ist
            <br>
            <code>regex destination [statuscode [priority]]</code>
            <br>
            Beispiel:
            <br>
            <code>_redirect.www.example.com IN TXT ".* http://example.com"</code>
            <br>
            Wenn die URI angehangen werden soll, ergänze den Eintrag noch um <code>{0}</code>.
            <br>
            Beispiel:
            <br>
            <code>_redirect.www.example.com IN TXT ".* http://example.com{0}"</code>
            <br>
            Standardmäßig wird der HTTP-Statuscode <code>301</code> verwendet. Du kannst ihn im TXT-Eintrag anpassen.
            <br>
            Beispiel:
            <br>
            <code>_redirect.www.example.com IN TXT ".* http://example.com{0} 302"</code>
            <br>
            Die Reihenfolge kann durch die Priority geändert werden. Größere Priority = Höhere Priorität
            <br>
            Beispiel:
            <br>
            <code>_redirect.www.example.com IN TXT ".* http://example.com{0} 302 10"</code>
        </p>
        <hr>
        <p>
            Hinweise:
            <br>
            <code>/</code> müssen (und dürfen) im regulären Ausdruck nicht escaped werden.
        </p>
    </div>

</body>

</html>