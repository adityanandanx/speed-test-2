document.addEventListener("DOMContentLoaded", (e) => {
  /**@type {HTMLInputElement} */
  const inp = document.querySelector("#pass");
  const show = document.querySelector("#show");

  let pass = "";
  let mask = "";
  let hidden = true;

  show.addEventListener("click", (e) => {
    hidden = !hidden;

    if (hidden) {
      show.textContent = "Show";
      inp.value = mask;
    } else {
      show.textContent = "Hide";
      inp.value = pass;
    }
  });

  const getRandom = () => {
    const pool = "!@#$%^&*()_+";
    return pool.charAt(Math.floor(Math.random() * pool.length));
  };

  inp.addEventListener("keydown", (e) => {
    if (["ArrowLeft", "Home", "End", "ArrowRight", "Tab"].includes(e.key)) {
      return;
    }

    e.preventDefault();

    let start = inp.selectionStart;
    let end = inp.selectionEnd;

    if (e.key === "Backspace") {
      if (start === end && start !== 0) {
        pass = pass.slice(0, start - 1) + pass.slice(start);
        mask = mask.slice(0, start - 1) + mask.slice(start);
        start--;
      } else if (start !== end) {
        pass = pass.slice(0, start) + pass.slice(end);
        mask = mask.slice(0, start) + mask.slice(end);
      }
    } else if (e.key === "Delete") {
      if (start === end && start !== 0) {
        pass = pass.slice(0, start) + pass.slice(start + 1);
        mask = mask.slice(0, start) + mask.slice(start + 1);
      } else if (start !== end) {
        pass = pass.slice(0, start) + pass.slice(end);
        mask = mask.slice(0, start) + mask.slice(end);
      }
    } else if (e.key.length === 1 && (!e.ctrlKey || !e.metaKey)) {
      pass = pass.slice(0, start) + e.key + pass.slice(end);
      mask = mask.slice(0, start) + getRandom() + mask.slice(end);
      start++;
    }

    if (hidden) {
      inp.value = mask;
    } else {
      inp.value = pass;
    }
    console.log({ pass, mask });
    inp.setSelectionRange(start, start);
  });
});
