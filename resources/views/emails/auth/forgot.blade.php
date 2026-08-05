<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
        <title>

        </title>
    </head>

    <body>
        Create a new password: /api/auth/pwd/{{ $token }}?lproc={{ $lproc }}<br>
        Expires at {{ $token_expires_at->format('Y-m-d H:i') }}
    </body>
</html>
