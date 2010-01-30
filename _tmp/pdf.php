<?php
/* 
 * SaveToPDF
 * Author : Hermawan Haryanto 
 * Nick : Teo Danardi, mas_edo 
 * Create date : 04/12/2001 
*/ 

    //set something usefull information for your pdf document.
    $author        ="Teo Danardi";  //author name (optional)
    $title        ="WaroengKemang.com"; //title of the pdf page (optional)
    $creator    ="Teo Danardi / Hermawan Haryanto"; //creator (optional)
    $subject    ="News Article"; //Subject (optional)
    //end set

    //start info taken from the database ( i like mysql)
    $headline="HEADLINE OF THE NEWS";
    $reporter="Reporter";
    $postdate="04/15/2001";
    $article.="This is the sample article line 1 n";
    $article.="This is the sample article line 2 n";
    $article.="This is the sample article line 3 n";
    $article.="This is the sample article line 4 n";
    $article.="This is the sample article line 5 n";
    $article.="This is the sample article line 6 n";
    $article.="This is the sample article line 7 n";
    $article.="This is the sample article line 8 n";
    //end database.

    //merging all info to one text
    $text.=$headline."n";
    $text.=$postdate."nn";
    $text.=$article."n";
    $text.=$reporter;
    //end merging

    //start creating pdf on the fly
    $pdf = PDF_open();
    pdf_set_info_author($pdf, $author);
    PDF_set_info_title($pdf, $title);
    pdf_set_info_creator($pdf, $creator);
    pdf_set_info_subject($pdf, $subject);
    PDF_begin_page($pdf, 450, 450);
    pdf_set_font($pdf, "Helvetica-Bold" , 12, winansi);
    pdf_set_text_rendering($pdf, 0);
    PDF_show_boxed($pdf, $text, 50, 100, 400, 300, "left");
    pdf_stroke($pdf);
    PDF_end_page($pdf);
    PDF_close($pdf);
    //end creating pdf
?> 