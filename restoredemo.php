<?
/*
 * Restore MySQL dump using PHP
 * (c) 2006 Daniel15
 * Last Update: 9th December 2006
 * Version: 0.2
 * Edited: Cleaned up the code a bit. 
 *
 * Please feel free to use any part of this, but please give me some credit :-)
 */
  
// Name of the file
$filename = 'omo_demo.sql';
// MySQL host
$mysql_host = 'localhost';
// MySQL username
$mysql_username = 'root';
// MySQL password
$mysql_password = 'gh78bg56';
// Database name
$mysql_database = 'omo_demo';
 
//////////////////////////////////////////////////////////////////////////////////////////////
 
// Connect to MySQL server
mysql_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysql_error());
// Select database
mysql_select_db($mysql_database) or die('Error selecting MySQL database: ' . mysql_error());
 
// Temporary variable, used to store current query
$templine = '';
// Read in entire file
$lines = file($filename);
// Loop through each line
foreach ($lines as $line)
{
    // Skip it if it's a comment
    if (substr($line, 0, 2) == '--' || $line == '')
        continue;
 
    // Add this line to the current segment
    $templine .= $line;
    // If it has a semicolon at the end, it's the end of the query
    if (substr(trim($line), -1, 1) == ';')
    {
        // Perform the query
        mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
        // Reset temp variable to empty
        $templine = '';
    }
}
 
?>
