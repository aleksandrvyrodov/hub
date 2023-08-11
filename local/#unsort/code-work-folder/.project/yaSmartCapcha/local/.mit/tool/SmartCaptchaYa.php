<?php

namespace MIT\Tool;

final class SmartCaptchaYa
{

  const SMARTCAPTCHA_SERVER_KEY = 'ysc2_PzMmkbM0qk62zi13Jv1nVlYQNvkA2UzRdXrWDOybce3046cd';
  const SMARTCAPTCHA_CLIENT_KEY = 'ysc1_PzMmkbM0qk62zi13Jv1ns23F4JznqgwUHkh78TRKade2ff5f';
  const SELECTOR_SUBMIT = '.order-prod__submit';


  private function __construct()
  {
  }

  static public function Check(string $token): bool
  {
    $ch = curl_init();
    $args = http_build_query([
      "secret" => self::SMARTCAPTCHA_SERVER_KEY,
      "token" => $token,
      "ip" => $_SERVER['REMOTE_ADDR'], // Нужно передать IP-адрес пользователя.
      //                                  Способ получения IP-адреса пользователя зависит от вашего прокси.
    ]);
    curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/validate?$args");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
      if ($_COOKIE['MIT'] === 'dev') {
        echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
        exit();
      }
      return false;
    }

    $resp = json_decode($server_output);

    return ($resp->status === "ok");
  }

  static public function Mount(string $id)
  {
    $site_key = self::SMARTCAPTCHA_CLIENT_KEY;
    $selector_submit = self::SELECTOR_SUBMIT;

    return <<<HTML
      <div class="smart-captcha--mit" id="$id">
        <style>
          .smart-captcha { margin-top: 35px; }
          .smart-captcha iframe{ height: initial !important; }
        </style>
        <script src="https://smartcaptcha.yandexcloud.net/captcha.js?render=onload&onload=smartCaptchaInit" defer></script>
        <div class="smart-captcha"></div>
        <script>
          ((w, d, c) => {
            // w.addEventListener('DOMContentLoaded', e=>{alert();
              w.smartCaptchaInit = function () {
                if (!w.smartCaptcha)
                  return;

                let
                  point = d.querySelector('#$id')
                  form = point.closest('form')
                  container = point.querySelector('.smart-captcha')

                  form
                    .querySelector('$selector_submit')
                    .setAttribute('disabled', '1');

                w.smartCaptcha.render(container, {
                  sitekey: '$site_key',
                  callback: function (token) {
                    if (token)
                      form
                        .querySelector('$selector_submit')
                        .removeAttribute('disabled');
                    else
                      form
                        .querySelector('$selector_submit')
                        .setAttribute('disabled', '1');

                  },
                });
              };

              w.smartCaptchaReset = function () {
                if (!w.smartCaptcha)
                  return;

                w.smartCaptcha.reset();
              };

              w.smartCaptchaGetResponse = function () {
                if (!w.smartCaptcha)
                  return;

                var resp = w.smartCaptcha.getResponse();
              };

            // });
          })(window, document, document.currentScript);
        </script>

      </div>
      HTML;
  }
}


/*
  if(isset($_POST['smart-token'])){
      require_once __DIR__ . '/../.mit/tool/SmartCaptchaYa.php';
      if(!SmartCaptchaYa::Check($_POST['smart-token']))
*/
