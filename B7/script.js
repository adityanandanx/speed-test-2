document.addEventListener("DOMContentLoaded", (e) => {
  const boxes = document.querySelectorAll(".box");
  let dragging = null;
  window.addEventListener("mousemove", (e) => {
    boxes.forEach((box) => {
      const rect = box.getBoundingClientRect();
      const cx = rect.x + rect.width / 2;
      const cy = rect.y + rect.height / 2;
      const dx = cx - e.clientX;
      const dy = cy - e.clientY;
      if (Math.abs(dx) < 100 && Math.abs(dy) < 100) {
        box.style.transform = `translate(${-dx / 10}px, ${-dy / 10}px)`;
        box.style.transition = `scale 700ms ease`;
      } else {
        box.style.transition = `transform 700ms ease`;
        box.style.transform = `translate(0, 0)`;
      }
    });

    if (dragging) {
      dragging.style.left = e.clientX - 35 + "px";
      dragging.style.top = e.clientY - 35 + "px";
    }
  });
  boxes.forEach((box) => {
    box.addEventListener("mousedown", (e) => {
      dragging = box;
    });
    box.addEventListener("mouseup", (e) => {
      dragging = null;
    });
  });
});
