<?php
    $doc = new DomDocument();
    $file ="c:\Users\Phillip\Documents\GitHub\BBBEE_project\Project One\classes\crontest.html";

    if ($doc->loadHTMLFile($file)){
        $span = $doc->getElementByTagName('span')->item(0);
        $count =$span->textContent;
        $count++;

        $doc->getElementByTagName('span')->item(0)->nodeValue =$count;
        $doc->saveHTMLFile($file);

        echo 'file updated sucessfully';


    }else{
        return false;
    }
