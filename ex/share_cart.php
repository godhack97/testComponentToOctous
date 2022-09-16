<!-- This work is licensed under the W3C Software and Document License
     (http://www.w3.org/Consortium/Legal/2015/copyright-software-and-document).
  -->
<html>
<head>
  <script type="text/javascript" src="//code.jquery.com/jquery-3.3.1.min.js"></script>
  <title>Web Share Test</title>
  <meta name="viewport" content="width=device-width">
</head>
<body>
  

  <img id="share" src="/local/templates/sodamoda/images/icons/icon-share-ios.png" width="20" />
  
<!--  <input id="share-no-gesture" type="button" value="Share without user gesture" />-->
  
  <div id="output"></div>
  
  <script>

    async function cartWebShare(link) {
      
      console.log('cartWebShare '+ link);
      
      if (navigator.share === undefined) {
        logError('Error: Unsupported feature: navigator.share()');
        return;
      }

      //var title_input = 'Товар';
      //var text_input = 'Название товара';
      //var url_input = "https://example.com"; //document.querySelector('#url');
      //var file_input = document.querySelector('#files');

      var title = 'document.title'; //title_input.disabled ? undefined : title_input.value;
      var text = 'Блузка'; //text_input.disabled ? undefined : text_input.value;
      var url = link; //location.href; //"https://sodamoda.ru"; //url_input.disabled ? undefined : url_input.value; // location.href
      var files = undefined; //file_input.disabled ? undefined : file_input.files;

      if (files && files.length > 0) {
        if (!navigator.canShare || !navigator.canShare({files})) {
          console.log('Error: Unsupported feature: navigator.canShare()');
          return;
        }
      }

      try {
        await navigator.share({files, title, text, url});
        console.log('Successfully sent share');
      } catch (error) {
        console.log('Error sharing: ' + error);
      }
    }

//    function onLoad() {
//
//      document.querySelector('#share').addEventListener('click', testWebShare);
//
//      if (navigator.share === undefined) {
//        if (window.location.protocol === 'http:') {
//          // navigator.share() is only available in secure contexts.
//          window.location.replace(window.location.href.replace(/^http:/, 'https:'));
//        } else {
//          logError('Error: You need to use a browser that supports this draft ' +
//                   'proposal.');
//        }
//      }
//    }
//    window.addEventListener('load', onLoad);

    $('#share').click(function(){
      
      var link = window.location.origin + window.location.pathname + '?b=';
      cartWebShare(link);
      
    })
  </script>
</body>
</html>
