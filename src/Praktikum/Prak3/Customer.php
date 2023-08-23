<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class Customer for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     Customer.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.0
 */
require_once '../Prak3/page.php';

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
class Customer extends Page
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
        $orderingId = $_SESSION["orderid"];
        $sql = "SELECT `article`.`name`, `ordered_article`.`status` 
            FROM `ordered_article`
            LEFT JOIN `article`
            ON `article`.`article_id` = `ordered_article`.`article_id`
            WHERE `ordered_article`.`ordering_id` = '{$orderingId}'";
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
        $this->generatePageHeader('Customer');

        echo <<<HTML
<div class='container'>

HTML;
        foreach($data as $pizza) {
            $pizzaName = htmlspecialchars($pizza[0]);
            echo <<<HTML
  <p>{$pizzaName}: {$this->processStatus($pizza[1])}</p>

HTML;
        }

        echo <<<HTML
  <button tabindex="1" accesskey="b">
    <a href="./Ordersite.php">Neue Bestellung (b)</a>
  </button>
</div>

HTML;
        $this->generatePageFooter();
    }

    /**
     * Process Status
     * @return String
     */
    protected function processStatus($status): String
    {
        if($status == 1) return "bestellt";
        if($status == 2) return "im Ofen";
        if($status == 3) return "fertig";
        if($status == 4) return "unterwegs";
        if($status == 5) return "geliefert";
        return "default";
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
        session_start();
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
            $page = new Customer();
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
Customer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >
