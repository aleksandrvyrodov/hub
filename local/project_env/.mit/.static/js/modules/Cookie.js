class Cookie {
  constructor(name) {
    this.name = name;
  }

  getCookie(x = false) {
    let matches = document.cookie.match(new RegExp(
      "(?:^|; )" + this.name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    if (x) {
      return matches ? decodeURIComponent(matches[1]) : undefined;
    } else {
      return matches ? JSON.parse(decodeURIComponent(matches[1])) : undefined;
    }
  }

  setCookie(value, options = {}) {
    options = {
      path: '/',
      'max-age': 60 * 60,
      samesite: "Strict",
      ...options
    };

    if (options.expires instanceof Date) {
      options.expires = options.expires.toUTCString();
    }

    let updatedCookie = encodeURIComponent(this.name) + "=" + encodeURIComponent(value);

    for (let optionKey in options) {
      updatedCookie += "; " + optionKey;
      let optionValue = options[optionKey];
      if (optionValue !== true) {
        updatedCookie += "=" + optionValue;
      }
    }

    document.cookie = updatedCookie;
  }

  deleteCookie(n = false) {
    if (n === false) {
      this.setCookie("", {
        'max-age': -1
      })
    } else {
      n = parseInt(n);
      if (!isNaN(n)) {
        let za = this.getCookie(),
          z = za.items;
        for (let i = 0; i < z.length; i++) {
          if (parseInt(z[i]._id) === n) {
            z.splice(i, 1);
            if (z.length) {
              this.setCookie(JSON.stringify(za));
            } else {
              this.setCookie("", {
                'max-age': -1
              })
            }
            break;
          }
        }
      } else {
        console.error("delete cookie item problem");
      }
    }

  }
}
