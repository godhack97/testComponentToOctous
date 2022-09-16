window.toKitFavs = {
  curFavs: [],
  buttonParams: {
    title: {
      inFav: null,
      notFav: null,
      className: 'o-fav-btn'
    }
  },
  isAuhorize: false,
  params: null,
  elemItems: [],
  jsEvent: null,

  init(params) {
    if(typeof params === 'object') {
      this.params = params;
      this.initFavorites();
      BX.ready(BX.delegate(this.prepareBtns,this));
      document.addEventListener("DOMContentLoaded", this.bxInit.bind(this));
    }
  },

  //custom
  reinit(params) {
    if(typeof params === 'object') {
      this.params = params;

      this.initFavorites();
      this.prepareBtns();
      BX.delegate(this.prepareBtns,this);
      this.bxInit()
    }
  },
  changeProduct(elem) {
    this.initFavorites();
    this.prepareBtns();
  },
  //custom//

  initFavorites() {
    if(typeof this.params.BUTTON === 'object') {
      this.buttonParams = this.params.BUTTON;
    }

    this.isAuhorize = this.params.AUTHORIZED;

    var lsFavs = this.getCookie(this.params.COOKIE_NAME) ? JSON.parse(this.getCookie(this.params.COOKIE_NAME)) : null;
    if(this.isAuhorize) {
      this.curFavs = this.params.CUR_IN_FAV;

      if(lsFavs) {
        this.syncFavDiff(lsFavs.filter(x => !this.curFavs.includes(x)));
      }
    } else {
      this.curFavs = lsFavs;
    }
  },

  prepareBtns() {
    this.elemItems = document.querySelectorAll('.'+this.buttonParams.CLASS);


    if(this.curFavs) {
      for(let i = 0; i < this.elemItems.length; i++) {
        if(this.includes(this.elemItems[i].dataset.id, this.curFavs)) {
          this.elemItems[i].classList.add(this.buttonParams.CLASS_ACTIVE);
        }
        else {
          this.elemItems[i].classList.remove(this.buttonParams.CLASS_ACTIVE);
        }
      }

      setTimeout(()=>{
        document.dispatchEvent(new CustomEvent("favupdate", { detail: {
            COUNT: this.curFavs.length
          }}));
      }, 0);
    }
  },

  bxInit() {
    let j = 0;
    let strPrefix = '';
    let params = this.params;
    let _this = this;
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === "attributes" && mutation.attributeName === 'data-id') {
          _this.changeProduct(mutation.target)
        }
      });
    });
    for(let i = 0; i < this.elemItems.length; i++) {
      this.elemItems[i].addEventListener('click', this.toggleFav.bind(this));
      observer.observe(this.elemItems[i], {
        attributes: true //configure it to listen to attribute changes
      });
    }
  },

  syncFavDiff(arDiff) {
    if(this.isAuhorize) {
      this.httpRequest('?AJAX_FAVORITES_CALL=Y', 'POST', {
        TOKIT_DIFFS: JSON.stringify(Array.isArray(arDiff) ? arDiff : [arDiff])
      }, (data) => {
        const response = JSON.parse(data.response);
        this.deleteCookie(this.params.COOKIE_NAME);
      })
    }
  },

  toggleFav(e) {
    let elem = e.target || e.srcElement;
    if(!elem.classList.contains('.'+this.buttonParams.CLASS)) {
      elem = elem.closest('.'+this.buttonParams.CLASS);
    }

    if(elem.classList.contains(this.buttonParams.CLASS_ACTIVE)) {
      elem.classList.remove(this.buttonParams.CLASS_ACTIVE);
    } else {
      elem.classList.add(this.buttonParams.CLASS_ACTIVE);
    }


    if(this.isAuhorize) {
      elem.classList.add('is-load');
      let url = '?AJAX_FAVORITES_CALL=Y';
      if(elem.dataset.url.length > 0){
        url = elem.dataset.url
      }
      this.httpRequest(url, 'POST', {
        TOKIT_ELEM_ID: elem.dataset.id,
        TOGGLE_FAV: 'Y'
      }, (data) => {
        const response = JSON.parse(data.response);

        document.dispatchEvent(new CustomEvent("favupdate", { detail: response} ));

        elem.classList.remove('is-load');

        if(response.ADD) {
          elem.classList.add(this.buttonParams.CLASS_ACTIVE);
        } else if(response.DELETE) {
          elem.classList.remove(this.buttonParams.CLASS_ACTIVE);
        }
      })
    } else {
      var curFavs = this.getCookie(this.params.COOKIE_NAME);

      if(curFavs) {
        var arFavs = JSON.parse(curFavs);
        if(this.includes(elem.dataset.id, arFavs)) {
          arFavs = this.remove(elem.dataset.id, arFavs);
        } else {
          arFavs.push(elem.dataset.id);
        }
        this.setCookie(this.params.COOKIE_NAME, JSON.stringify(arFavs), {
          path: '/'
        })

        console.log('Count', arFavs);
        document.dispatchEvent(new CustomEvent("favupdate", { detail: {
            COUNT: arFavs.length
          }}));
      } else {
        curFavs = JSON.stringify([elem.dataset.id]);
        this.setCookie(this.params.COOKIE_NAME, curFavs, {
          path: '/'
        })

        console.log('Count cur', curFavs);
        document.dispatchEvent(new CustomEvent("favupdate", { detail: {
            COUNT: JSON.parse(curFavs).length
          }}));
      }
    }
  },

  httpRequest(url, method, data, success_callback, failure_callback) {
    var xhr;
    var data2 = [];
    if (typeof data == 'object') {
      for(var index in data) {
        if (data.hasOwnProperty(index)) {
          data2[data2.length] =index+'='+data[index];
        }
      }
      data = data2.join("&");
    }
    if(typeof XMLHttpRequest !== 'undefined') {
      xhr = new XMLHttpRequest();
    } else {
      var versions = ["MSXML2.XmlHttp.5.0",
        "MSXML2.XmlHttp.4.0",
        "MSXML2.XmlHttp.3.0",
        "MSXML2.XmlHttp.2.0",
        "Microsoft.XmlHttp"]
      for(var i = 0, len = versions.length; i < len; i++) {
        try {
          xhr = new ActiveXObject(versions[i]);
          break;
        }
        catch(e){}
      }
    }
    var ua = navigator.userAgent.toLowerCase()
    if (ua.indexOf("msie") != -1 && ua.indexOf("opera") == -1 && ua.indexOf("webtv") == -1) {
      xhr.onreadystatechange = ensureReadiness;
    } else {
      xhr.onload = function (e) {
        if (this.status == 200) {
          success_callback(xhr);
        } else {
          failure_callback(xhr);
        }
      };
    }
    function ensureReadiness() {
      if(xhr.readyState < 4) {
        return;
      }
      if(xhr.status !== 200) {
        return;
      }
      if(xhr.readyState === 4) {
        if(xhr.status !== 200) {
          failure_callback(xhr);
        } else {
          success_callback(xhr);
        }
      }
    }
    if (method == 'post' || method == 'POST') {
      method = 'POST';
    } else {
      method = 'GET';
    }
    xhr.open(method, url, true);
    if (method == 'POST') {
      xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    }
    xhr.send(data);
  },

  includes(val, arr) {
    return arr.indexOf(val) >= 0;
  },

  remove(val, arr) {
    return arr.filter(function(elem){
      return elem != val;
    });
  },

  arrayUnique(arr) {
    var a = arr.concat();
    for(var i=0; i<a.length; ++i) {
      for(var j=i+1; j<a.length; ++j) {
        if(a[i] === a[j])
          a.splice(j--, 1);
      }
    }
    return a;
  },

  /* Cookies Functions */
  getCookie(name) {
    var matches = document.cookie.match(new RegExp(
      "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ))
    return matches ? decodeURIComponent(matches[1]) : undefined
  },
  setCookie(name, value, props) {
    props = props || {}
    var exp = props.expires
    if (typeof exp == "number" && exp) {
      var d = new Date()
      d.setTime(d.getTime() + exp*1000)
      exp = props.expires = d
    }
    if(exp && exp.toUTCString) { props.expires = exp.toUTCString() }
    value = encodeURIComponent(value)
    var updatedCookie = name + "=" + value
    for(var propName in props){
      updatedCookie += "; " + propName
      var propValue = props[propName]
      if(propValue !== true){ updatedCookie += "=" + propValue }
    }
    document.cookie = updatedCookie
  },
  deleteCookie(name) {
    this.setCookie(name, null, { expires: -1 })
  }
}