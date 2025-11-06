document.addEventListener("DOMContentLoaded", (e) => {
  /**@type{HTMLDivElement} */
  const ring = document.querySelector(".ring");
  let rot = 0;
  let speed = 0;
  const maxspeed = 10;
  let acc = 1;
  const friction = 0.25;

  let scale = 1;

  window.addEventListener("wheel", (e) => {
    if (e.deltaY > 0) {
      speed = Math.min(speed + acc, maxspeed);
    } else {
      speed = Math.max(speed - acc, -maxspeed);
    }
  });

  const animate = () => {
    requestAnimationFrame(animate);
    rot = (rot + speed) % 360;

    if (speed > 0) {
      speed -= friction;
    } else if (speed < 0) {
      speed += friction;
    }

    scale = 1 + Math.abs(speed / maxspeed) * 0.3;

    ring.style.rotate = `${rot}deg`;
    ring.style.scale = scale;
  };
  animate();
});
