document.addEventListener("DOMContentLoaded", (e) => {
  /** @type {HTMLElement} */
  let target;
  let css = {};
  const tooltip = document.querySelector(".tooltip");
  document.querySelectorAll("*").forEach((el) => {
    el.addEventListener("mouseenter", (e) => {
      tooltip.classList.add("hidden");
      if (el.classList.contains("tooltip")) return;
      if (target) target.classList.remove("hovered");
      target = e.target;
      target.classList.add("hovered");
    });
  });
  window.addEventListener("click", (e) => {
    if (!target) return;
    const styles = target.computedStyleMap();
    const toget = [
      "color",
      "background-color",
      "font-size",
      "width",
      "height",
      "margin",
      "padding",
    ];
    tooltip.innerHTML = "";
    tooltip.classList.remove("hidden");
    tooltip.style.left = target.getBoundingClientRect().left + "px";
    tooltip.style.top = target.getBoundingClientRect().bottom + "px";
    toget.forEach((prop) => {
      css[prop] = styles.get(prop).toString();
      tooltip.innerHTML += `
      <div>
        <span class="prop">${prop}:</span> <span class="">${css[prop]}</span> 
        </div>
      `;
    });
  });
});
