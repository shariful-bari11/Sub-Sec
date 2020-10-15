<?php

class DocxToTextConversion
{
    private $document;

    public function __construct($DocxFilePath)
    {
        $this->document = $DocxFilePath;
    }

    public function convertToText()
    {
        if (isset($this->document) && !file_exists($this->document)) {
            return 'File Does Not exists';
        }

        $fileInformation = pathinfo($this->document);
        $extension = $fileInformation['extension'];
        if ($extension == 'doc' || $extension == 'docx') {
            if ($extension == 'doc') {
                return $this->extract_doc();
            } elseif ($extension == 'docx') {
                return $this->extract_docx();
            }
        } else {
            return 'Invalid File Type, please use doc or docx word document file.';
        }
    }

    private function extract_doc()
    {
        $fileHandle = fopen($this->document, 'r');
        $allLines = @fread($fileHandle, filesize($this->document));
        $lines = explode(chr(0x0D), $allLines);
        $document_content = '';
        foreach ($lines as $line) {
            $pos = strpos($line, chr(0x00));
            if (($pos !== false) || (strlen($line) == 0)) {
            } else {
                $document_content .= $line . ' ';
            }
        }
        $document_content = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", '', $document_content);
        return $document_content;
    }

    private function extract_docx()
    {
        $document_content = '';
        $content = '';

        $zip = zip_open($this->document);

        if (!$zip || is_numeric($zip)) {
            return false;
        }

        while ($zip_entry = zip_read($zip)) {
            if (zip_entry_open($zip, $zip_entry) == false) {
                continue;
            }

            if (zip_entry_name($zip_entry) != 'word/document.xml') {
                continue;
            }

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', ' ', $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $document_content = strip_tags($content);

        return $document_content;
    }
}



//include "DocxToTextConversion.php";

$converter = new DocxToTextConversion('TASK_SAMPLE_SHARIFUL_BARI.docx');

 $output= $converter->convertToText();



function getSubject($str) {
     $sub_start = stripos($str, 'বিষয়:');
     $sub_end = stripos($str, '।', $sub_start);
    //echo $sub_start;
     //echo $sub_end;
 
     return substr($str, $sub_start, $sub_end - $sub_start);

 }


 echo getSubject($output);
 echo ('<br>');
 echo  substr_count($output,"অনুচ্ছেদ:");
 echo ('<br>');

 
// echo $output ;


//  $matches = [];

// $res = preg_match('/অনুচ্ছেদ:.*অনুচ্ছেদ:/u', $output, $matches);

// print_r($matches);


function strpos_all($haystack, $needle) {
    $offset = 0;
    $allpos = array();
    while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
        $offset   = $pos + 1;
        $allpos[] = $pos;
    }
    return $allpos;
}
$all_pos=strpos_all($output, "অনুচ্ছেদ:");



// echo(strlen(" ৮.০"));


function getSection($allPos,$output) {
    $sections = array();
    foreach($allPos as $pos) {
        $sections[] = substr($output, $pos+25, 8);
    }
    return $sections;
}

print_r (getSection($all_pos,$output));




 