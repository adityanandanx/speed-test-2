document.addEventListener("DOMContentLoaded", (e) => {
  const inp = document.querySelector("input");
  const chars = document.querySelector(".chars");
  const shuffle = document.querySelector("#shuffle");
  let val = "";
  inp.addEventListener("input", (e) => {
    val = e.target.value;
    chars.innerHTML = "";
    for (const ch of val) {
      const char = document.createElement("div");
      char.classList.add("char");
      char.innerText = ch;
      chars.appendChild(char);
    }
  });
  shuffle.addEventListener("click", (e) => {
    const unshuffled = val.split("");
    let shuffled = "";
    while (unshuffled.length) {
      shuffled += unshuffled.splice(
        Math.floor(Math.random() * unshuffled.length),
        1
      );
    }
    console.log(shuffled);

    chars.innerHTML = "";
    for (const ch of shuffled) {
      const char = document.createElement("div");
      char.classList.add("char");
      char.innerText = ch;
      chars.appendChild(char);
    }
  });
});
