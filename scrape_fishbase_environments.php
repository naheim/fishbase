<?php
	require('~/Box/Includes/directoryinfo.php');
	include('~/Box/Includes/connect.php');
	include('~/Box/Includes/functions.php');
	
	$t0 = microtime(true);
	$counter = 0;
		
	$q = "SELECT taxa.id, taxon_name, taxon_rank, taxon_genus, taxon_species, marine, freshwater, brackish, non_marine, taxon_url
	FROM paleosize.taxa
	INNER JOIN paleosize.specimens on taxa.id = taxon_id
	INNER JOIN paleosize.measurements on specimens.id = specimen_id 
	INNER JOIN paleosize.refs on measurements.ref_id = refs.id
	INNER JOIN paleosize.databases_taxa ON taxa.id = databases_taxa.taxon_id
	WHERE electronic_resource_name = 'FishBase'
	GROUP BY taxa.id, taxon_name, marine, freshwater, brackish, terrestrial, non_marine, taxon_url;";
	$result = pg_query($connect_morpho_owner, $q);
	while($row = pg_fetch_assoc($result)) {
		$temp = explode(".php?id=", $row['taxon_url']);
		$i = $temp[1];

		$feed = simplexml_load_file("http://www.fishbase.us/maintenance/FB/showXML.php?identifier=FB-".$i);
		
		if(count($feed) > 0 && count($feed->taxon) > 0) {		
			$habitat = NULL;	
			$origEnv = array('marine'=>'NULL', 'freshwater'=>'NULL', 'brackish'=>'NULL', 'non_marine'=>'NULL');
			$environments = array('marine'=>'NULL', 'freshwater'=>'NULL', 'brackish'=>'NULL', 'non_marine'=>'NULL');

			foreach($feed->taxon->dataObject as $do) {
				if($do->children("http://purl.org/dc/elements/1.1/")->identifier == "FB-Habitat-".$i) {
					$habitat = $do->children("http://purl.org/dc/elements/1.1/")->description;					
					if(stripos($habitat, "marine") !== false) $environments['marine'] = 'true';
					if(stripos($habitat, "freshwater") !== false) $environments['freshwater'] = 'true';
					if(stripos($habitat, "brackish") !== false) $environments['brackish'] = 'true';
				}
			}
			if($environments['freshwater'] == 'true') {
				$environments['non_marine'] = 'true';
			} else if($environments['marine'] == 'true' && $environments['freshwater'] == 'NULL' && $environments['brackish'] == 'NULL') {
				$environments['non_marine'] = 'false';
			}
			
			$q = "UPDATE paleosize.taxa 
			SET marine = ".$environments['marine'].",
			freshwater = ".$environments['freshwater'].",
			brackish = ".$environments['brackish'].",
			non_marine = ".$environments['non_marine'].",
			modified = now()
			WHERE id = ".$row['id'];  //echo $q."\n\n";
			pg_query($connect_morpho_owner, $q);
		}
		if($counter % 500 == 0) echo $counter."\n";
		++$counter;
	}
	pg_free_result($result);

	// timer		
	$t1 = microtime(true);
	echo "\nThis process took ".time_elapsed($t1, $t0).".\n";
	//beep();
	
	// the end
	exit();	
?>