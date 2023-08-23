<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class Baker for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     Baker.php
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
class Baker extends Page
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
        $sql = "SELECT `ordered_article`.`ordered_article_id`, `article`.`name`, `ordered_article`.`status`
    FROM `ordered_article` 
    LEFT JOIN `article` ON `ordered_article`.`article_id`=`article`.`article_id`
    WHERE `ordered_article`.`status` < 4";
        $recordset = $this->_database->query($sql);
        if(!$recordset) {
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
        $this->generatePageHeader('Baker',"",true);

        echo <<<HTML
<link rel="stylesheet" href="./css/bakerStyle.css">
<div class="container">
  <div class="pizzaHeader">
    <p class="pizzacolumn"></p>
    <p>bestellt</p>
    <p>im Ofen</p>
    <p>fertig</p>
  </div>
  <div>

HTML;
        foreach($data as $pizza) {
            $pizzaId = htmlspecialchars($pizza[0]);
            $pizzaName = htmlspecialchars($pizza[1]);
            $pizzaStatus = $pizza[2];
            echo <<<HTML
    <form action="./Baker.php" method="POST" id="form{$pizzaId}">
      <p class="pizzacolumn">{$pizzaId} - {$pizzaName}</p>
      <input type="radio" name="status" id="form{$pizzaId}bestellt" value="1" onclick="document.forms['form{$pizzaId}'].submit();"{$this->statusChecked($pizzaStatus,1)}>
      <input type="radio" name="status" id="form{$pizzaId}imOfen" value="2" onclick="document.forms['form{$pizzaId}'].submit();"{$this->statusChecked($pizzaStatus,2)}>
      <input type="radio" name="status" id="form{$pizzaId}fertig" value="3" onclick="document.forms['form{$pizzaId}'].submit();"{$this->statusChecked($pizzaStatus,3)}>
      <input type="hidden" name="action" value="{$pizzaId}" tabindex="1" accesskey="s"></input>
    </form>

HTML;

        }
        echo <<<HTML
  </div>
</div>

HTML;
        $this->generatePageFooter();
    }

    private function statusChecked($status, $expectedStatus): String
    {
        if($status == $expectedStatus) return " checked";
        return "";
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
        if(count($_POST)) {
            if(isset($_POST['action']) && isset($_POST['status'])) {
                $pizzaId = $this->_database->real_escape_string($_POST['action']);
                $status = $this->_database->real_escape_string($_POST['status']);

                $sql = "UPDATE `ordered_article`
        SET `status` = '$status'
        WHERE `ordered_article_id` = '$pizzaId'";
                $this->_database->query($sql);
            }

            header("HTTP/1.1 303 See Other");
            header("Location: ./Baker.php");
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
            $page = new Baker();
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
Baker::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >
