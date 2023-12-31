<?php declare(strict_types=1);

error_reporting(E_ALL);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     PageTemplate.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

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
    // to do: declare reference variables for members
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks

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

    protected function generatePageHeader(string $title = "Baker", string $jsFile = "", bool $autoreload = false): void
    {
        parent::generatePageHeader($title, $jsFile, $autoreload); // TODO: Change the autogenerated stub


    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data.
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData():array
    {
        $data = array();
        // to do: fetch data for this view from the database
        // to do: return array containing data

        if ($result = $this->_database->query("SELECT * FROM ordered_article INNER JOIN article ON ordered_article.article_id = article.article_id WHERE ordered_article.status < 4")) {

            $i = 0;

            while($obj = $result->fetch_object()){
                $data[$i] = $obj;
                $i++;
            }
        }
        $result->free();
        return $data;
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
        $data = $this->getViewData();
        $this->generatePageHeader("Baker","",true); //to do: set optional parameters
        $url = $this->current_url;
        echo <<<END
            <h1>Pizzabäcker (bestellte Pizzen)</h1>
            <section class="driver">
              <form action="$url" method="POST"> 
        END;
        if($data) {
            foreach ($data as $num => $pizzas) {
                $key_num = $num+1;
                $name = "status[$pizzas->ordered_article_id]";
                $inputs = $this->printFieldSetInput($name, $pizzas->status);
                echo <<<END
             <fieldset>
                  <legend accesskey="$key_num"><b>$pizzas->name</b></legend>
                  $inputs
              </fieldset>
           END;

            }
            echo <<<END
                <form>
                <input type="submit"  name="submit" value="Status aktualisieren">
              </form>
            </section>
        END;
        } else{
            echo <<< HTML
                <p>Keine neuen Änderungen!</p>
            HTML;
        }

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
        if(isset($_POST['submit'])){
            $status = $_POST['status'];
            foreach ($status as $key => $value){
                $this->_database->query("UPDATE ordered_article SET status = $value WHERE ordered_article_id = $key");
            }
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

    private function printFieldSetInput($name, $status): string{
        $tag = 'name='.$name;
        $output = $status == 1 ? '<input type="radio" '.$tag.' value="1" checked> bestellt' : '<input type="radio" '.$tag.' value="1"> bestellt';
        $output .= $status == 2 ? '<input type="radio" '.$tag.' value="2" checked>im Ofen' : '<input type="radio" '.$tag.' value="2">im Ofen';
        $output .= $status == 3 ? '<input type="radio" '.$tag.' value="3" checked>fertig' : '<input type="radio" '.$tag.' value="3">fertig';
        return $output;
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