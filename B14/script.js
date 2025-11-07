document.addEventListener("DOMContentLoaded", (e) => {
  /**@type {HTMLInputElement} */
  const slider = document.querySelector(".slider");
  /**@type{HTMLDivElement} */
  const container = document.querySelector(".container");

  container.addEventListener("click", (e) => {});

  slider.addEventListener("input", (e) => {
    updateProgress();
  });

  const updateProgress = () => {
    const p = slider.value;
    container.style.setProperty("--progress", `${p}%`);
  };

  const updateSlider = (e) => {
    const rect = container.getBoundingClientRect();
    const p = ((e.clientX - rect.left) / rect.width) * 100;
    slider.value = p;
    updateProgress();
  };

  let mousedown = false;
  container.addEventListener("mousedown", (e) => {
    mousedown = true;
    updateSlider(e);
  });
  container.addEventListener("mouseup", (e) => {
    mousedown = false;
  });
  container.addEventListener("mousemove", (e) => {
    if (mousedown) {
      updateSlider(e);
    }
  });
});
