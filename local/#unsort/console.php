<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['code'])) {
    @include_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

    header('Content-type: text/plain');
    echo eval(preg_replace('/^<\?(php)?/i','',$_POST['code']));
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
      grid-template-rows: 1fr;
    }
  </style>
</head>

<body>
  <form action="" method="post" target="run" id="run">
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

      window.onkeydown = e => run(e, editor);
      window.onkeypress = e => run(e, editor);

      frame.onload = (e) => {
        result.setValue(frame.contentDocument.body.innerText);
      }

    });

    function run(e, editor) {

      if (e.keyCode === 116 && !e.ctrlKey) {
        e.preventDefault();
        code.value = editor.getValue();
        form.submit();
      }
    }

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