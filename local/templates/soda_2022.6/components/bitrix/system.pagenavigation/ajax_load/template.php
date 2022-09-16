<?php
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

  global $USER;

	$this->createFrame()->begin('');
  
  if($arResult["NavPageCount"] > 1){
    if ($arResult["NavPageNomer"]+1 <= $arResult["nEndPage"]){
      $plus = $arResult["NavPageNomer"]+1;
      $url = $arResult["sUrlPathParams"] . "PAGEN_1=".$plus;
      //$url = $arResult["sUrlPathParams"] . "PAGEN_". $arResult['nStartPage']."=".$plus;
      ?>
      <div class="load_more" data-url="<?=$url?>"></div>
      <?php
    }
    else{
      ?>
      <div class="load_more"></div>
      <?php
    }
  }
?>