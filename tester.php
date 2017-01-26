<?php
include("recipeparser.php");

$url = "http://www.delish.com/cooking/recipe-ideas/recipes/a51161/salsa-verde-shrimp-with-cilantro-rice-recipe/";
$recipeData = ParseRecipeData($url);
print_r($recipeData);

echo "<br><br>";
?>