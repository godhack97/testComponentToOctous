
'use strict';

function logText(message, isError) {
  //alert(message);
  if (isError)
    console.log(message);
  else
    console.log(message);
}

function logError(message) {
  logText(message, true);
}

async function cartWebShare(link) {
  
  //console.log('cartWebShare '+ link);
  
  if (navigator.share === undefined) {
    logError('Error: Unsupported feature: navigator.share()');
    return;
  }

  //const title_input = 'Товар';
  //const text_input = 'Название товара';
  //const url_input = "https://example.com"; //document.querySelector('#url');
  //const file_input = document.querySelector('#files');

  const title = $('title').text(); //title_input.disabled ? undefined : title_input.value;
  const text = $('title').text(); //text_input.disabled ? undefined : text_input.value;
  const url = link; //location.href; //"https://sodamoda.ru"; //url_input.disabled ? undefined : url_input.value; // location.href
  const files = undefined; //file_input.disabled ? undefined : file_input.files;

  if (files && files.length > 0) {
    if (!navigator.canShare || !navigator.canShare({files})) {
      logError('Error: Unsupported feature: navigator.canShare()');
      return;
    }
  }

  try {
    await navigator.share({files, title, text, url});
    logText('Successfully sent share');
  } catch (error) {
    logError('Error sharing: ' + error);
  }
  
}

//function onLoad() {
//
//  document.querySelector('#share-mobile').addEventListener('click', cartWebShare);
//
//  if (navigator.share === undefined) {
//    if (window.location.protocol === 'http:') {
//      // navigator.share() is only available in secure contexts.
//      window.location.replace(window.location.href.replace(/^http:/, 'https:'));
//    } else {
//      logError('Error: You need to use a browser that supports this draft ' +
//               'proposal.');
//    }
//  }
//}
//
//window.addEventListener('load', onLoad);