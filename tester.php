<?php
include("recipeparser.php");

$url = "http://www.delish.com/cooking/recipe-ideas/recipes/a51161/salsa-verde-shrimp-with-cilantro-rice-recipe/";
$recipeData = ParseRecipeData($url);
var_dump($recipeData);

echo "<br><br>";
?>