class Sender {
  controller_fetch;
  timeOutObj = null;
  time = 1;
  options = {};
  url = {};
  onceObserver = {
    success: () => { },
    error: () => { }
  }

  constructor(url = '/.mit/ajax/', options = {}) {
    this.options = options;
    this.url = url;
    this.controller_fetch = new AbortController();

    this.onceObserver = new Proxy(this.onceObserver, {
      get: (target, prop, receiver) => {
        return () => {
          target[prop]()
          target[prop] = () => { };
        };
      }
    });
  }

  _stackcontrol(ms) {
    this.controller_fetch.abort();
    this.controller_fetch = new AbortController();

    if (this.timeOutObj !== null) {
      this.timeOutObj.cancel();
    }

    this.timeOutObj = function (ms) {
      let cancel, timeout, promise;
      promise = new Promise(
        (resolve, reject) => {
          timeout = setTimeout(function () {
            resolve();
          }, ms);
          cancel = () => {
            clearTimeout(timeout);
            reject(timeout);
          }
        });

      return {
        promise: promise,
        cancel: cancel
      };
    }(ms);

    this.timeOutObj.promise.catch(e => {
      console.log('out');
    });
  }

  load(data, success = (result, data) => { console.log(result) }, error = (err, data) => { console.error(err) }) {
    this._stackcontrol(this.time);

    data = JSON.stringify(data);

    fetch(this.url, {
      method: "POST",
      body: data,
      signal: this.controller_fetch.signal,
      ...this.options
    })
      .then((response) => {
        if (response.status === 200)
          return response.json();
        else
          throw new Error("ERR-167: Ошибка соединения с сервером!");
      })
      .then(
        (result) => {
          this.timeOutObj.promise.then(
            () => {
              success(result, data);
              this.onceObserver.success(result);
            },
          );
        },
        (err) => {
          error(err);
          this.onceObserver.error(err);
        }
      );

    return this.onceObserver;
  }
}

export default Sender;