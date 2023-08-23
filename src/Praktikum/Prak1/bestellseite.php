<!DOCTYPE html>
<html lang="de">  
<head>
    <meta charset="UTF-8" />
    <!-- für später: CSS include -->
    <!-- <link rel="stylesheet" href="XXX.css"/> -->
    <!-- für später: JavaScript include -->
    <!-- <script src="XXX.js"></script> -->
    <title>Pizzaservice</title>
</head>
<body>
    <h1> Bestellung</h1>
    <section>
    <h2> Speisekarte </h2>
    <img src="images/pizza.png" width = "150" alt="">
    <p>Margherita <br> 4,00€ </p>
    <img src="images/pizza.png" width = "150" alt="">
    <p>Salami <br> 4,50€ </p>   
    <img src="images/pizza.png" width = "150" alt="">
    <p>Hawaii <br> 5,50€ </p>
</section>
<section> <!-- -->
    <form action="https://echo.fbi.h-da.de/" id="form_id" method="post" accept-charset="UTF-8">
    <h3> Warenkorb</h3>
    <select multiple name="Pizza[]" tabindex="0" size="8">
        <option value="Salami" selected>Salami</option>
        <option value="Hawaii">Hawaii</option>
        <option value="Salami">Salami</option>
    </select>
    <p> 14,50€ </p>
  <input name="adress" type="text" id="Adresse" placeholder="Adresse" value="" required>
  <p></p>
  <input type="button" id="bDeleteall" value="Alle löschen">
  <input type="button" id="bDeleteselected" value="Auswahl löschen">
  <input type="submit" id="bSubmit" value="Bestellen">
</form>
</section>
</body>
</html>