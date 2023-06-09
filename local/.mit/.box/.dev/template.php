<!DOCTYPE html>
<html>

<head>
  <title>[DevArea]</title>
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
    }

    body {
      min-height: 100%;
      background-color: #2b2a32;
      padding: 2rem;
      box-sizing: border-box;
      height: initial;
      display: grid;
      grid-auto-flow: row;
      grid-template-rows: auto;
      grid-auto-rows: 1fr;
      gap: 2rem;
      width: 100%;
    }

    pre {
      font-family: 'Fira Code';
      font-size: 0.8rem;
      margin: 0;
      background-color: #fef4ac;
      min-height: 100%;
      padding: 1rem;
      box-sizing: border-box;
      border-radius: 0.25rem;
      white-space: pre-wrap;
    }
    pre:empty{
      display: none;
    }
    .fence{
      background-color: #2b2a32;
      display: grid;
      grid-auto-flow: column;
      grid-auto-columns: 1fr;
      gap: 1rem;
    }
  </style>
</head>

<body>
  <pre><? include $include; ?></pre>
  <? if ($data_list = out_split(return: true)) : ?>
    <div class="fence">
      <? foreach ($data_list as $data) : ?>
        <pre><?= $data; ?></pre>
      <? endforeach; ?>
    </div>
  <? endif; ?>
</body>

</html>