document.addEventListener("DOMContentLoaded", (e) => {
  /**@type {HTMLDivElement} */
  const container = document.querySelector(".container");
  container.addEventListener("keydown", (e) => {
    // console.log(e.key);

    if (e.key === "Enter" && !e.shiftKey) {
      createNote();
      e.preventDefault();
    } else if (e.key === "Enter" && e.shiftKey) {
      e.target.rows = Math.max(e.target.value.split("\n").length + 1, 1);
    } else if (e.key === "Backspace") {
      e.target.rows = Math.max(e.target.value.split("\n").length, 1);
    }
  });

  const createNote = () => {
    const textarea = document.createElement("textarea");
    textarea.rows = 1;
    textarea.placeholder = "Start Writing...";
    container.appendChild(textarea);
    textarea.focus();
  };
});
