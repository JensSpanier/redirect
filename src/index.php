<?php
$env_redirect_home = getenv('REDIRECT_HOME');
define('HOME', $env_redirect_home === false ? 'localhost' : $env_redirect_home);

$original_host = explode(':', $_SERVER['HTTP_HOST'])[0];
if ($original_host !== HOME) {
    $txt_records = dns_get_record("_redirect.$original_host", DNS_TXT);
    require_once __DIR__ . '/redirect.php';
    new Redirect($txt_records);
}

function _(string $text): void
{
    echo htmlspecialchars($text);
}

$ipv4 = [];
$ipv6 = [];
foreach (dns_get_record(HOME, DNS_A | DNS_AAAA) as $record) {
    switch ($record['type']) {
        case 'A':
            $ipv4[] = $record['ip'];
            break;
        case 'AAAA':
            $ipv6[] = $record['ipv6'];
            break;
    }
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
            Lege einen CNAME-Eintrag für die Domain an, die du weiterleiten möchtest. Dieser muss auf <code><?php _(HOME); ?></code> zeigen.
            <br>
            Beispiel:
            <br>
            <code>www.example.com IN CNAME <?php _(HOME); ?>.</code>
            <?php if ($ipv4 || $ipv6) : ?>
                <br>
                Oder verwende die IP-Adressen, falls du keinen CNAME anlegen kannst:
                <?php foreach ($ipv4 as $ip) : ?>
                    <br>
                    <code>example.com IN A <?php _($ip); ?></code>
                <?php endforeach; ?>
                <?php foreach ($ipv6 as $ip) : ?>
                    <br>
                    <code>example.com IN AAAA <?php _($ip); ?></code>
                <?php endforeach; ?>
            <?php endif; ?>
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