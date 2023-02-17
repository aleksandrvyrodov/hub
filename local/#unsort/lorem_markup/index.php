<style>
  .loremipsium {
    --ed: 1rem;
    --cf: 1.5;
    background-color: #fef4ab;
    padding: var(--ed) calc(var(--ed) * var(--cf));
  }

  .loremipsium:before {
    content: 'Текст - заполнитель [' attr(x-path) ']';
    display: block;
    display: inline-block;
    font-family: monospace;
    background-color: #555;
    color: #fef4ab;
    position: relative;
    top: calc(var(--ed) * -1);
    left: calc(var(--ed) * var(--cf) * -1);
    padding-right: calc(var(--ed) * var(--cf));
    padding-left: calc(var(--ed) * var(--cf));
  }
</style>

<div class="loremipsium" x-path=<?= parse_url($_SERVER['REQUEST_URI'])['path']; ?>>

</div>