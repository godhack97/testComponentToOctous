<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("CONTACTS");
?><h1>CONTACTS</h1>
<br>
<div class="contacts-sections">
   <div class="block block-chat">
      <div class="cols">
         <h2>Chat/WhatsApp/Telegram</h2>
         <p>
            Daily from 10:00 to 22:00 UTC+3<br>
         </p>
         <span data-comment="span здесь нужен для отступа до кнопок"></span>
      </div>
      <div class="cols">
         <button type="button" class="ibutton hollow js-open-chat">FeedBack</button>
         <a class="ibutton hollow js-metrika-order-whatsapp" href="whatsapp://send?phone=<?= PHONE_WHATSAPP ?>" target="_blank" title="WhatsApp">Whatsapp</a>
         <a class="ibutton hollow" href="https://t.me/SODAMODA_CHAT" target="_blank" title="Telegram">Telegram</a>
      </div>
   </div>
   <div class="block">
      <h2>Phone number</h2>
      <p>
         <a href="tel:+78003505670" class="black">+7 800 350-56-70</a>
      </p>
      <p>
         Daily from 10:00 to 22:00 UTC+3<br>
      </p>
   </div>
   <div class="block">
      <h2>E-mail</h2>
      <p>
         <a class="black" href="mailto:info@sodamoda.ru">info@sodamoda.ru</a>
      </p>
   </div>
   <div class="block">
      <h2>Social media</h2>
      <!-- p>
      <a class="black hover" href="//instagram.com/sodamoda.ru/" target="_blank">INSTAGRAM</a>
		</p -->
      <p>
         <a class="black hover" href="https://t.me/+TYHDuzXCdg85YjFi" target="_blank">TELEGRAM CHANNEL</a>
      </p>
   </div>
   <div class="block">
      <h2>Address</h2>
      <p>
         Russia, Moscow, «Aviapark» mall, 3rd floor, Trend Island<br><br>
         Russia, Moscow, «Atrium» mall, Zemlyanoy Val st. 33, 3rd floor<br><br>
         Russia, Moscow, «Oceania» mall, Kutuzovsky Boulevard st. 57, 2nd floor<br><br>
         Russia, Moscow, Malaya Bronnaya st.&nbsp;19a<br><br>
         Russia, Rostov-on-Don, Gallery «Astor» mall, 2nd floor, Telegraph area, <a href="tel:+79604703030" class="black">+7 960 470 3030</a><br><br>
         Russia, Saint Petersburg, Bolshaya Konyushennaya st.&nbsp;1<br><br>
      </p>
   </div>
   <div class="block">
      <h2>Our franchisees</h2>
      <p>
         Azerbaijan, Baku, Academician Mirasadulla Mirgasimov st. 13<br />
         Phone: <a href="tel:+994507117181" class="black">+994 507 11 71 81</a><br />
         <br />
         Belarus, Minsk, Timiryazeva st. 74a, «Palazzo» mall, 1st floor<br />
         Phone: +375 29 514 74 28<br />
         <br />
         Georgia, Tbilisi<br />
         Phone: <a href="tel:+995568712599" class="black">+995 568 71 25 99</a><br />
         <br />
         Kazakhstan, Almaty, Nazarbayev st. 223, shopping center «Alma-Ata», Block 2A<br />
         Phone: <a href="tel:+77782340404" class="black">+7 778 234 04 04</a><br />
         <br />
         <!-- Russia, Kaliningrad, Primorskoe koltso 2, «Baltiya» mall<br />
         Phone: <a href="tel:+74012994977" class="black">+7 4012 99 49 77</a><br />
         <br />
         Russia, Kaliningrad, Sovetsky prospekt st. 81 section B, showroom 14<br />
         Phone: <a href="tel:+74012994977" class="black">+7 4012 99 49 77</a><br />
         <br / -->
         Russia, Kazan, 32B Albert Kamaleyev Avenue<br />
         Phone: <a href="tel:+79600614016" class="black">+7 960 061 40 16</a><br />
         <br />
         Russia, Khabarovsk, Karl Marx st. 76, NK Siti, boutique 419<br />
         Phone: <a href="tel:+79244191313" class="black">+7 924 419 13 13</a><br />
         <br />
         Russia, Krasnodar, Krasnoarmeyskaya st. 131<br />
         Phone: <a href="tel:+79892753050" class="black">+7 989 275 30 50</a><br />
         <br />
         Russia, Tyumen, Fedyuninsky st. 67, «Ostrov» mall, 2nd floor<br />
         Phone: <a href="tel:+79326289182" class="black">+7 932 628 91 82</a><br />
         <br />
         Russia, Yekaterinburg, Malysheva st. 71, «Bolshoy» mall, 2nd floor<br />
         Phone: <a href="tel:+79600614016" class="black">+7 960 061 40 16</a><br />
         <br />
         UAE, Dubai<br />
         Marina, Silverene tower, B, 2102<br />
      </p>
   </div>
   <div class="block">
      <h2>Bank details</h2>
      <p>
         IP BODZHGUA D. V.<br>
         TIN 770408144370 / PSRNSP 319774600264310<br>
         Legal address: 16 Novinsky Boulevard, Moscow, 121069
      </p>
   </div>
</div>
<br><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>