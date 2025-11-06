document.addEventListener("DOMContentLoaded", (e) => {
  /**@type {HTMLCanvasElement} */
  const drawing = document.querySelector("#drawing");
  const w = 512;
  const h = 512;
  drawing.width = w;
  drawing.height = h;
  /**@type {HTMLCanvasElement} */
  const ascii = document.querySelector("#ascii");
  ascii.width = w;
  ascii.height = h;
  const dc = drawing.getContext("2d");
  const ac = ascii.getContext("2d");

  const th = 18;
  ac.font = `${th}px monospace`;
  ac.textBaseline = "top";
  const tw = ac.measureText("@").width;

  let mousedown = false;
  drawing.addEventListener("mousedown", (e) => {
    mousedown = true;
  });
  drawing.addEventListener("mouseup", (e) => {
    mousedown = false;
  });
  drawing.addEventListener("mouseleave", (e) => {
    mousedown = false;
  });
  drawing.addEventListener("mousemove", (e) => {
    if (!mousedown) return;
    const x = e.offsetX;
    const y = e.offsetY;
    dc.fillStyle = "red";
    dc.beginPath();
    dc.arc(x, y, 10, 0, Math.PI * 2);
    dc.closePath();
    dc.fill();
    updateAscii();
  });
  drawing.addEventListener("click", (e) => {
    const x = e.offsetX;
    const y = e.offsetY;
    dc.fillStyle = "red";
    dc.beginPath();
    dc.arc(x, y, 10, 0, Math.PI * 2);
    dc.closePath();
    dc.fill();
    updateAscii();
  });

  const drawAt = (x, y) => {
    ac.fillStyle = "white";
    ac.textBaseline = "top";
    ac.fillText("@", x, y);
    const size = ac.measureText("@");
    ac.fill();
  };

  const updateAscii = () => {
    ac.clearRect(0, 0, w, h);
    for (let y = 0; y < h; y += th) {
      for (let x = 0; x < h; x += tw) {
        const color = dc.getImageData(x, y, 1, 1);

        if (color.data[3] > 0) {
          drawAt(x, y);
        }
      }
    }
  };
});
