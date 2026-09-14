<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
        <title>

        </title>
    </head>

    <body>
        Your logins:
        @foreach ($logins as $login)
            {{ $login }}
        @endforeach
    </body>
</html>
