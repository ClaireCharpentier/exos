<?php
$quantite = 5;
$additionneur = function($nbr) use($quantite)
{
  return $nbr + $quantite;
};

$listeNbr = [1, 2, 3, 4, 5];

$listeNbr = array_map($additionneur, $listeNbr);

var_dump($listeNbr);
// On obtient là aussi le tableau [6, 7, 8, 9, 10]