<?php
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	/**
	 * @global CMain $APPLICATION
	 */

	global $APPLICATION;

	//delayed function must return a string
	if(empty($arResult)) {
		return "";
	}

$strReturn = '';

$strReturn .= '<div class="breadcrumb-list" itemprop="http://schema.org/breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';

$itemSize = count($arResult);

for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	$arrow = ($index > 0 ? '<span>/</span>' : '');

	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
	{
		$strReturn .=  $arrow.'<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'" class="breadcrumb-item" id="bx_breadcrumb_'.$index.'" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.($index + 1).'" /></a>';
	}
	else
	{
		$strReturn .=  $arrow.'<a href="javascript:void(0)" title="'.$title.'" class="breadcrumb-item" id="bx_breadcrumb_'.$index.'" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.($index + 1).'" /></a>';
	}
}

$strReturn .= '</div>';

return $strReturn;
