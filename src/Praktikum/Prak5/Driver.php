<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class Driver for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     Driver.php
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
class Driver extends Page
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
        $sql="SELECT `ordering`.`ordering_id`, `ordering`.`address`, `article`.`name`, `ordered_article`.`status`
          FROM `ordering`
          JOIN `ordered_article`
          ON `ordering`.`ordering_id` = `ordered_article`.`ordering_id`
          JOIN `article`
          ON `article`.`article_id` = `ordered_article`.`article_id`
          ";
        $recordset = $this->_database->query($sql);
        if (!$recordset) {
            throw new Exception("Charset failed: ".$this->_database->error);
        }
        $orderlist = $recordset->fetch_all();
        $recordset->free();
        usort($orderlist, function($a, $b) {
            return $a[0] <=> $b[0];
        });
        $orderlist = $this->isDeliverable($orderlist);
        $orderlist = $this->combineOrderList($orderlist);
        return $orderlist;
    }

    /**
     * Find only the relevant orders that have to be delivered.
     * Orders still in preparation aren't ready to deliver yet.
     * @return Array
     */
    private function isDeliverable($orderlist): Array {
        $skiplist = array();
        foreach($orderlist as $item) {
            if ($item[3] < 3 or $item[3] > 4) {
                if (!in_array($item[0], $skiplist)) {
                    array_push($skiplist, $item[0]);
                }
            }
        }
        $orderlist = array_filter($orderlist, function($var) use(&$skiplist) {
            return !in_array($var[0], $skiplist);
        });
        return $orderlist;
    }

    /**
     * Combine orderlists to have easier accessable functions.
     * Same ID gets merged with an array for pizzanames.
     */
    private function combineOrderlist($orderlist): Array {
        $orderlistNew = array();
        $orderItem = array();
        foreach($orderlist as $order) {
            if (!empty($orderItem)) {
                if ($orderItem[0] == $order[0]) {
                    array_push($orderItem[2], $order[2]);
                    continue;
                }
                array_push($orderlistNew, $orderItem);
            }
            $orderItem = array($order[0], $order[1], array($order[2]), $order[3]);
        }
        if (!empty($orderItem)) {
            array_push($orderlistNew, $orderItem);
        }
        return $orderlistNew;
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
        $this->generatePageHeader('Driver',"",true);

        echo <<<HTML
<link rel="stylesheet" href="./css/driverStyle.css">
<div class="container">

HTML;
        foreach($data as $order) {
            $orderid = htmlspecialchars($order[0]);
            $orderAddress = htmlspecialchars($order[1]);
            $orderPrice = @$order[4];
            $orderPizzalist = htmlspecialchars($this->splitPizzaList($order[2]));
echo <<<HTML
  <div class="card card-flex">
    <form action="./Driver.php" method="POST" id="form{$order[0]}">
      <p class="paddress">{$orderAddress}</p>
      <p class="pprice">{$orderPrice}</p>
      <p class="ppizza">{$orderPizzalist}</p>
      <div class="container-flex">
        <div class="input-wrapper">
          <label for="form{$orderid}fertig">fertig</label>
          <input type="radio" name="lieferstatus" id="form{$orderid}fertig" value="3" onclick="document.forms['form{$orderid}'].submit();"{$this->isChecked($order[3], 3)}>
        </div>
        <div class="input-wrapper">
          <label for="form{$orderid}unterwegs">unterwegs</label>
          <input type="radio" name="lieferstatus" id="form{$orderid}unterwegs" value="4" onclick="document.forms['form{$orderid}'].submit();"{$this->isChecked($order[3], 4)}>
        </div>
        <div class="input-wrapper">
          <label for="form{$orderid}geliefert">geliefert</label>
          <input type="radio" name="lieferstatus" id="form{$orderid}geliefert" value="5" onclick="document.forms['form{$orderid}'].submit();"{$this->isChecked($order[3], 5)}>
        </div>
      </div>
      <input type="hidden" name="action" value="{$orderid}" tabindex="1" accesskey="s"></input>
    </form>
  </div>


HTML;
        }
        echo <<<HTML
</div>
HTML;
        $this->generatePageFooter();
    }

    private function splitPizzaList($pizzalist): String {
        $pizzalist = implode(', ', $pizzalist);
        return $pizzalist;
    }

    private function isChecked($status, $expected): String {
        if($status == $expected) return " checked";
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
            if (isset($_POST['action']) && isset($_POST['lieferstatus'])) {
                $orderid = $this->_database->real_escape_string($_POST['action']);
                $status = $this->_database->real_escape_string($_POST['lieferstatus']);

                $sql = "UPDATE `ordered_article` SET `status` = '$status' WHERE `ordering_id` ='$orderid'";
                $this->_database->query($sql);
            }
            header("HTTP/1.1 303 See Other");
            header("Location: ./Driver.php");
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
            $page = new Driver();
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
Driver::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >
