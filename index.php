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

$input = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus lobortis, libero vestibulum tincidunt posuere, felis nisi lacinia libero, eu consectetur est libero eu diam. Integer urna urna, venenatis ac lobortis vel, pharetra a neque. Curabitur posuere diam sed tortor aliquam suscipit. Curabitur sodales, tortor vitae consectetur scelerisque, leo nisi laoreet ipsum, sed pellentesque enim dui ac leo. Nam sed porta leo. Nulla at facilisis lacus. Aliquam erat volutpat. Sed nec euismod dolor. Integer dictum magna tempus est blandit aliquet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin at massa nibh, et facilisis sapien. Etiam a justo at urna mattis faucibus a commodo diam. Curabitur sagittis convallis ante, vel laoreet ipsum cursus sed. Nulla eu lectus sem. Pellentesque viverra vulputate tortor, ut semper est viverra in. Mauris est tortor, fringilla a adipiscing non, consequat non eros.

Proin posuere massa nec nisi ornare ut consectetur sem tempor. Integer volutpat pharetra felis eu lobortis. Proin lacinia tortor a augue vulputate ullamcorper. Phasellus laoreet mollis tempus. Aliquam tincidunt, elit cursus placerat ornare, nibh sem pharetra ipsum, ut tempus lacus quam id est. Phasellus in sem mauris. Aenean hendrerit bibendum dui, id aliquet dui eleifend vitae. Vivamus pellentesque mauris vel erat fringilla molestie. Vestibulum sodales pretium dolor nec commodo. Nunc mi leo, scelerisque at fermentum cursus, egestas non mi. Donec porttitor tempor velit eget vulputate. Integer tempor condimentum diam. Mauris nisi purus, varius nec porttitor sit amet, luctus condimentum sapien. Cras libero lectus, mattis at condimentum sit amet, ultrices et orci.

Aliquam nisi nulla, tristique in facilisis id, venenatis non ligula. Vivamus pellentesque libero sit amet tellus tristique volutpat. Etiam commodo lacus nec velit blandit in vestibulum libero fringilla. Nullam eget sapien lacus, non tempor nulla. Nulla ac ligula ante. Maecenas placerat quam velit. Phasellus non urna arcu, dignissim hendrerit lorem. Praesent quis leo non sapien accumsan fringilla. Sed sed urna vitae elit gravida sagittis. Maecenas vestibulum nisl in sem porta vitae fermentum ante commodo.

Nulla facilisi. Sed eget ligula vitae magna laoreet egestas a nec libero. Nunc tincidunt ligula nec nisi scelerisque euismod. Vivamus congue, magna a semper iaculis, neque eros mollis sem, eget semper urna purus sed odio. Etiam fringilla molestie nunc sit amet tincidunt. Suspendisse potenti. Donec nibh sem, pellentesque lobortis euismod non, scelerisque gravida enim. Fusce eu urna convallis mauris mattis hendrerit. Donec porta metus eu magna aliquam lobortis. Suspendisse hendrerit, magna sed porttitor varius, tellus leo feugiat justo, sed interdum sapien dui sit amet tellus. Aliquam magna enim, tincidunt ut tincidunt id, tempor non ipsum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer pharetra, magna eget mollis posuere, tortor nibh eleifend arcu, sed vehicula augue libero eget sapien. In hac habitasse platea dictumst. Nam et erat enim, non porta nulla. Praesent fermentum dolor hendrerit dui semper et dapibus justo fermentum.";

echo "Input:<br>".utf8_encode($input)."<br><br>";
$result = compress($input);
echo "Compressed result:<br>$result<br><br>";
$decom = utf8_encode(decompress($result));
echo "Decompressed result:<br>$decom<br><br>";
if (utf8_encode($input) == $decom) {
	echo "Success!";
} else {
	echo "What... those aren't alike!";
}
?>