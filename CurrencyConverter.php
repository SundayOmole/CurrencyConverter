<?php

/*
 * Title: CurrencyConverter.php
 * Author: Festus Sunday OMOLE
 * Copyright: 2012 Festus Sunday OMOLE
 * Date: 07/08/12
 * 
 
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details:
 * http://www.gnu.org/licenses/gpl.html
 *
 */


class CurrencyConverter{
     // the following variables would be supplied at runtime
       var $mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_table = "";
    
    
    // constructor method
    function CurrencyConverter($host, $user, $pass, $db, $table){
        $this->mysql_host  = $host;
        $this->mysql_user  = $user;
        $this->mysql_pass  = $pass;
        $this->mysql_db    = $db;
        $this->mysql_table = $table;
               
        $this->exchangeRatesTable();        
        $this->downloadAPI();
       
    } // end of constructor method
    
    function convert($input){
    
        $temp = explode(' ', $input);
        $from     = $temp[0];
        $amount   = $temp[1];
        $decimals = 2;
        
        $con = mysql_connect($this->mysql_host, $this->mysql_user, $this->mysql_pass);
        if (!$con) {die('Could not connect: ' . mysql_error());}
        
        
        mysql_select_db($this->mysql_db, $con);
        $sql = "SELECT * FROM " . $this->mysql_table . " WHERE currencies='" . $from . "'";
        $result = mysql_query($sql, $con) or die(mysql_error());
        $row = mysql_fetch_assoc($result);
        $exc_rate = $row[rates];
        
        return ((number_format(($amount * $exc_rate), $decimals)));
        
    }
    
    /*downloadAPI function downloads an XML file which is then parsed and inserted into the database.
     This function updates exixting table or inserts news ones.
    */
    function downloadAPI() {
        $xml = simplexml_load_file('http://toolserver.org/~kaldari/rates.xml');
        
        foreach ($xml->children() as $child) {
            $currency = $child->currency;
            $rate     = $child->rate;
            
            $con = mysql_connect($this->mysql_host, $this->mysql_user, $this->mysql_pass);
            if (!$con) {die('Could not connect: ' . mysql_error());}
            
            mysql_select_db($this->mysql_db, $con);
            
            $sql = "SELECT * FROM " . $this->mysql_table . " WHERE currencies='" . $currency . "'";
            $result = mysql_query($sql, $con) or die(mysql_error());
            
            if (mysql_num_rows($result) > 0) {
            $sql = "UPDATE " . $this->mysql_table . " SET rates=" . $rate . " WHERE currencies='$currency'";}
            else{
            $sql = "INSERT INTO " . $this->mysql_table . "(currencies, rates)  VALUES('$currency','$rate')";
            }
            $result = mysql_query($sql, $con) or die(mysql_error());
            
        }
    }
    
    /* Create the currency exchangeRatesTable. This function create a 
        table if not already exits.*/
    function exchangeRatesTable()
    {
        $con = mysql_connect($this->mysql_host, $this->mysql_user, $this->mysql_pass);
        if (!$con) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db($this->mysql_db, $con);
        
 
   $sql = "\n"
        . "CREATE TABLE IF NOT EXISTS \n"
        . $this -> mysql_table
        . "(\n"
        . " `currencies` varchar(3) NOT NULL,\n"
        . " `rates` double NOT NULL,\n"
        . " `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
        . "  PRIMARY KEY (`currencies`)\n"
        . ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ";

        $result = mysql_query($sql, $con)or die(mysql_error());

    }
}



//The following scripts will help test the application.
$x = new CurrencyConverter('yourHost', 'yourUserName', 'yourPassword', 'yourDB', 'yourTableName');
$s = USD;
$z = 'aud 5000';
echo strtoupper($z) . " = " . $s . $x->convert($z);

echo "<hr />";

$y = array(
    'jpy 10000',
    'aud 300',
    'ars 5000'
);
foreach ($y as $newy) {
    echo strtoupper($newy) . " = " . $s . $x->convert($newy) . "<br />";
}
?>