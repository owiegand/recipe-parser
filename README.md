# recipe-parser
This PHP libaray is used to parse recipe websites and return ingredients, directions and other relevant information.

### Usage
```
$recipeData = ParseRecipeData($url);
```
Once the libaray has been imported to your working directory and the recipeparser.php file included in your current php file. Call the ParseRecipeData and pass in your recipe URL. Returned will be a JSON string containing data from the URL passed.

Possible JSON Stinrg Return:
```
{  
   "title":"Salsa Verde Shrimp with Cilantro Rice",
   "yield":"4",
   "time":{  
      "prep":"0:05",
      "cook":"",
      "total":"0:15"
   },
   "ingredients":[  
      "1 c. white rice",
      "extra-virgin olive oil",
      "1 lb. shrimp, peeled and deveined",
      "kosher salt",
      "Freshly ground black pepper",
      "Salsa verde with tomatillo",
      "1\/4 c. Chopped cilantro",
      "Lime wedges"
   ],
   "directions":[  
      "Cook rice according to package instructions.",
      "In a large skillet over medium-high heat, warm 1 tsp olive oil. Season shrimp with salt and black pepper and cook until cooked through, about 4 minutes. Add salsa verde and stir until warmed.",
      "Before serving, fluff rice and fold in cilantro. Top with salsa verde shrimp and serve with lime wedges."
   ],
   "url":"http:\/\/www.delish.com\/cooking\/recipe-ideas\/recipes\/a51161\/salsa-verde-shrimp-with-cilantro-rice-recipe\/",
   "source":"Delish.com"
}
```

### Currently Supported Websites:
* Delish.com

More spported websites will be added through regular updates.

### How To Contribute
Most of updates and contributions to this libaray will come by adding new websites that this parser can handle. This libaray creates a very simple way to add new "website parsers". This is the step by step process for adding a new process:

1. Create a parsing function inside of recipeparser.php

  ```
  function <WebsiteName>Parser($WebsiteXPath, $URL){
  }
  ```
2. Inside of the PHP function ParseRecipeData in recipeparser.php, a new if case needs to be set up to catch the wesbites host name. EG. for foodnetwork.com

  ```
  if($URLHostName == "www.foodnetwork.com"){
      //Call The Parser For foodnetwork.com.com
      return FoodnetworkParser($xpath, $RecipeURL);
  }
  ```
  
3. Inside of the function you just created you needed pull the data listed below from the website. The function has two arguements: the website ULR and an XPath. XPath is the recommended way pulling the data of the website. 

  ```
  $TimeToMake;
  $PrepTime;
  $ServingAmount; 
  $IngredientsList = array(); //List of Ingredients
  $DirectionsList = array(); //List of Directions
  $Title
  ```
4. You then need to pass these values into the CreateReturnJSON function

### Intergration of 3rd Party Ingredient Parsers
Current Recipe Parser ships with this implementation:
```
function ParseIngreident($IngreidentString){
	return $IngreidentString;
}
```
This function allows you to add custom logic to how this parser handles and returns ingredients. With no changes to the above function, it will return the default ingredient string. This would be a perfect place to integrate [ingredient-parser](https://github.com/owiegand/ingredient-parser)

<!--
<GITHUBPARSER>
{
  "Icon": "fa-cutlery"
}
</GITHUBPARSER>
-->

