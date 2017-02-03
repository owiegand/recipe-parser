<?php 
//This function will be called with a URL to the recipe. 
function ParseRecipeData($RecipeURL){
	//Get HTML Contents
	$html =  file_get_contents($RecipeURL);
	
	$dom = new domDocument;
	
	//load the html into the object
	$dom->loadHTML($html);
	
	//discard white space 
	$dom->preserveWhiteSpace = false;

	//Create the new DOMXpath from DOM Object
	$xpath = new DOMXpath($dom);
	
	$URLHostName = parse_url($RecipeURL,PHP_URL_HOST);
	if($URLHostName == "www.delish.com"){
		//Call The Parser For Delish.com
		return DelishParser($xpath, $RecipeURL);
	}
	
}

//This function will be used to put together all the data into a returnable format
function CreateReturnJSON($TimeToMakeDish, $DishPrepTime, $DishServingAmount, $IngredientList, $Direction, $DishTitle, $URL, $Source){
	$RecipeInfo = array();
	$RecipeInfo['title'] = $DishTitle;
	$RecipeInfo['yield'] = $DishServingAmount;
	
	$CookTimeArray = array();
	$CookTimeArray['prep'] = $DishPrepTime;
	$CookTimeArray['cook'] = "";
	$CookTimeArray['total'] = $TimeToMakeDish;
	
	$RecipeInfo['time'] = $CookTimeArray;
	$RecipeInfo['ingredients'] = $IngredientList;
	$RecipeInfo['directions'] = $Direction;
	$RecipeInfo['url'] = $URL;
	$RecipeInfo['source'] = $Source;
	
	return json_encode($RecipeInfo);
}
//This function will be used to implement a custom ingredient parser. 
function ParseIngreident($IngreidentString){
	return $IngreidentString;
}

//WEBSITE PASRSERS BELOW HERE //
//This Function Will Handle Recipes on Delish's Website
function DelishParser($WebsiteXPath, $URL){
	//Time It Takes To Make Dish
	$TimeToMake = $WebsiteXPath->query("//*[@id='site-wrapper']/article/div[1]/div[2]/section/section[1]/div[1]/time")->item(0)->nodeValue;
	//Time To Prep Dish
	$PrepTime = $WebsiteXPath->query("//*[@id='site-wrapper']/article/div[1]/div[2]/section/section[1]/div[2]/time")->item(0)->nodeValue;
	
	//Serving Amount 
	$ServingAmount = $WebsiteXPath->query("//*[@id='site-wrapper']/article/div[1]/div[2]/section/section[1]/div[4]")->item(0)->nodeValue; 
	$ServingAmount = trim(explode(" ", $ServingAmount)[1]);
	
	//Ingredients List
	$Ingredients = $WebsiteXPath->query("//*[@id='site-wrapper']/article/div[1]/div[2]/section/section[2]/section[1]/div/ul/li");
	$IngredientsList = array();
	foreach ($Ingredients as $Ingredient) {
    	$IngredientsList[] = trim($Ingredient->nodeValue);
	}
	
	//Direction List
	$Directions = $WebsiteXPath->query("//*[@id='site-wrapper']/article/div[1]/div[2]/section/section[2]/section[2]/ol/li");
	$DirectionsList = array();
	foreach ($Directions as $Direction) {
    	$DirectionsList[] = ParseIngreident(trim($Direction->nodeValue));
	}

	//Recipe Title
	$Title = $WebsiteXPath->query("//*[@id='site-wrapper']/article/header/h1")->item(0)->nodeValue; 
	
	return CreateReturnJSON($TimeToMake, $PrepTime, $ServingAmount, $IngredientsList, $DirectionsList, $Title, $URL, "Delish.com");
}





?>