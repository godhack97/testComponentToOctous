<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
// // $APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("SODAMODA");
?>


<link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css"/>
<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>


<? $APPLICATION->IncludeComponent(
	"arlix:home.premiere_slider",
	"",
	[
		"IBLOCK_ID" => 24,
		"SECTION_ID" => 131,
		"COUNT" => 1,
	],
	false
); ?>
<? $APPLICATION->IncludeComponent(
	"arlix:home.premiere_complect",
	"big_left",
	[
		"IBLOCK_ID" => 24,
		"SECTION_ID" => 132,
		"COUNT" => 4,
	],
	false
); ?>
<? $APPLICATION->IncludeComponent(
	"arlix:home.premiere_one",
	"big_right",
	[
		"IBLOCK_ID" => 24,
		"SECTION_ID" => 133,
		"COUNT" => 1,
	],
	false
); ?>
<? $APPLICATION->IncludeComponent(
	"arlix:home.premiere_one",
	"big_left",
	[
		"IBLOCK_ID" => 24,
		"SECTION_ID" => 134,
		"COUNT" => 1,
	],
	false
); ?>
<? $APPLICATION->IncludeComponent(
	"arlix:home.premiere_complect",
	"big_right",
	[
		"IBLOCK_ID" => 24,
		"SECTION_ID" => 135,
		"COUNT" => 4,
	],
	false
); ?>


<div class="sliderIndexTwo-block">

    <div class="sliderIndexTwo swiper">
        <!-- Additional required wrapper -->
        <h2 class="font-caption">В Тренде</h2>
        <div class="swiper-wrapper">
            <div class="sliderIndexTwo-img swiper-slide">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/second.jpg" alt="">
            </div>
            <div class="sliderIndexTwo-img swiper-slide">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/two.jpeg" alt="">
            </div>
            <div class="sliderIndexTwo-img swiper-slide">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/three.jpg" alt="">
            </div>
            <div class="sliderIndexTwo-img swiper-slide">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/second.jpg" alt="">
            </div>
            <div class="sliderIndexTwo-img swiper-slide">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/two.jpeg" alt="">
            </div>
        </div>
				<div class="swiper-info">
					<span>New Season</span>
          <p class="swiper-title">Платье- мини из крепа</p>
					<p class="swiper-desc">Выполнено из крепа на основе вискозы</p>
				</div>

        <div class="swiper-pagination"></div>
    </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>