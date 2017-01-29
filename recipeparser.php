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
    	$DirectionsList[] = trim($Direction->nodeValue);
	}

	//Recipe Title
	$Title = $WebsiteXPath->query("//*[@id='site-wrapper']/article/header/h1")->item(0)->nodeValue; 
	
	return CreateReturnJSON($TimeToMake, $PrepTime, $ServingAmount, $IngredientsList, $DirectionsList, $Title, $URL, "Delish.com");
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


/*
[title] => Chai-Spiced Hot Chocolate
    [description] => 
    [notes] => 
    [yield] => 6 servings
    [source] => Bon Appétit
    [url] => http://www.bonappetit.com/recipes/quick-recipes/2010/02/chai_spiced_hot_chocolate
    [categories] => Array
        (
        )

    [photo_url] => http://www.bonappetit.com/wp-content/uploads/2011/01/mare_chai_spiced_hot_chocolate_h1.jpg
    [status] => recipe
    [time] => Array
        (
            [prep] => 15
            [cook] => 0
            [total] => 25
        )

    [ingredients] => Array
        (
            [0] => Array
                (
                    [name] => 
                    [list] => Array
                        (
                            [0] => 4 cups low-fat (1%) milk
                            [1] => 3/4 cup bittersweet chocolate chips
                            [2] => 10 cardamom pods, coarsely cracked
                            [3] => 1/2 teaspoon whole allspice, cracked
                            [4] => 2 cinnamon sticks, broken in half
                            [5] => 1/2 teaspoon freshly ground black pepper
                            [6] => 5 tablespoons (packed) golden brown sugar, divided
                            [7] => 6 quarter-size slices fresh ginger plus 1/2 teaspoon grated peeled fresh ginger
                            [8] => 1 teaspoon vanilla extract, divided
                            [9] => 1/2 cup chilled whipping cream
                        )

                )

        )

    [instructions] => Array
        (
            [0] => Array
                (
                    [name] => 
                    [list] => Array
                        (
                            [0] => Combine first 6 ingredients, 4 tablespoons brown sugar, and ginger slices in medium saucepan. Bring almost to simmer, whisking frequently. Remove from heat; cover and steep 10 minutes. Mix in 1/2 teaspoon vanilla.
                            [1] => Meanwhile, whisk cream, remaining 1 tablespoon brown sugar, grated ginger, and remaining 1/2 teaspoon vanilla in medium bowl to peaks.
                            [2] => Strain hot chocolate. Ladle into 6 mugs. Top each with dollop of ginger cream.
                        )

                )

        )

    [credits] => 
)
*/
?>