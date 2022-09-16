<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?><h1>Контакты</h1>
<br>
<div class="contacts-sections">
   <div class="block block-chat">
      <div class="cols">
         <h2>Чат/WhatsApp/Telegram</h2>
         <p>
            Ежедневно&nbsp;с 10:00 до 22:00 по московскому времени
         </p>
         <span data-comment="span здесь нужен для отступа до кнопок"></span>
      </div>
      <div class="cols">
         <button type="button" class="ibutton hollow js-open-chat">Написать в чат</button>
         <a class="ibutton hollow js-metrika-order-whatsapp" href="whatsapp://send?phone=<?= PHONE_WHATSAPP ?>" target="_blank" title="Связаться с нами в WhatsApp">Написать в Whatsapp</a>
         <a class="ibutton hollow" href="https://t.me/SODAMODA_CHAT" target="_blank" title="Связаться с нами в Telegram">Написать в Telegram</a>
      </div>
   </div>
   <div class="block">
      <h2>Номер телефона</h2>
      <p>
         <a href="tel:<?= PHONE ?>" class="black"><?= PHONE_FORMAT ?></a>
      </p>
      <p>
         Ежедневно с 10:00 до 22:00 по московскому времени<br>
      </p>
   </div>
   <div class="block">
      <h2>Почта</h2>
      <p>
         <a class="black" href="mailto:info@sodamoda.ru">info@sodamoda.ru</a>
      </p>
   </div>
   <div class="block">
      <h2>Социальные сети</h2>
      <!-- p>
      <a class="black hover" href="//instagram.com/sodamoda.ru/" target="_blank">INSTAGRAM</a>
		</p -->
      <p>
         <a class="black hover" href="https://t.me/+TYHDuzXCdg85YjFi" target="_blank">TELEGRAM КАНАЛ</a>
      </p>
   </div>
   <div class="block">
      <h2>Адреса магазинов</h2>
      <p>
         Россия, г. Москва, ул. Малая Бронная, д.&nbsp;19а<br><br>
         Россия, г. Москва, ТЦ Авиапарк, 3&nbsp;этаж, Trend Island<br><br>
         Россия, г. Москва, ТЦ&nbsp;Атриум, ул.&nbsp;Земляной вал&nbsp;33, 3&nbsp;этаж<br><br>
         Россия, г. Москва, ТРЦ Океания, Кутузовский проспект&nbsp;57, 2&nbsp;этаж<br><br>
         Россия, г. Ростов-на-Дону, ТЦ&nbsp;Галерея Астор, 2&nbsp;этаж, площадка Телеграф, <a href="tel:+79604703030" class="black">+7 960 470 3030</a><br><br>
         Россия, г. Санкт-Петербург, ул.&nbsp;Большая Конюшенная, д.&nbsp;1
      </p><br>
      <h2>Франчайзи</h2>
      <p>
         Азербайджан, г. Баку, ул. Академика Мирасадуллы Миргасимова&nbsp;13<br>
         Телефон: <a href="tel:+994507117181" class="black">+9 945 07117181</a><br>
         <br>
         Белоруссия, Минск, ул. Тимирязева 74а, ТЦ «Palazzo», 1 этаж<br>
         Телефон: +375 29 514 74 28<br>
         <br>
         Грузия, г. Тбилиси<br>
         Телефон: <a href="tel:+995568712599" class="black">+995 568 71 25 99</a><br>
         <br>
         Казахстан, г. Алматы, ул. Назарбаева&nbsp;223, ТРЦ «Алма-Ата», Блок&nbsp;2A<br>
         Телефон: <a href="tel:+77782340404" class="black">+7 778 234 0404</a><br>
         <br>
         ОАЭ, г. Дубай<br>
         Marina, Silverene tower, B, 2102<br>
         <br>
         Россия, г. Екатеринбург, ул. Малышева 71, Универмаг «Большой», 2 этаж<br>
         Телефон: <a href="tel:+79600614016" class="black">+7 960 061 40 16</a><br>
         <br>
         Россия, г. Казань, ул. Проспект Альберта Камалеева 32Б<br>
         Телефон: <a href="tel:+79600614016" class="black">+7 960 061 40 16</a><br>
         <br>
         <!-- Россия, г. Калининград, ул. Советский проспект 81 секция Б, шоурум 14<br>
         Россия, г. Калининград, Приморское кольцо 2, ТЦ &laquo;Балтия Молл&raquo;<br>
         Телефон: <a href="tel:+74012994977" class="black">+7 4012 99 49 77</a><br>
         <br -->
         Россия, г. Краснодар, ул. Красноармейская, 131<br>
         Телефон: <a href="tel:+79892753050" class="black">+7 989 275 30 50</a><br>
         <br>
         Россия, г. Тюмень, ул. Федюнинского 67, ТЦ «Остров», 2 этаж, напротив моста (центр)<br>
         Телефон: <a href="tel:+79326289182" class="black">+7 932 628 91 82</a><br>
         <br>
         Россия, г. Хабаровск, ул. Карла Маркса 76, НК Сити, бутик 419<br>
         Телефон: <a href="tel:+79244191313" class="black">+7 924 419 13 13</a><br>
      </p>
   </div>
   <div class="block">
      <h2>Реквизиты</h2>
      <p>
         ИП Боджгуа Д. В.<br>
         ИНН 770408144370 / ОГРНИП 319774600264310 <br>
         Юридический адрес: 121069, г. Москва, Новинский бульвар, д. 16
      </p>
   </div>
</div>
<br><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>