<?
header("Content-Type: text/html; charset=utf-8"); 
function utf8_to_unicode_code($utf8_string)
{
	$expanded = iconv("UTF-8", "UTF-32", $utf8_string);
    return unpack("L*", $expanded);
}
function unichr($u) {
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}
function compress($str, $xmlsafe = false) {
	$dico = array();
	$skipnum = $xmlsafe?5:0;
	for ($i=0; $i<256;$i++) {
		$dico[chr($i)] = $i;
	}
	if ($xmlsafe) {
		$dico["<"] = 256;
		$dico[">"] = 257;
		$dico["&"] = 258;
		$dico["\""] = 259;
		$dico["'"] = 260;
	}
	$res = "";
	$splitStr = str_split($str);
	//print_r($splitStr);
	$length = count($splitStr);
	$nbChar = 256+$skipnum;
	$buffer = "";	
	for ($i=0; $i<=$length;$i++) {
		$current = $splitStr[$i];
		if ($dico[$buffer . $current] !== NULL && $current != "")
		{
			//echo $buffer . ":" . $current . "<br>";
			$buffer .= $current;
		}
		else
		{
			$res .= unichr($dico[$buffer]);
			//echo $dico[$buffer] . ": " . unichr($dico[$buffer]) . "<br>";
			$dico[$buffer . $current] = $nbChar;
			$nbChar++;
			$buffer = $current;
		}
	}
	return $res;
}

function decompress($str, $xmlsafe = false) {
	$xmlsafe = false;
	$skipnum = $xmlsafe?5:0;
	for ($i=0; $i<256;$i++) {
		$dico[$i] = chr($i);
	}
	if ($xmlsafe) {
		$dico["<"] = 256;
		$dico[">"] = 257;
		$dico["&"] = 258;
		$dico["\""] = 259;
		$dico["'"] = 260;
	}
	$splitStr = utf8_to_unicode_code($str);
	$length = count($splitStr);
	$nbChar = 256+$skipnum;
	$buffer = "";
	$chaine = "";
	$result = "";
	for ($i=0; $i <= $length; $i++)
	{
		$code = $splitStr[$i];
		//echo unichr($code) . ": " . $code . "<br>";
		$current = $dico[$code];
		if ($buffer == "")
		{
			$buffer = $current;
			$result .= $current;
		}
		else
		{			
			if ($code <= 255+$skipnum)
			{
				$result .= $current;
				$chaine = $buffer . $current;
				$dico[$nbChar] = $chaine;
				$nbChar++;
				$buffer = $current;
			}
			else
			{
				$chaine = $dico[$code];
				if (!$chaine) $chaine = $buffer . substr($chaine,0,1);
				$result .= $chaine;
				$dico[$nbChar] = $buffer . substr($chaine,0,1);
				$nbChar++;
				$buffer = $chaine;
			}
		}
	}
	return $result;
}

$input = utf8_encode("gregwegwergfdgfgsdasfæøåæøåfgewfwefgwegwgdsfg");
echo "Input:<br>$input<br><br>";
$result = compress("gregwegwergfdgfgsdasfæøåæøåfgewfwefgwegwgdsfg");
echo "Compressed result:<br>$result<br><br>";
$decom = utf8_encode(decompress($result));
echo "Decompressed result:<br>$decom<br><br>";
if ($input == $decom) {
	echo "Success!";
} else {
	echo "What... those aren't alike!";
}
?>