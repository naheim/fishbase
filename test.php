<?php
	
	$pagestring = file_get_contents("testHtml.html");
    
    $beginstr = "Phylogenetic diversity index";
	$endstr = "<\/div>";
	$x = array();
	$y = preg_match("/(".$beginstr.")(.*?)(".$endstr.")/s", $pagestring, $x);
		
	//print_r($x);
	
	$z = 100 % 10;
	$z2 = 123 % 10;
	
	
	echo "0 -- ".(0 % 20)."\n";
	echo "1 -- ".(1 % 20)."\n";
	echo "3 -- ".(3 % 20)."\n";
	echo "10 -- ".(10 % 20)."\n";
	echo "20 -- ".(20 % 20)."\n";
	echo "21 -- ".(21 % 20)."\n";
	echo "30 -- ".(30 % 20)."\n";
	echo "40 -- ".(40 % 20)."\n";
	echo "60 -- ".(60 % 20)."\n";
	echo "80 -- ".(80 % 20)."\n";
	echo "100 -- ".(100 % 20)."\n";
	echo "120 -- ".(120 % 20)."\n";
	echo "123 -- ".(123 % 20)."\n";

	
?>