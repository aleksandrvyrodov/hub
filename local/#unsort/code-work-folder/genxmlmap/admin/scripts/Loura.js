class Tyrande {
    gen = document.getElementById('gen');
    reconnect_b = document.getElementById('reconnect');
    pre = document.getElementById('malfurion');
    counter = document.querySelector('.counter');
    response_code = 0;
    reconnect = false;
    interval;

    constructor() { }

    waitSpace() {
        if (this.interval) return true;

        this.pre.classList.remove('d-none');
        this.gen.remove();

        this.counter.innerText = '0s';
        this.counter.classList.remove('counter-hide');
        this.interval = setInterval(X, 100, this);

        function X(_this) {
            _this.counter.innerText = ((Number.parseFloat(_this.counter.innerText) * 1000 + 100) / 1000).toFixed(1) + 's';
        }
    }

    helloSpace() {
        this.counter.classList.add('counter-hide');
        clearInterval(this.interval);
        delete this.interval;
    }

    rules(obj) {
        if (obj.status == 'reconnect') this.reconnect = true;
        else this.reconnect = false;

        if (obj.mes) return obj.mes;
        else return '';
    }

    whatThisIsMes(chunk) {
        let mes = '';
        let chunk_arr = chunk.split('|');

        chunk_arr.forEach(el => {
            try {
                var obj = JSON.parse(el);
                mes += this.rules(obj);
            } catch (_) {
                mes += el;
            }
        });
        return mes;
    }

    extractBuf(text, gemstone) {
        text = gemstone == 'reconnect' ? this.pre.innerHTML + text : text;
        return text;
    }

    setMessArea() {
        let div = document.createElement('div');
        this.pre.append(div);
        return div;
    }

    scrollOrNotScroll() {
        return !this.counter.classList.contains('counter-hide');
    }

    getDataBySpace(gemstone = 'gen') {
        this.waitSpace();

        let _this = this;
        let data = new FormData();
        let mes = this.setMessArea();
        var url = `/ajax.enter.php`;

        data.append('method', `genxmlmap.gen.${gemstone}`);

        fetch(url, {
            method: 'POST',
            body: data
        }).then(response => {
            _this.response_code = _this.response_code == 502 ? 0 : response.status;

            let text = '',
                reader = response.body.getReader(),
                decoder = new TextDecoder();
            return readChunk();

            function readChunk() {
                return reader.read().then(appendChunks);
            }

            function appendChunks(result) {
                let chunk = decoder.decode(result.value || new Uint8Array, {
                    stream: !result.done
                });

                text += _this.whatThisIsMes(chunk);

                if (result.done) {
                    return text;
                } else {
                    mes.innerHTML = text;
                    if (_this.scrollOrNotScroll()) _this.pre.scrollTop = _this.pre.scrollHeight
                    return readChunk();
                }
            }
        }).then(result => {
            result = _this.whatThisIsMes(result);
            mes.innerHTML = result;
            if (_this.scrollOrNotScroll()) _this.pre.scrollTop = _this.pre.scrollHeight;
            if (_this.reconnect) {
                _this.reconnect = false;
                _this.getDataBySpace('reconnect');
            } else {
                if (_this.response_code == 502) {
                    if (_this.reconnect_b.classList.contains("d-none"))
                        _this.reconnect_b.classList.remove("d-none");
                    _this.getDataBySpace('reconnect');
                } else {
                    _this.helloSpace();
                }
            }
        });
    }
}


let Empire = new Tyrande();

Empire.gen.addEventListener('click', e => Empire.getDataBySpace());
Empire.reconnect_b.addEventListener('click', e => Empire.getDataBySpace('reconnect'));