<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация");
?>

<div class="section-auth">
  <div class="col">
    <h1>Войти</h1>
    <div class="form">
      <div class="socials">
        <a class="ibutton hollow soc-ins"></a>
        <a class="ibutton hollow soc-vk"></a>
        <a class="ibutton hollow soc-fb"></a>
        <a class="ibutton hollow soc-phone">По номеру телефона</a>
      </div>
      <div class="split-or">или</div>
      <div class="description">Введите ниже адрес электронной почты и&nbsp;пароль для входа в&nbsp;личный кабинет</div>
      <form action="">
        <div class="field">
          <input type="text" placeholder="Адрес электронной почты" size="40" name="" value="" aria-required="true" required="">    
        </div>
        <div class="field">
          <input type="password" placeholder="Пароль" size="40" name="" value="" aria-required="true" required="">    
        </div>
        <div class="wrap-buttons-between flex-end">
          <a class="black" href="">Забыли пароль?</a>
          <button class="ibutton" type="submit" name="">Войти</button>
        </div>
      </form>
    </div>
  </div>
  <div class="col">
    <h1>Регистрация</h1>
    <br>
    <div class="form">
      <div class="description">Пожалуйста, зарегистрируйтесь, чтобы создать учетную запись</div>
      <form action="">
        <div class="field">
          <input type="text" placeholder="Адрес электронной почты" size="40" name="" value="" aria-required="true" required="">    
        </div>
        <div class="field">
          <input type="password" placeholder="Пароль" name="" value="" aria-required="true" required="">    
        </div>
        <div class="field">
          <input type="password" placeholder="Повторите пароль" name="" value="" aria-required="true" required="">    
        </div>
        <div class="field">
          <input type="text" placeholder="Имя" size="40" name="" value="" aria-required="true" required="">    
        </div>
        <div class="field">
          <input type="text" placeholder="Фамилия" size="40" name="" value="">    
        </div>
        <div class="field">
          <input type="text" placeholder="Телефон" size="40" name="" value="" aria-required="true" required="">    
        </div>
        <div class="field field-checkbox">
          <label> 
            <input type="checkbox" checked="checked" value="Y" name="">
            <div class="label">Я хочу получать информацию о новинках SODAMODA на мой E-mail</div>
          </label>
        </div>
        <div class="field field-checkbox">
          <label> 
            <input type="checkbox" checked="checked" value="Y" name="">
            <div class="label">Cогласен (согласна) с <a href="/rule/" target="_blank" class="black">Политикой конфиденциальности</a></div>
          </label>
        </div>
        <div class="wrap-buttons-between flex-end">
          <span class="false">.</span>
          <button class="ibutton" type="submit" name="">Зарегистрироваться</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>