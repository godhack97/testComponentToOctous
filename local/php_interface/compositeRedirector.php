<?php
  
  function setCurrencyCookie1($currencyCode)
  {
    setcookie("CATALOG_CURRENCY", $currencyCode, strtotime('+180 days'), "/");
  }
function setLanguageCookie($languageCode)
{
    setcookie("MANUAL_LANGUAGE", $languageCode, strtotime('+180 days'), "/");
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function RedirectByCountry()
{
    $currentPage = $_SERVER["REQUEST_URI"];
    $currentLanguage = $_COOKIE["MANUAL_LANGUAGE"];
    $currentCurrency = $_COOKIE["CATALOG_CURRENCY"];

    $isAdminSection = strpos($currentPage, '/bitrix/') !== false;
    $CUSTOM_SITE_ID = strpos($currentPage, '/en/') !== false ? "s2" : "s1";

    if (!$isAdminSection) {
        //если установили язык через гет-параметр, то считаем, что уже находимся на нужном сайте
        if (in_array($_REQUEST["language"], ["RU", "EN"])) {
            setLanguageCookie($_REQUEST["language"]);
        }
        else if ($currentLanguage) {
            if ($currentLanguage === "RU" && $CUSTOM_SITE_ID !== "s1") {
                $_COOKIE['AUTO_REDIRECT_BY_COUNTRY'] = true;
                $redirectPage = preg_replace('/\/en/', '', $currentPage, 1);
                redirect($redirectPage);
            }

            if ($currentLanguage === "EN" && $CUSTOM_SITE_ID !== "s2") {
                $_COOKIE['AUTO_REDIRECT_BY_COUNTRY'] = true;
                redirect("/en" . $currentPage);
            }
        }
				
	    /*
			if (!$currentLanguage) {
					$language = explode(";", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
					//массив кодов русскоговорящих стран
					$arRusCountryCodes = ["AZ", "AM", "BY", "KG", "KZ", "RU", "TM", "TJ", "UA"];
					$isRussian = false;
					foreach ($arRusCountryCodes as $countryCode) {
							if (stripos($language[0], $countryCode) !== FALSE) {
									$isRussian = true;
									setLanguageCookie("RU");
									!empty($currentCurrency) ? setCurrencyCookie($currentCurrency) : setCurrencyCookie("rub");
									break;
							}
					}

					if (!$isRussian) {
							if ($CUSTOM_SITE_ID !== "s2") {
									setLanguageCookie("EN");
									!empty($currentCurrency) ? setCurrencyCookie($currentCurrency) : setCurrencyCookie("usd");
									redirect("/en" . $currentPage);
							}
					}
			}
			 */
    }
    if(isset($_REQUEST['currency']) && !empty($_REQUEST['currency'])){
      $_COOKIE['AUTO_REDIRECT_BY_COUNTRY'] = true;
	    setCurrencyCookie1(strtoupper($_REQUEST['currency']));
      //redirect($currentPage);
    }
}

RedirectByCountry();