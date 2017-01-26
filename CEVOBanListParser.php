<?php
/*** a new dom object ***/
   
	//while($i <= 20){
		
		$html =  file_get_contents("http://cevo.com/member/bans/");
		$dom = new domDocument;

		/*** load the html into the object ***/
		$dom->loadHTML($html);

		/*** discard white space ***/
		$dom->preserveWhiteSpace = false;

		/*** the table by its tag name ***/
		$tables = $dom->getElementsByTagName('table');

		/*** get all rows from the table ***/
		$rows = $tables->item(0)->getElementsByTagName('tr');

		/*** loop over the table rows ***/
		$i =0;
		
		foreach ($rows as $row)
		{
			$IDFound =false;
			$SteamIDsFromProfilePage = array();
			/*** get each column by tag name ***/
			$cols = $row->getElementsByTagName('td');
			//TODO: Remove Any Bans From Other Games
			if($cols->item(2)->nodeValue.trim() == "Cheating"){
				echo "Name: ".$cols->item(1)->nodeValue.'<br />';
				//$StuffToRemove = array("Cheating ", "(", ")");
				//echo "SteamID: ".str_replace($StuffToRemove,"",$cols->item(2)->nodeValue).'<br />';
			
				foreach($cols->item(1)->getElementsByTagName('a') as $link) {
					//Gets The Link To The Users Cevo Profile
					$LinkToProfile = $link->getAttribute('href');
				}
				//Go To The Cevo Profile Page And Try To Parse The Acivity Feed To Find The SteamID
				$Profilehtml =  file_get_contents("http://cevo.com".$LinkToProfile);
				echo"Profile Link: http://cevo.com".$LinkToProfile."<br/>"; //TO SHOW PROFILE LINK USE THIS LINE!
				
				$Profiledom = new domDocument;
				$Profiledom->loadHTML($Profilehtml);
				$divs = $Profiledom->getElementsByTagName('div');
				foreach ($divs as $div) {
					
					foreach ($div->attributes as $attr) {
						$name = $attr->nodeName;
						$value = $attr->nodeValue;
						if($name == "class" && $value == "feed-text"){
							//echo "Attribute '$name' :: '$value'<br />";
							//echo "DIV Debug: ".$div->nodeValue."<br/>";
							//$pattern = '/STEAM_[01]:[01]:([0-9]{9})/'; Old Version. The Steam ID Doesnt Have To Have 9 Numbers at The End
							$pattern ='/STEAM_[01]:[01]:(\d+)/'; //Now We Are Allowing Any Number of Digits At The End
							$String = $div->nodeValue.trim();
							preg_match($pattern, $String, $matches, PREG_OFFSET_CAPTURE);
							$SingleSteamID = $matches[0][0].trim();
							//echo "Matcher Output: ".$SingleSteamID."<br/>";
							if (in_array($SingleSteamID, $SteamIDsFromProfilePage) || $SingleSteamID == "") {
							}else {
								echo "SteamID: ".$SingleSteamID."<br/>";
								$SteamIDsFromProfilePage[] = $SingleSteamID;
								$IDFound =true;
							}
								
						}else {
							//$IDFound =false;
							//TODO: Throw An Error If We Never Find The Feed-Text Div.
						}
					}
				}
				
				if(!$IDFound){
					echo "Using Roster Search: Yes <br />";
					$SteamIDs = array();
					foreach ($divs as $div) {
						foreach ($div->attributes as $attr) {
							$name = $attr->nodeName;
							$value = $attr->nodeValue;
							//echo "Attribute '$name' :: '$value'<br />";
							if($name == "class" && $value == "roster-information"){
								foreach($div->getElementsByTagName('a') as $link) {
									//Gets The Link To The Users Cevo Profile
									$Link = $link->getAttribute('href')."<br/>";
									if(strpos($Link,"roster")){
										
										//echo $Link;
										//Now For Each Link Find The User And Grab His SteamID. He Might Have Switched It Between Teams So Record All If There Are Different.
										
										$Profile2html =  file_get_contents("http://cevo.com".$Link);
										$Profile2dom = new domDocument;
										$Profile2dom->loadHTML($Profile2html);
										//echo "http://cevo.com".$Link;
										$divs2 = $Profile2dom->getElementsByTagName('div');
										
										foreach ($divs2 as $div2) {
						
											foreach ($div2->attributes as $attr) {
												$name = $attr->nodeName;
												$value = $attr->nodeValue;
												//echo "Attribute '$name' :: '$value'<br />";
												if($name == "class" && $value == "member-details text-shadow"){
													//echo "Text1: ".$div2->nodeValue.trim()."<br/>";
													if(strpos($div2->nodeValue.trim(),$cols->item(1)->nodeValue.trim()) !== false){
														//echo "Text2: ".$div2->nodeValue.trim()."<br/>";
														//$pattern = '/STEAM_[01]:[01]:([0-9]{8,9})/'; //IDs On Feed Are 9 But IDs on Rosters Are Only 8 Digits
														$pattern ='/STEAM_[01]:[01]:(\d+)/'; //Now We Are Allowing Any Number of Digits At The End
														$String = $div2->nodeValue.trim();
														
														preg_match($pattern, $String, $matches, PREG_OFFSET_CAPTURE);
														$SingleSteamID = $matches[0][0].trim();
														if (in_array($SingleSteamID, $SteamIDs)) {
														}else {
															echo "SteamID: ".$SingleSteamID."<br/>";
															$SteamIDs[] = $SingleSteamID;
															$IDFound =true;
														}
													}
												}
											}
										}
										
										
									}
								}
							}
						}
					}
				} else {
					echo "Using Roster Search: No <br />";
				}
				if(!$IDFound){
					//We Werent Able To Find The ID on Their Profile Page. Look For Them on There Team Pages.
					echo "SteamID: Unable To Find <br />";
				}
				
				
				
				
				echo "Ban Reason: ".$cols->item(2)->nodeValue.'<br />';
				echo "Ban Date: ".$cols->item(3)->nodeValue.'<br />';
				echo '<hr />';
			}
			
			//Used When Testing To Limit The Results
			/*
			if($i > 200){
				break;
			} else {
				$i++;
			}
			*/
			
			
		} 
	//}
?>