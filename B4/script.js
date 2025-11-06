document.addEventListener("DOMContentLoaded", (e) => {
  const canvas = document.querySelector("canvas");
  const c = canvas.getContext("2d");
  const w = window.innerWidth;
  const h = window.innerHeight;
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  let x, y;
  canvas.addEventListener("click", (e) => {
    x = e.clientX;
    y = e.clientY;
    console.log({ x, y });
    strike(x, y);
  });

  const r = () => {
    return -50 + Math.random() * 100;
  };

  const strike = (x, y) => {
    c.fillStyle = "rgb(20, 20, 20)";
    c.fillRect(0, 0, w, h);

    c.strokeStyle = "white";

    c.shadowColor = "white";
    c.shadowBlur = 20;
    c.shadowOffsetX = 0;
    c.shadowOffsetY = 0;
    for (let i = 0; i < 3; i++) {
      c.beginPath();
      c.lineWidth = Math.random() * 3;
      c.moveTo(x + r(), 0);
      _strike(x, 0);
      c.stroke();
    }
    setTimeout(() => {
      c.clearRect(0, 0, w, h);
    }, 150);
  };

  const _strike = (x, y, i = 0, max = Math.floor(10 + Math.random() * 40)) => {
    i++;
    if (i > max) return;
    const newx = x + r();
    const newy = y + Math.random() * 50;
    c.lineTo(newx, newy);
    _strike(newx, newy, i, max);
  };
});
