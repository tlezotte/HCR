<?php
/**
 * - Config Information
 */
require_once('../include/config.php'); 

    	    
getProxyDATA("http://intranet.yourcompany.com/?q=taxonomy/term/28/0/feed", "intranet.xml");
getProxyDATA("http://www.shrm.org/shrmnewsfeeds/createmyfeedxml.asp?nchannel=m&maxarticles=5&xchannel=headlinessummary", "shrm.xml");
getProxyDATA("http://weather.yahooapis.com/forecastrss?p=48083", "weather.xml"); 
getProxyDATA("http://pipes.yahoo.com/pipes/pipe.run?_id=onKnkYIk3BG26l7HJZhxuA&_render=rss&currencyCode=USD&filter=USD%7CEUR%7CGBP%7CCAD%7CJPY%7CCNY%7CCZK%7C", "currency.xml");
getProxyDATA("http://pipes.yahoo.com/pipes/pipe.run?_id=bkbxiLp53BGS3HU06kjTQA&_render=json","market.json");
?>
