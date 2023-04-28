(function (w, d) {
    class CookieLT {
        constructor(name) {
            this.name = name;
        }

        getCookie() {
            let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + this.name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? JSON.parse(decodeURIComponent(matches[1])) : undefined;
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
    }

    let cookie = new CookieLT('COOKIE-AGREEMENT');
    if (cookie.getCookie() !== true) {
        d.cleateElements;
        let el = d.createElement('div');
        el.id = el.className = 'cookie-agreement';
        el.innerHTML = `<span>Этот сайт использует cookie-файлы для более комфортной работы пользователя. Продолжая просматривать сайт, Вы соглашаетесь на использование cookie.</span>`;
        let st = d.createElement('style');
        st.class = 'cookie-agreement';
        st.innerText = `.cookie-agreement{ position: fixed; display: flex; box-sizing: border-box; justify-content: space-between; align-items: center; background-color: rgba(0, 0, 0, 0.8); padding: 20px 2%; bottom: 0px; z-index: 1000000000; width: 100%;} .cookie-agreement span{ color: white; margin-right: 10px;} .cookie-agreement span{ color: white;}`;
        let bt = d.createElement('button');
        bt.className = 'button';
        bt.innerText = 'OK';
        bt.onclick = function () {
            cookie.setCookie(true, {});
            el.remove();
            st.remove();
        }
        el.append(bt);
        d.body.append(el);
        d.body.append(st);
    }
}(window, document));