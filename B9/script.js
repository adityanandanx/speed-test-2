document.addEventListener("DOMContentLoaded", (e) => {
  const inp = document.querySelector("#pass");
  const mask = document.querySelector("#mask");
  let pass = "";
  let random = "";
  const pool = "!@#$%^&*()_+";

  inp.addEventListener("input", (e) => {
    random += pool.at(Math.floor(Math.random() * (pool.length - 1)));
    pass = e.target.value;

    random = random.slice(0, pass.length);
    mask.value = random;
  });
});
