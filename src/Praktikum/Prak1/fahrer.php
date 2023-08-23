<?php echo<<<HTML
    <!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fahrer - Übersicht</title>
</head>
<body>
    <h1>Fahrer</h1>
    <form action="https://echo.fbi.h-da.de/" id="form_id" method="post" accept-charset="UTF-8">
    <section>
        <h2>Bestellung Nr. 74</h2>
        <p>Platz der Republik 64, 60385 Frankfurt am Main</p>

        <label for="status">Status: </label>
        <select name="status_Bestellung_74" id="status" tabindex="0">
            <option value="ready" selected>fertig</option>
            <option value="delivering">unterwegs</option>
            <option value="delivered">geliefert</option>
        </select>
    </section>
    <input type="submit" id="bSubmit" value="aktualisieren">
    </form>
</body>
</html>
HTML;

