<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

global $USER, $APPLICATION;
if(!is_object($USER)) {
  $USER = new CUser;
}

$result = Array();
$result['message'] = 'Success';


$PRODUCT_ID = (int) htmlspecialcharsbx($_POST['product_id']);
$SECTION_ID = (int) htmlspecialcharsbx($_POST['section_id']);
$DIRECTION  = htmlspecialcharsbx($_POST['direction']);


// Делаем инверсию для взятия нужного товара
// Изначально DIRECTION - это направление свайпа
if($DIRECTION == 'left')
  $DIRECTION = 'right';
else if($DIRECTION == 'right')
  $DIRECTION = 'left';


if(empty($PRODUCT_ID))
{
  $result['message'] = 'Empty data';
  exit(json_encode($result));
} 

$arSelect   = Array(
  'ID', 
  'CODE', 
  'NAME', 
  'DETAIL_PAGE_URL', 
  'DETAIL_PICTURE', 
  'DETAIL_TEXT', 
  'PROPERTY_VIDEO_IMAGE', 
  'PROPERTY_VIDEO_FILES', 
  'PROPERTY_IMAGES', 
  'PROPERTY_SIZES', 
  'PROPERTY_CML2_ARTICLE', 
  'PROPERTY_COLOR',
  'PROPERTY_FABRIC',
  'PROPERTY_COMPOSITION',
  'PROPERTY_CARE',
);
$arFilter   = Array(
  "IBLOCK_ID" => CATALOG_ID,
  "SECTION_ID" => $SECTION_ID,
  "ACTIVE" => "Y",
  "CHECK_PERMISSIONS" => "Y",
);
$arSort     = Array("SORT" => "DESC", "ID" => "DESC");

$arItems     = Array();
$arNavParams = Array("nPageSize" => 1, "nElementID" => $PRODUCT_ID);
$rsElement   = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);

while($arElement = $rsElement->GetNext()) {
  $arItems[] = $arElement;
}

if(count($arItems) == 3) 
{
  $arResult['left']  = $arItems[0];
  $arResult['right'] = $arItems[2];
}
elseif(count($arItems) == 2) 
{
  if($arItems[0]["ID"] != $PRODUCT_ID)
    $arResult['left'] = $arItems[0];
  else
    $arResult['right'] = $arItems[1];
}


$result['html']  = 'N';
$result['start']['left'] = 'N';
$result['start']['right'] = 'N';

if($DIRECTION == 'start')
{
  if(!empty($arResult['left']))
  {
    $result['start']['left_id'] = $arResult['left']['ID'];
    $result['start']['left'] = getHtml($arResult['left']);
  }
  
  if(!empty($arResult['right']))
  {
    $result['start']['right_id'] = $arResult['right']['ID'];
    $result['start']['right'] = getHtml($arResult['right']);
  }
}
else if(!empty($arResult[$DIRECTION]))
{
  $result['html'] = getHtml($arResult[$DIRECTION]);
}

$result['id'] = $arResult[$DIRECTION]['ID'];
$result['data'] = $arResult;
$result['direction'] = $DIRECTION;
$result['direction_post'] = $_POST['direction'];

