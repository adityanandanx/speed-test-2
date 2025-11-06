document.addEventListener("DOMContentLoaded", (e) => {
  const video = document.querySelector("video");
  const embedVideo = document.querySelector("#embedVideo");
  /**@type {HTMLProgressElement} */
  const progress = document.querySelector("#progress");
  const playPause = document.querySelector("#play-pause");
  const duration = document.querySelector("#duration");

  function togglePlay() {
    // Play/Pause button
    if (video.paused || video.ended) {
      playPause.setAttribute("data-state", "play");
    } else {
      playPause.setAttribute("data-state", "pause");
    }
  }
  window.addEventListener("load", () => {
    console.log(video.duration);
    duration.textContent = `${formatSeconds(
      video.currentTime
    )} / ${formatSeconds(video.duration)}`;

    progress.setAttribute("max", video.duration);
  });
  video.addEventListener("loadedmetadata", () => {
    progress.setAttribute("max", video.duration);
    duration.textContent = `${formatSeconds(
      video.currentTime
    )} / ${formatSeconds(video.duration)}`;
  });
  video.addEventListener("timeupdate", () => {
    if (!progress.getAttribute("max"))
      progress.setAttribute("max", video.duration);
    progress.value = video.currentTime;
    duration.textContent = `${formatSeconds(
      video.currentTime
    )} / ${formatSeconds(video.duration)}`;
  });

  const formatSeconds = (secs) => {
    const min = secs / 60;
    const remsec = secs % 60;
    return `${Math.floor(min)}:${Math.floor(remsec)
      .toString()
      .padStart(2, "0")}`;
  };

  video.addEventListener("play", () => {
    togglePlay();
  });

  video.addEventListener("pause", () => {
    togglePlay();
  });

  progress.addEventListener("click", (e) => {
    if (!Number.isFinite(video.duration)) return;
    const rect = progress.getBoundingClientRect();
    const pos = (e.pageX - rect.left) / progress.offsetWidth;
    video.currentTime = pos * video.duration;
  });

  progress.addEventListener("mousemove", (e) => {
    if (!Number.isFinite(video.duration)) return;
    const rect = progress.getBoundingClientRect();
    const pos = (e.pageX - rect.left) / progress.offsetWidth;
    embedVideo.currentTime = pos * video.duration;
    embedVideo.style.left = pos * 100 + "%";
    // embedVideo.style.bottom =  + "px";
  });

  playPause.addEventListener("click", (e) => {
    if (video.paused || video.ended) {
      video.play();
    } else {
      video.pause();
    }
  });
});
