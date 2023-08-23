<?php echo<<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Bäcker - Übersicht</title>
</head>
<body>
  <h1>Bäcker</h1>
  <form action="https://echo.fbi.h-da.de/" id="form_id" method="post" accept-charset="UTF-8">
  <section>
      <h2>Bestellung 64 - Pizza Margherita</h2>

      <input type="radio" id="ordered_bestellung_64" name="status_bestellung_64" value="ordered" checked>
      <label for="ordered_bestellung_64">bestellt</label><br>
      <input type="radio" id="baking_bestellung_64" name="status_bestellung_64" value="baking">
      <label for="baking_bestellung_64">im Ofen</label><br>
      <input type="radio" id="done_bestellung_64" name="status_bestellung_64" value="done">
      <label for="done_bestellung_64">fertig</label>
  </section>
  <section>
      <h2>Bestellung 65 - Pizza Hawaii</h2>

      <input type="radio" id="ordered_bestellung_65" name="status_bestellung_65" value="ordered" checked>
      <label for="ordered_bestellung_64">bestellt</label><br>
      <input type="radio" id="baking_bestellung_65" name="status_bestellung_65" value="baking">
      <label for="baking_bestellung_65">im Ofen</label><br>
      <input type="radio" id="done_bestellung_65" name="status_bestellung_65" value="done">
      <label for="done_bestellung_65">fertig</label>
  </section>
  <input type="submit" id="bSubmit" value="aktualisieren">
  </form>
</body>
</html>
HTML;