function getHtml($arElement) 
{
  $html = '
  <div class="product-detail" data-id="'. $arElement['ID'] .'">
    <div class="images">
      <div class="">
        <div class="product-detail-slider">
          <div class="image-slider image-slider-'. $arElement['ID'] .' active">';

            if(!empty($arElement['DETAIL_PICTURE']))
            {
              $arDetailImage = CFile::ResizeImageGet($arElement['DETAIL_PICTURE'], array('width'=>980, 'height'=>1470), BX_RESIZE_IMAGE_EXACT);
              $html .= '<div class="image"><img src="'. $arDetailImage['src'] .'" /></div>';
            }


            if(!empty($arElement['PROPERTY_IMAGES_VALUE']))
            {
              foreach($arElement['PROPERTY_IMAGES_VALUE'] as $imageID)
              {
                $arImage = CFile::ResizeImageGet($imageID, array('width'=>980, 'height'=>1470), BX_RESIZE_IMAGE_EXACT);
                $html .= '<div class="image"><img src="'. $arImage['src'] .'" /></div>';
              }
            }

            $html .= '
          </div>
        </div>
      </div>
    </div>
    <div class="info js-sticky-kit-go">
      <div class="wrapper">
        <div class="mobile-shutter-close js-shutter-close"><img src="/local/templates/sodamoda/images/icons/times.svg"></div>
        <div class="mobile-touch-line"></div>
        <div class="mobile-message-added">
          <div class="wrapper">
            <div class="text">Размер <span class="insert-size">M (RU 44/46)</span> добавлен<br>в&nbsp;вашу корзину</div>
            <a class="link hover" href="/basket/">Посмотреть</a>
          </div>
        </div>
        <div class="indent">
          <h1>'. $arElement['NAME'] .'</h1>
          <div class="price">1 999 руб.</div>

            <div class="mobile-buy mobile-display">
              <!-- js-addtobasket -->
              <a class="ibutton hollow js-addtobasket-go" data-id="7" data-size="" rel="nofollow">Добавить</a>
              <div class="size-wrap">
                <div class="size">
                  <div class="item"><a class="js-addtobasket-go-item hover" data-id="7" data-elurl="/catalog/jackets/top-site/" data-propid="1" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="XXS (RU 38/40)">XXS (RU 38/40)</a></div>
                  <div class="item"><a class="js-addtobasket-go-item hover" data-id="7" data-elurl="/catalog/jackets/top-site/" data-propid="2" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="XS (RU 40/42)">XS (RU 40/42)</a></div>
                  <div class="item"><a class="js-addtobasket-go-item hover" data-id="7" data-elurl="/catalog/jackets/top-site/" data-propid="3" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="S (RU 42/44)">S (RU 42/44)</a></div>
                  <div class="item"><a class="js-addtobasket-go-item hover" data-id="7" data-elurl="/catalog/jackets/top-site/" data-propid="13" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="M (RU 44/46)">M (RU 44/46)</a></div>
                  <div class="item"><a class="js-addtobasket-go-item hover" data-id="7" data-elurl="/catalog/jackets/top-site/" data-propid="14" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="L (RU 46/48)">L (RU 46/48)</a></div>
                  <div class="item"><a class="js-addtobasket-go-item hover" data-id="7" data-elurl="/catalog/jackets/top-site/" data-propid="15" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="XL (RU 48/50)">XL (RU 48/50)</a></div>
                  <div class="item"><a class="js-addtobasket-go-item hover" data-id="7" data-elurl="/catalog/jackets/top-site/" data-propid="16" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="XXL (RU 50/52)">XXL (RU 50/52)</a></div>
                </div>
                <div class="table-size hover">
                  <div class="caption js-open-popup-table-sizes">Какой у меня размер?</div>
                  <div class="icon js-open-popup-table-sizes"><img src="/local/templates/sodamoda/images/icons/hanger.svg"></div>
                </div>
                <div class="close hover js-popup-close"><img src="/local/templates/sodamoda/images/icons/close.svg"></div>
              </div>
            </div>

            <div class="props">
                                      <div class="item">
                <span class="name">Цвет:</span>
                                  <span class="value">Молочный, Розовый</span>
                              </div>
                                      <div class="item">
                <span class="name">Ткань:</span>
                                  <span class="value">Лён</span>
                              </div>
                      </div>
                      <p class="description">Топ без рукавов, с круглым вырезом, узлом спереди и разрезом с застежкой на пуговицу сзади. </p>

          <div class="desktop-display">
                          <div class="size">
                                    <div class="item"><a data-elurl="/catalog/jackets/top-site/" data-propid="1" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="XXS (RU 38/40)" class="js-set-size hover">XXS (RU 38/40)</a></div>
                                    <div class="item"><a data-elurl="/catalog/jackets/top-site/" data-propid="2" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="XS (RU 40/42)" class="js-set-size hover">XS (RU 40/42)</a></div>
                                    <div class="item"><a data-elurl="/catalog/jackets/top-site/" data-propid="3" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="S (RU 42/44)" class="js-set-size hover">S (RU 42/44)</a></div>
                                    <div class="item active"><a data-elurl="/catalog/jackets/top-site/" data-propid="13" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="M (RU 44/46)" class="js-set-size hover">M (RU 44/46)</a></div>
                                    <div class="item"><a data-elurl="/catalog/jackets/top-site/" data-propid="14" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="L (RU 46/48)" class="js-set-size hover">L (RU 46/48)</a></div>
                                    <div class="item"><a data-elurl="/catalog/jackets/top-site/" data-propid="15" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="XL (RU 48/50)" class="js-set-size hover">XL (RU 48/50)</a></div>
                                    <div class="item"><a data-elurl="/catalog/jackets/top-site/" data-propid="16" data-elid="7" data-name="Размеры" data-code="SIZES" data-value="XXL (RU 50/52)" class="js-set-size hover">XXL (RU 50/52)</a></div>
                              </div>
                        <div class="table-size hover">
              <div class="caption js-open-popup-table-sizes">Таблица размеров</div>
              <div class="icon js-open-popup-table-sizes"><img src="/local/templates/sodamoda/images/icons/hanger.svg"></div>
            </div>
                          <a class="ibutton js-addtobasket" data-id="7" data-size="" rel="nofollow">Добавить</a>
                      </div>

          <div class="links">
                          <div class="item">
                <a class="link hover js-open-popup-cares">Состав и уход за изделием</a>
                <div class="info-cares">
                                      <div class="caption">Верхняя часть</div>
                    90% лён, 10% бамбук                    <br><br><br>
                                      <div class="caption">Воротник</div>
                    100% полиэстерн                    <br><br><br>
                                                        <div class="caption">Уход</div>
                    <div class="cares">
                      <img src="/upload/iblock/213/30.svg" title="Вертикальная сушка (на веревке)"><img src="/upload/iblock/a54/37.svg" title="Гладить при высокой температуре (до 200°С)"><img src="/upload/iblock/0f6/35.svg" title="Гладить при низкой температуре (до 110°С)"><img src="/upload/iblock/1c3/40.svg" title="Cухая чистка (химчистка)">                    </div>
                                  </div>
              </div>
                        <div class="item">
              <a class="link hover">Доставка, обмен и возврат</a>
            </div>
            <div class="item">
              <a class="link hover js-share-display">Поделиться</a>
              <div class="wrap-share">
                <div class="ya-share2 ya-share2_inited" data-services="vkontakte,facebook,whatsapp,viber,twitter,skype,telegram,linkedin" data-size="s"><div class="ya-share2__container ya-share2__container_size_s"><ul class="ya-share2__list ya-share2__list_direction_horizontal"><li class="ya-share2__item ya-share2__item_service_vkontakte"><a class="ya-share2__link" href="https://vk.com/share.php?url=https%3A%2F%2Fart-view.ru%2Fcatalog%2Fjackets%2Ftop-site%2F&amp;title=%D0%9F%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%20%D0%BD%D0%B0%20%D1%80%D0%B0%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B8&amp;utm_source=share2" rel="nofollow noopener" target="_blank" title="ВКонтакте"><span class="ya-share2__badge"><span class="ya-share2__icon"></span><span class="ya-share2__counter"></span></span><span class="ya-share2__title">ВКонтакте</span></a></li><li class="ya-share2__item ya-share2__item_service_facebook"><a class="ya-share2__link" href="https://www.facebook.com/sharer.php?src=sp&amp;u=https%3A%2F%2Fart-view.ru%2Fcatalog%2Fjackets%2Ftop-site%2F&amp;title=%D0%9F%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%20%D0%BD%D0%B0%20%D1%80%D0%B0%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B8&amp;utm_source=share2" rel="nofollow noopener" target="_blank" title="Facebook"><span class="ya-share2__badge"><span class="ya-share2__icon"></span><span class="ya-share2__counter"></span></span><span class="ya-share2__title">Facebook</span></a></li><li class="ya-share2__item ya-share2__item_service_whatsapp"><a class="ya-share2__link" href="https://api.whatsapp.com/send?text=%D0%9F%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%20%D0%BD%D0%B0%20%D1%80%D0%B0%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B8%20https%3A%2F%2Fart-view.ru%2Fcatalog%2Fjackets%2Ftop-site%2F&amp;utm_source=share2" rel="nofollow noopener" target="_blank" title="WhatsApp"><span class="ya-share2__badge"><span class="ya-share2__icon"></span></span><span class="ya-share2__title">WhatsApp</span></a></li><li class="ya-share2__item ya-share2__item_service_viber"><a class="ya-share2__link" href="viber://forward?text=%D0%9F%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%20%D0%BD%D0%B0%20%D1%80%D0%B0%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B8%20https%3A%2F%2Fart-view.ru%2Fcatalog%2Fjackets%2Ftop-site%2F&amp;utm_source=share2" rel="nofollow" target="_blank" title="Viber"><span class="ya-share2__badge"><span class="ya-share2__icon"></span></span><span class="ya-share2__title">Viber</span></a></li><li class="ya-share2__item ya-share2__item_service_twitter"><a class="ya-share2__link" href="https://twitter.com/intent/tweet?text=%D0%9F%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%20%D0%BD%D0%B0%20%D1%80%D0%B0%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B8&amp;url=https%3A%2F%2Fart-view.ru%2Fcatalog%2Fjackets%2Ftop-site%2F&amp;utm_source=share2" rel="nofollow noopener" target="_blank" title="Twitter"><span class="ya-share2__badge"><span class="ya-share2__icon"></span></span><span class="ya-share2__title">Twitter</span></a></li><li class="ya-share2__item ya-share2__item_service_skype"><a class="ya-share2__link" href="https://web.skype.com/share?url=https%3A%2F%2Fart-view.ru%2Fcatalog%2Fjackets%2Ftop-site%2F&amp;utm_source=share2" rel="nofollow noopener" target="_blank" title="Skype"><span class="ya-share2__badge"><span class="ya-share2__icon"></span></span><span class="ya-share2__title">Skype</span></a></li><li class="ya-share2__item ya-share2__item_service_telegram"><a class="ya-share2__link" href="https://telegram.me/share/url?url=https%3A%2F%2Fart-view.ru%2Fcatalog%2Fjackets%2Ftop-site%2F&amp;text=%D0%9F%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%20%D0%BD%D0%B0%20%D1%80%D0%B0%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B8&amp;utm_source=share2" rel="nofollow noopener" target="_blank" title="Telegram"><span class="ya-share2__badge"><span class="ya-share2__icon"></span></span><span class="ya-share2__title">Telegram</span></a></li><li class="ya-share2__item ya-share2__item_service_linkedin"><a class="ya-share2__link" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=https%3A%2F%2Fart-view.ru%2Fcatalog%2Fjackets%2Ftop-site%2F&amp;title=%D0%9F%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%20%D0%BD%D0%B0%20%D1%80%D0%B0%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B8&amp;utm_source=share2" rel="nofollow noopener" target="_blank" title="LinkedIn"><span class="ya-share2__badge"><span class="ya-share2__icon"></span></span><span class="ya-share2__title">LinkedIn</span></a></li></ul></div></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>';
            
  return 'N'; //$html;
}

exit(json_encode($result));