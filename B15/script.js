document.addEventListener("DOMContentLoaded", (e) => {
  const canvas = document.querySelector("canvas");
  const c = canvas.getContext("2d");
  const inp = document.querySelector("input");
  const btn = document.querySelector("button");

  let max = -1;

  btn.addEventListener("click", (e) => {
    c.clearRect(0, 0, w, h);
    const p1 = [w / 2, 0];
    const p2 = [w, h];
    const p3 = [0, h];
    c.fillStyle = "black";
    c.beginPath();
    c.moveTo(...p1);
    c.lineTo(...p2);
    c.lineTo(...p3);
    c.closePath();
    c.fill();
    max = parseInt(inp.value) - 1;
    draw(p1, p2, p3);
  });

  const w = 600;
  const h = 600;

  canvas.width = w;
  canvas.height = h;

  const mp = (p1, p2) => {
    const [x1, y1] = p1;
    const [x2, y2] = p2;
    return [(x1 + x2) / 2, (y1 + y2) / 2];
  };

  const draw = (p1, p2, p3, i = 0) => {
    if (i > max) return;
    const m12 = mp(p1, p2);
    const m23 = mp(p2, p3);
    const m31 = mp(p3, p1);

    c.fillStyle = "white";
    c.beginPath();
    c.moveTo(...m12);
    c.lineTo(...m23);
    c.lineTo(...m31);
    c.closePath();
    c.fill();

    draw(p1, m12, m31, i + 1);
    draw(p2, m23, m12, i + 1);
    draw(p3, m31, m23, i + 1);
  };
  c.clearRect(0, 0, w, h);
  const p1 = [w / 2, 0];
  const p2 = [w, h];
  const p3 = [0, h];
  c.fillStyle = "black";
  c.beginPath();
  c.moveTo(...p1);
  c.lineTo(...p2);
  c.lineTo(...p3);
  c.closePath();
  c.fill();
  draw(p1, p2, p3);
});
