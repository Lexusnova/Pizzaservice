<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class Ordersite for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     Ordersite.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.0
 */
require_once '../Prak5/page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
class Ordersite extends Page
{
    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data.
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData():array
    {
      $sql = "SELECT * FROM `article`";
      $recordset = $this->_database->query($sql);
      if (!$recordset) {
        throw new Exception("Charset failed: ".$this->_database->error);
      }
      $pizzalist = $recordset->fetch_all();
      $recordset->free();
      return $pizzalist;
    }
  

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */
    protected function generateView():void
    {
        $data = $this->getViewData(); // NOSONAR ignore unused $data
        $this->generatePageHeader('Bestellung');

        echo <<<HTML
<div class="container container-flex">
  <section class="speisekarte">
    <h2>Speisekarte</h2>
    <div class="pizzawahl">

HTML;
        foreach($data as $pizza) {
            $pizzaName = htmlspecialchars($pizza[1]);
            $pizzaImage = htmlspecialchars($pizza[2]);
            $price = htmlspecialchars(number_format((float)$pizza[3], 2, ",", ""));
            echo <<<HTML
    <div class="card" onclick="shoppingcart.addPizza({$pizza[0]}, '{$pizzaName}', {$pizza[3]});">
      <img src="./pizza-salami.jpeg" alt="pizza.png">
      <h3>{$pizzaName}</h3>
      <p>{$price}€</p>
    </div>

HTML;
        }
        echo <<<HTML
  </div>
</section>

<section class="warenkorb">
  <h2>Warenkorb</h2>
  <form action="./Ordersite.php" method="POST" onsubmit="return formularReady();">
    <div class="basket">
      <select name="pizza[]" id="pizzaselect" tabindex="1" multiple>
      </select>
      <p id="currentPrice"> Gesamtpreis: 0.00€</p>
    </div>
    <div class="options">
      <input type="text" name="address" id="address" value="" oninput="buttonEnableToggle();" placeholder="Hier Adresse eingeben.">
      <button type="button" tabindex="2" accesskey="a" onclick="shoppingcart.deleteAll();">Alle Löschen</button>
      <button type="button" tabindex="3" accesskey="s" onclick="shoppingcart.deleteSelected();">Auswahl Löschen</button>
      <button type="submit" tabindex="4" accesskey="b" id="submit" onclick="onSubmit();" title="Dieser Button wird benutzbar, sobald Sie eine Pizza ausgewählt und eine Adresse angegeben haben." disabled="true">Kostenpflichtig Bestellen</button>
  </div>
  </form>
</section>
</div>

HTML;


        $this->generatePageFooter();
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     * @return void
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if (count($_POST)) {
            if (isset($_POST["pizza"]) && isset($_POST["address"])) {
                $pizzalist = $_POST["pizza"];
                $address = $this->_database->real_escape_string($_POST["address"]);

                // Create order and get orderid for adding pizza's
                $sql = "INSERT INTO `ordering` (`address`) VALUES('$address')";
                $this->_database->query($sql);
                $sql = "SELECT `ordering_id` FROM `ordering` WHERE `address`='$address' ORDER BY `ordering_id` DESC LIMIT 1";
                $result = $this->_database->query($sql);
                if (!$result) {
                    throw new Exception("Charset failed: ".$this->_database->error);
                }
                $orderid = $result->fetch_assoc()['ordering_id'];

                // Add ordered pizza's to order
                foreach($pizzalist as $pizzaid) {
                    $pizzaid = $this->_database->real_escape_string($pizzaid);
                    $sql = "INSERT INTO `ordered_article` 
                  (`ordered_article_id`, `ordering_id`, `article_id`, `status`) 
                  VALUES (NULL, '$orderid', '$pizzaid', '1')";
                    $result = $this->_database->query($sql);
                    if (!$result) {
                        throw new Exception("Insert pizza failed: ".$this->_database->error);
                    }
                }

                session_start();
                $_SESSION["orderid"] = $orderid;
            }

            header("HTTP/1.1 303 See Other");
            header("Location: ./Customer.php");
            die();
        }
    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     * @return void
     */
    public static function main():void
    {
        try {
            $page = new Ordersite();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page.
// That is input is processed and output is created.
Ordersite::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >
