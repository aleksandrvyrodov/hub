const log = (out) => console.log('fn ', out);
document.addEventListener('wakeup', e => console.log('event ', e.detail.log));

/* INIT -------------------------*/

console.log('line ', 1);

log(2)

setTimeout(
  () => console.log('timeout ', 3),
  0
);

(new Promise((rs, rj) => rs(4)))
  .then(
    rs => console.log('resolve ', rs)
  )

document.dispatchEvent(new CustomEvent('wakeup', {
  detail: { log: 5 }
}));

/* STEP -------------------------*/

console.log('line ', 6);

log(7)

setTimeout(
  () => console.log('timeout ', 8),
  0
);

(new Promise((rs, rj) => rs(9)))
  .then(
    rs => console.log('resolve ', rs)
  )

document.dispatchEvent(new CustomEvent('wakeup', {
  detail: { log: 10 }
}));

console.log('line ', 11);